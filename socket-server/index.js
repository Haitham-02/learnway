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

redis.subscribe("chat-messages", (err, count) => {
  if (err) {
    console.error("Failed to subscribe: %s", err.message);
  } else {
    console.log(`✓ Subscribed successfully! This client is currently subscribed to ${count} channels.`);
  }
});

redis.on("message", (channel, message) => {
  if (channel === "chat-messages") {
    try {
      const data = JSON.parse(message);
      console.log(`📨 Broadcasting to room ${data.room}`);
      io.to(data.room).emit("new-message", data.html);
    } catch (e) {
      console.error("Error parsing message:", e);
    }
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
