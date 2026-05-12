# LearnWay: Comprehensive Project Documentation

## 🌟 Executive Summary
LearnWay is a premium, all-in-one Educational Management System (EMS) and virtual learning platform. It is designed to modernize the interaction between students, teachers, and administrators by integrating robust academic management with cutting-edge AI assistance, real-time communication, and virtual classroom monitoring.

---

## 🏗️ Technical Architecture & Stack

### Core Technologies
- **Backend Framework**: [Symfony 6.4 (PHP 8.1+)](https://symfony.com/)
- **Database Engine**: MySQL 8.0 (Relational data) + Qdrant (Vector search)
- **Real-time Engine**: Node.js + Socket.io (for messaging and notifications)
- **Caching & Session**: Redis (via Predis)
- **AI Integration**: Google Gemini Pro (LLM) + RAG (Retrieval-Augmented Generation)

### Frontend Architecture
- **Templating**: Twig (Server-side rendering)
- **Reactivity**: [Hotwire Stack](https://hotwired.dev/) (Turbo for SPA-like navigation, Stimulus for component logic)
- **Design System**: Vanilla CSS following **Material Design 3 (M3)** aesthetics (Custom implementation).
- **Data Visualization**: Chart.js for student progress and engagement analytics.

---

## 📂 System Modules & Features

### 1. User & Access Management
- **Role-Based Control**: Multi-tier access for Admins, Teachers, and Students.
- **Dynamic Enrollment**: Automatic class assignment based on academic years and terms.
- **Profile Customization**: Personal settings, profile pictures, and contact management.

### 2. Academic Infrastructure
- **Curriculum Organization**: Subjects -> Chapters -> Lessons (Chapters) -> Content/Files.
- **Smart Scheduling**: Conflict-preventing timetable management with specific time slots (90min/120min).
- **Progress Tracking**: Real-time monitoring of lesson completion and assignment status.

### 3. Virtual Learning (Livestream)
- **Integrated Video Conferencing**: Real-time streaming for virtual classes.
- **Engagement Monitoring**: **Facial Analysis** service to track student mood and confidence levels during live sessions.
- **Live Q&A**: Dedicated channel for students to ask questions, with moderated teacher responses.

### 4. Community & Collaboration
- **Global Forum**: Threaded discussions with moderation workflows, ratings, and attachments.
- **Messaging System**: Private and group chats with read receipts and real-time delivery status via WebSockets.
- **Announcements**: Multi-tier notification system (Platform-wide or Class-specific).

### 5. LearnWay Copilot (AI Assistant)
- **Role-Adaptive Intelligence**: The Copilot functions differently depending on the authenticated user's role:
    - **Students**: Focuses on lesson summaries, assignment deadlines, and personal grade tracking.
    - **Teachers**: Assists with grading workflows, monitoring student engagement, and managing class-specific Q&A.
    - **Admins**: Provides platform-wide analytics, user management shortcuts, and forum moderation support.
- **Role-Aware Persistence**: Remembers conversation history and UI state across page reloads using `localStorage`.
- **Smart Redirection**: AI can suggest and take users to relevant pages (Schedule, Forum, etc.) with user consent.
- **Contextual Intelligence**: Retrieves real-time data about the user's specific role, class schedule, and recent activity to provide precise support.
- **Actionable AI**: Capability to redirect users to relevant platform pages or summarize complex course materials.

---

## 📊 Database Schema Overview (`learnway_web`)

The database is architected with strict relational integrity, featuring ~30 interconnected tables.

### Key Table Groups:
- **Identity**: `users`, `roles`, `student_enrollments`, `teacher_assignments`.
- **Academic**: `academic_years`, `terms`, `classes`, `subjects`, `chapters`, `class_schedules`.
- **Engagement**: `forum_posts`, `forum_comments`, `forum_reviews`, `conversations`, `messages`.
- **Interactive**: `livestreams`, `livestream_qa`, `facial_analysis`.
- **AI Infrastructure**: `ai_chats`, `ai_messages`, `ai_knowledge_base`.

---

## 📁 Project Directory Map
- `/src`: Controllers, Entities, Repositories, and core AI/Authorization services.
- `/templates`: Categorized Twig views (Admin, Student, Teacher, etc.).
- `/assets`: Stimulus controllers and modern CSS components.
- `/socket-server`: The Node.js real-time communication microservice.
- `/public/uploads`: Storage for user profiles, assignment files, and forum attachments.

---

## 🏁 Summary
LearnWay stands out by bridging the gap between static management systems and dynamic learning environments. By leveraging **Gemini AI** for assistance and **Facial Analysis** for engagement tracking, it provides educators with unprecedented insights into student success while offering students a highly personalized and interactive dashboard. The use of **Hotwire** ensures the platform remains lightning-fast and maintainable, adhering to modern web development best practices.
