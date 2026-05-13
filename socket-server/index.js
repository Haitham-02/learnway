const { createServer } = require("http");
const { Server } = require("socket.io");
const Redis = require("ioredis");

const httpServer = createServer();
const io = new Server(httpServer, {
  cors: {
    origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
    methods: ["GET", "POST"],
    credentials: true
  }
});

const redisHost = process.env.REDIS_HOST || "localhost";
const redisPort = process.env.REDIS_PORT || 6379;

console.log(`Connecting to Redis at ${redisHost}:${redisPort}...`);

const redis = new Redis({
  host: redisHost,
  port: redisPort,
  retryStrategy: (times) => {
    const delay = Math.min(times * 50, 2000);
    return delay;
  },
  reconnectOnError: (err) => {
    const targetError = 'READONLY';
    if (err.message.includes(targetError)) {
      return true;
    }
    return false;
  }
});

redis.on('connect', () => {
  console.log(`✓ Connected to Redis at ${redisHost}:${redisPort}`);
});

redis.on('error', (err) => {
  console.error('✗ Redis connection error:', err.message);
});

// Subscribe to Redis channels for real-time updates
redis.subscribe(
  "chat-messages",           // Messages module
  "notifications",          // Notification updates
  "livestream-chat",         // Livestream chat
  "livestream-qa",           // Livestream Q&A
  "livestream-qa-answer",    // Livestream Q&A answers
  "livestream-ai-update",    // Livestream AI analytics updates
  (err, count) => {
    if (err) {
      console.error("Failed to subscribe: %s", err.message);
    } else {
      console.log(`✓ Subscribed successfully! This client is currently subscribed to ${count} channels.`);
    }
  }
);

redis.on("message", (channel, message) => {
  try {
    const data = JSON.parse(message);

    if (channel === "chat-messages") {
      // Handle regular message module chat
      console.log(`📨 Broadcasting message to room ${data.room}`);
      io.to(data.room).emit("new-message", data.html);
    }
    else if (channel === "notifications") {
      console.log(`🔔 Broadcasting notification refresh to room ${data.room}`);
      io.to(data.room).emit("notification-refresh", data);
    }
    else if (channel === "livestream-chat") {
      // Handle livestream chat
      console.log(`💬 Broadcasting livestream chat to room ${data.room}`);
      io.to(data.room).emit("livestream-chat", data);
    } 
    else if (channel === "livestream-qa") {
      // Handle livestream Q&A questions
      console.log(`❓ Broadcasting livestream Q&A to room ${data.room}`);
      io.to(data.room).emit("livestream-qa", data);
    }
    else if (channel === "livestream-qa-answer") {
      // Handle livestream Q&A answers
      console.log(`✅ Broadcasting livestream Q&A answer to room ${data.room}`);
      io.to(data.room).emit("livestream-qa-answer", data);
    }
    else if (channel === "livestream-ai-update") {
      // Handle AI analytics updates
      console.log(`🤖 [AI] Received update from Redis for room ${data.room}`);
      console.log(`   - Student: ${data.studentName} (${data.studentId})`);
      console.log(`   - Emotion: ${data.emotion}, Score: ${data.score}%`);
      io.to(data.room).emit("livestream-ai-update", data);
      console.log(`   - Broadcasted to room: ${data.room}`);
    }
  } catch (e) {
    console.error(`Error parsing message from ${channel}:`, e);
  }
});

io.on("connection", (socket) => {
  console.log("👤 A user connected:", socket.id);

  socket.on("join-room", (room) => {
    console.log(`🚪 Socket ${socket.id} joined room: ${room}`);
    socket.join(room);
  });

  socket.on("disconnect", () => {
    console.log("👤 User disconnected:", socket.id);
  });
});

const PORT = process.env.PORT || 3001;
httpServer.listen(PORT, () => {
  console.log(`✓ Socket.io server running on port ${PORT}`);
});
