# GTG Banking API

A comprehensive REST API for banking and financial services built with Laravel 12.x. This project demonstrates enterprise-level architecture with robust security, transaction processing, and user management capabilities.

## 🚀 Features

### Core Banking Operations
- **User Authentication** - Secure login/registration with Laravel Sanctum
- **Account Management** - Balance inquiries, account details, statements
- **Transaction Processing** - Real-time transfers, deposits, withdrawals
- **Bill Payments** - Utility bill payment integration
- **Statement Generation** - PDF statements with transaction history

### Advanced Features
- **Staff Management** - Separate staff authentication and operations
- **Product Catalog** - Complete product management system
- **Insurance Services** - Policy management and claims
- **Loan Management** - Loan applications and tracking
- **Biometric Authentication** - Fingerprint validation support
- **Ticket System** - Customer support and feedback management

### Security  Performance
- **Dual Authentication** - API keys + Bearer tokens
- **Rate Limiting** - Protection against abuse (60 req/min)
- **Input Validation** - Comprehensive request validation
- **Error Handling** - Consistent error responses
- **Audit Logging** - Complete transaction audit trail

## 🛠 Technical Stack

- **Framework**: Laravel 12.x
- **PHP Version**: 8.2+
- **Authentication**: Laravel Sanctum
- **Database**: SQLite/MySQL
- **Documentation**: Scramble API docs
- **PDF Generation**: DomPDF
- **Permissions**: Spatie Laravel Permission
- **Testing**: PHPUnit

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/API/    # 45+ API controllers
│   ├── Models/                  # 35+ Eloquent models
│   ├── Services/                # 40+ business logic services
│   ├── Middleware/              # Custom security middleware
│   ├── Http/Requests/           # 50+ form validation classes
│   ├── Http/Resources/          # API response transformers
│   └── Traits/                  # Reusable traits
├── routes/api.php               # API route definitions
├── database/migrations/         # Database schema
└── tests/                       # PHPUnit tests
```

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd gtgapi
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Generate API keys**
   ```bash
   php artisan make:api-key
   ```

6. **Start the server**
   ```bash
   php artisan serve
   ```

## 📚 API Documentation

### Authentication
All endpoints require two layers of authentication:
1. **API Key** - Include `x-api-key` header
2. **Bearer Token** - Include `Authorization: Bearer {token}` header (for protected routes)

### Base URL
```
http://localhost:8000/api/v1/
```

### Key Endpoints

#### Authentication
- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout
- `POST /forgot-password` - Password reset

#### Account Operations
- `POST /fetch-account-name` - Get account name
- `POST /account/balance` - Get account balance
- `POST /account/statement` - Get account statement
- `POST /transfer` - Transfer funds
- `POST /withdrawal-request` - Request withdrawal

#### Transactions
- `GET /transactions/recent` - Recent transactions
- `POST /transactions/dates` - Transactions by date range
- `POST /bill-payment` - Pay utility bills
- `POST /transactions/count` - Transaction count

#### Products  Services
- `GET /products` - Available products
- `POST /orders` - Place product order
- `POST /insurance/view` - View insurance policies
- `POST /loans/history` - Loan history

### Response Format
All API responses follow this consistent format:
```json
{
  "status": true,
  "message": "Success message",
  "data": {...},
  "timestamp": "2025-01-16 18:30:00",
  "response_time": 145.32
}
```

## 🔐 Security Features

### Multi-layered Security
- **API Key Authentication** - First layer of security
- **Sanctum Token Authentication** - Second layer for user-specific actions
- **Rate Limiting** - Prevents API abuse
- **Input Validation** - Comprehensive request validation
- **CSRF Protection** - Cross-site request forgery protection

### Data Protection
- **Password Hashing** - Bcrypt password hashing
- **Token Expiration** - 24-hour token lifetime
- **Audit Logging** - Complete transaction logging
- **SQL Injection Prevention** - Eloquent ORM protection

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific tests:
```bash
php artisan test --filter=AuthTest
```

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure database connection
- [ ] Set up proper API keys
- [ ] Configure mail settings
- [ ] Set up SSL certificate
- [ ] Configure caching
- [ ] Set up monitoring

### Environment Variables
```env
APP_NAME="GTG Banking API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gtg_banking
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📊 Performance Features

- **Response Time Tracking** - Built-in performance monitoring
- **Database Optimization** - Efficient queries and indexing
- **Caching** - Redis/database caching support
- **Queue System** - Background job processing
- **Error Logging** - Comprehensive error tracking

## 🤝 Contributing

This is a project showcasing Laravel REST API development. The codebase demonstrates:

- **Clean Architecture** - Separation of concerns
- **Service Layer Pattern** - Business logic separation
- **Repository Pattern** - Data access abstraction
- **Request/Response Transformation** - API resources
- **Comprehensive Testing** - Unit and feature tests

## 📄 License

This project is a version of personal project.

## 👨‍💻 Developer

**Iheanacho Chibuike**
- Email: iheanachochukwubuikem@gmail.com
- LinkedIn: https://www.linkedin.com/in/philip-iheanacho-081589375/
- GitHub: https://github.com/IheanachoCode

## 🔍 Code Quality

This project demonstrates proficiency in:
- **Laravel Best Practices** - Following Laravel conventions
- **RESTful API Design** - Proper HTTP methods and status codes
- **Database Design** - Normalized database structure
- **Security Implementation** - Multi-layered security approach
- **Code Documentation** - Comprehensive inline documentation
- **Error Handling** - Graceful error management
- **Testing** - Automated testing practices

---

*This project showcases advanced Laravel development skills including complex business logic, security implementation, and scalable architecture suitable for enterprise applications.*
