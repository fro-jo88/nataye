# Nataye Smart Education System (NSES)

A comprehensive, production-ready school management system built with Laravel 10, MySQL, and modern web technologies.

## 🎯 Features

- **Multi-Role Support**: Admin, Teacher, Student, Parent, Staff
- **Student Management**: Complete student lifecycle with attendance, grades, and profiles
- **Parent-Student Linking**: Phone-first identity resolution with E.164 normalization
- **Attendance System**: Session-based attendance with bulk marking and locking
- **Exam Management**: Online/offline exams with auto-grading for MCQs
- **Grade Management**: Automated grade calculation and report generation
- **Messaging**: Internal messaging system between users
- **Notifications**: Multi-channel notifications (email, system, push)
- **Blog & Events**: School announcements and event management
- **Comprehensive Audit Logs**: All sensitive actions logged with PII redaction
- **File Management**: Secure file uploads with validation

## 🛠️ Tech Stack

- **Backend**: Laravel 10, PHP 8.1+
- **Database**: MySQL 8.x
- **Authentication**: Laravel Sanctum
- **Cache/Queue**: Redis
- **Phone Validation**: libphonenumber-for-php (E.164 normalization)
- **PDF Generation**: DomPDF
- **Excel Export**: Maatwebsite Excel
- **Containerization**: Docker (php-fpm, nginx, mysql, redis)

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Redis (for caching and queues)
- Node.js & NPM (for frontend assets)
- Docker & Docker Compose (optional, recommended)

## 🚀 Installation

### Option 1: Docker (Recommended)

1. **Clone the repository**
```bash
git clone <repository-url>
cd nataye
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Update .env with your database credentials**
```env
DB_DATABASE=nataye
DB_USERNAME=nataye_user
DB_PASSWORD=secure_password_here
```

4. **Build and start Docker containers**
```bash
docker-compose up -d
```

5. **Install dependencies**
```bash
docker-compose exec app composer install
```

6. **Generate application key**
```bash
docker-compose exec app php artisan key:generate
```

7. **Run migrations and seeders**
```bash
docker-compose exec app php artisan migrate --seed
```

8. **Access the application**
- Application: http://localhost:8000
- API Documentation: http://localhost:8000/api/documentation

### Option 2: Local Installation

1. **Clone and setup**
```bash
git clone <repository-url>
cd nataye
composer install
cp .env.example .env
```

2. **Create MySQL database**
```sql
CREATE DATABASE nataye CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nataye_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON nataye.* TO 'nataye_user'@'localhost';
FLUSH PRIVILEGES;
```

3. **Configure environment**
```bash
php artisan key:generate
```

Update `.env` with database credentials.

4. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

5. **Start services**
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Frontend assets (if using Vite/Mix)
npm install && npm run dev
```

## 🔐 Default Credentials

After seeding, use these credentials to login:

**Admin**
- Email: `admin@nataye.test`
- Password: `Admin123!`

**Teacher**
- Email: `john.smith@nataye.test`
- Password: `Teacher123!`

**Student**
- Email: `alice.brown1@student.nataye.test`
- Password: `Student123!`

**Parent**
- Email: `parent1@nataye.test`
- Password: `Parent123!`

## 📚 Database Schema

The system includes 28 database tables:

### Core Tables
- `users` - User accounts
- `roles` - User roles
- `permissions` - Permission system
- `role_permission` - Role-permission mapping

### Academic Tables
- `classes` - Grade levels
- `sections` - Class sections
- `subjects` - Academic subjects
- `students` - Student profiles
- `teachers` - Teacher profiles
- `parents` - Parent/guardian profiles
- `student_parent` - Student-parent relationships

### Attendance & Exams
- `attendance_sessions` - Attendance sessions
- `attendances` - Individual attendance records
- `exams` - Exam definitions
- `question_banks` - Exam questions
- `exam_attempts` - Student exam attempts
- `grades` - Grade records

### Communication
- `messages` - Internal messaging
- `notifications` - System notifications
- `blog_posts` - Blog/announcements
- `events` - School events

### System
- `files` - File uploads
- `audit_logs` - Audit trail
- `settings` - System settings
- `sessions` - User sessions

## 🔌 API Endpoints

### Authentication
```
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
```

### Students
```
GET    /api/v1/students
POST   /api/v1/students
GET    /api/v1/students/{id}
PUT    /api/v1/students/{id}
DELETE /api/v1/students/{id}
POST   /api/v1/students/{id}/link-parent
```

### Attendance
```
POST   /api/v1/attendance/sessions
POST   /api/v1/attendance/sessions/{id}/mark
POST   /api/v1/attendance/sessions/{id}/lock
GET    /api/v1/attendance/reports
```

### Exams
```
GET    /api/v1/exams
POST   /api/v1/exams
GET    /api/v1/exams/{id}
POST   /api/v1/exams/{id}/questions
POST   /api/v1/exams/{id}/attempt
POST   /api/v1/exams/{id}/submit
POST   /api/v1/exams/{id}/grade
```

All API responses follow this format:
```json
{
  "status": "success|error",
  "code": 200,
  "data": {},
  "errors": []
}
```

## 🧪 Testing

### Run all tests
```bash
php artisan test
```

### Run specific test suites
```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Run with coverage
```bash
php artisan test --coverage
```

### Static Analysis
```bash
vendor/bin/phpstan analyse
```

### Code Style
```bash
vendor/bin/pint
```

## 🔧 Configuration

### Phone Number Normalization
Set default country in `.env`:
```env
PHONE_DEFAULT_COUNTRY=US
PHONE_DEFAULT_REGION=US
```

### File Uploads
Configure in `.env`:
```env
MAX_UPLOAD_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx,xls,xlsx
```

### Security Settings
```env
FORCE_HTTPS=false
PASSWORD_MIN_LENGTH=8
PASSWORD_REQUIRE_UPPERCASE=true
PASSWORD_REQUIRE_NUMBER=true
PASSWORD_REQUIRE_SPECIAL=true
```

## 📊 Parent-Student Linking

The system supports multiple parent-student linking methods:

1. **Phone-based**: Auto-match by normalized E.164 phone number
2. **Email-based**: Match by email address
3. **Link Code**: Generate time-limited codes for manual linking
4. **Admin Override**: Manual linking with audit trail

## 🔒 Security Features

- Password hashing with bcrypt/argon2
- Rate limiting on authentication endpoints
- CSRF protection for session-based routes
- Input validation and output sanitization
- File upload validation (mime type, size)
- Comprehensive audit logging
- Role-based access control (RBAC)
- PII redaction in audit logs

## 🐳 Docker Services

- **app**: PHP-FPM application server
- **nginx**: Web server
- **mysql**: MySQL 8.0 database
- **redis**: Cache and queue backend
- **worker**: Laravel queue worker
- **scheduler**: Laravel task scheduler

## 📈 Performance Optimization

- **Redis caching**: For frequently accessed data
- **Queue workers**: For background jobs
- **Database indexing**: 20+ optimized indexes
- **OPcache**: PHP opcode caching
- **Eager loading**: Prevent N+1 queries

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🆘 Support

For issues, questions, or contributions, please open an issue in the GitHub repository.

## 🗺️ Roadmap

- [ ] Mobile app (Flutter/React Native)
- [ ] Advanced reporting and analytics
- [ ] Integration with external LMS
- [ ] Video conferencing integration
- [ ] Payment gateway integration
- [ ] Automated report card generation
- [ ] SMS notifications
- [ ] Multi-language support

## 👥 Credits

Developed with ❤️ for modern educational institutions.
