# 📋 Task Management System API

A RESTful API for task management with role-based access control, task dependencies, and fault-tolerant authentication.

---

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose installed
- Git

### Setup & Run

1. **Clone the repository**
```bash
git clone <https://github.com/MohamedYehia599/task_management_project>
cd task_management_project
```

2. **Start the application**
```bash
docker-compose --env-file ./backend/.env up -d
```

The API will be available at `http://localhost:8000`

**Note:** The `.env` file is included in the repository for easy installation and evaluation purposes.

### 👥 Test Users (Seeded)

The database seeder is **idempotent** (safe to run multiple times).

| Role | Email | Password |
|------|-------|----------|
| Manager | manager1@test.com | manager123 |
| Manager | manager2@test.com | manager123 |
| User | user1@test.com | user123 |
| User | user2@test.com | user123 |

**Additional users:** 96 more random users are seeded (mix of managers and regular users).

---

## 📊 Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────┐
│             users                    │
├─────────────────────────────────────┤
│ PK  id (bigint)                     │
│     name (varchar 255)              │
│     email (varchar 255) UNIQUE      │
│     password (varchar 255)          │
│     role (enum: manager, user)      │
│     created_at (timestamp)          │
│     updated_at (timestamp)          │
└──────────────┬──────────────────────┘
               │
               │ created_by (FK)
               │ assigned_to (FK)
               │
               ▼
┌─────────────────────────────────────┐
│             tasks                    │
├─────────────────────────────────────┤
│ PK  id (bigint)                     │
│     title (varchar 255)             │
│     description (text, nullable)    │
│     status (enum)                   │
│         - pending                   │
│         - completed                 │
│         - canceled                  │
│     due_date (date)                 │
│ FK  assigned_to → users.id          │
│ FK  created_by → users.id           │
│     created_at (timestamp)          │
│     updated_at (timestamp)          │
│                                     │
│ INDEXES:                            │
│ - (assigned_to, status, due_date)  │
│ - (status, due_date)                │
│ - (due_date)                        │
│ - (created_at)                      │
└──────────────┬──────────────────────┘
               │
               │ Many-to-Many
               │ (task dependencies)
               ▼
┌─────────────────────────────────────┐
│       task_dependencies              │
├─────────────────────────────────────┤
│ PK  id (bigint)                     │
│ FK  task_id → tasks.id              │
│ FK  depends_on_task_id → tasks.id   │
│     created_at (timestamp)          │
│     updated_at (timestamp)          │
│                                     │
│ CONSTRAINTS:                        │
│ - UNIQUE(task_id, depends_on_task_id)│
│ - ON DELETE RESTRICT (both FKs)     │
│                                     │
│ INDEXES:                            │
│ - (task_id, depends_on_task_id)    │
│ - (depends_on_task_id)              │
└─────────────────────────────────────┘
```

---

## 📚 API Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/login` | Login | ❌ |
| POST | `/api/auth/refresh` | Refresh tokens | ❌ |
| POST | `/api/auth/logout` | Logout | ✅ |

### Tasks

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/tasks` | List tasks | ✅ |
| POST | `/api/tasks` | Create task | ✅ | 
| GET | `/api/tasks/{id}` | Get task details | ✅ |
| PATCH | `/api/tasks/{id}` | Update task | ✅ | 
| PATCH | `/api/tasks/{id}/status` | Update status |
| POST | `/api/tasks/{id}/dependencies` | Add dependencies | ✅ |

### Filtering (List Tasks)

below are the allowed query parameters with proper validation

status&assigned_to=5&due_date_from=2026-01-01&due_date_to=2026-12-31&per_page=20

---

## 🔐 Authentication Architecture

### Custom JWT Implementation

This project implements a custom JWT authentication  using `firebase/php-jwt` rather than Laravel Sanctum.



**Why Custom JWT?**

**Sanctum Limitations:**
- Requires database query on every request (checks `personal_access_tokens` table)
- No built-in refresh token mechanism
- Stateful (tokens stored in database)

**Custom JWT Benefits:**
- **Stateless** - No database query per request
- **Fast** - JWT verified cryptographically (no I/O)
- **Scalable** - No database bottleneck
- **Flexible** - Separate access/refresh tokens with different TTLs
- **Secure** - Signed tokens with instant revocation via Redis

---

## ⚡ Redis Circuit Breaker: Fault Tolerance

### The Problem

Traditional approach: Backend depends on Redis. If Redis fails, the entire backend becomes unavailable.

### The Solution: Circuit Breaker Pattern

Implements a circuit breaker pattern that prevents cascading failures when Redis is unavailable. The system gracefully degrades to JWT-only validation when Redis is down, ensuring the application remains operational.

**Benefits:**

1. **Fault Tolerance**
   - Backend continues working when Redis is unavailable
   - Graceful degradation instead of complete failure

2. **Fast Failures**
   - Stops attempting failed operations after threshold
   - Returns immediately without waiting for timeouts

3. **Auto-Recovery**
   - Automatically retries and recovers when Redis becomes available
   - Self-healing system behavior



---

## 🗄️ Caching Strategy

The recursive dependency query (`getAllDependents`) could benefit from caching, but following the principle of **"Premature optimization is the root of all evil"** - *Donald Knuth*, caching was not implemented.

The architecture supports using Redis as a caching layer with proper caching strategies  if needed in the future. The Repository Pattern makes adding caching transparent to the rest of the application.

---

## 🏗️ Design Patterns & Best Practices

### Design Patterns Implemented

1. **Repository Pattern**
   - Abstracts data access logic
   - Enables easy switching between data sources (MySQL, Redis)

2. **Dependency Injection**
   - To separate creation from use
   - Provide Loose Coupling between Components

3. **Circuit Breaker Pattern**
   - Prevents cascading failures when external services fail
   - Implements graceful degradation

4. **Singleton Pattern**
   - Single shared instance of `RedisCircuitBreaker` and  `RedisClient` across the application
   - Consistent health state tracking



 project follows **Laravel best practices** , **Clean code and Solid principles**:


---

## 📦 Dependencies Used

**firebase/php-jwt** `^7.0`
- JWT token generation and validation
- Industry-standard cryptographic signing

---

