# GTG API Documentation

A comprehensive banking and financial services API built with Laravel, providing authentication, account management, transactions, and more.

## 🚀 Features

- **Authentication & Authorization**: Secure user authentication with Sanctum tokens
- **Account Management**: Complete account operations (balance, transfers, statements)
- **Transaction Processing**: Real-time transaction handling and history
- **Product Management**: Product catalog and ordering system
- **Staff Operations**: Staff-specific functionalities and reporting
- **API Documentation**: Beautiful, interactive documentation with Scramble
- **Rate Limiting**: Built-in rate limiting for API protection
- **API Key Management**: Secure API key generation and management

## 📋 Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL 8.0 or higher
- Composer

## 🛠 Installation

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

5. **Generate API keys** (see API Key Management section below)

6. **Start the server**
   ```bash
   php artisan serve
   ```

## 🔑 API Key Management

### Generating API Keys

The GTG API uses API keys for authentication. You can generate new API keys using the artisan command:

```bash
php artisan api:key:generate "Developer1" --expires=30 --description="Mobile App Developer"
php artisan api:key:generate "Developer2" --expires=30 --description="Web Developer"
```

#### Command Parameters

- **`name`** (required): A descriptive name for the API key
- **`--expires`** (optional): Number of days until the key expires (default: never expires)
- **`--description`** (optional): Additional description for the key

#### Examples

```bash
# Generate a key that never expires
php artisan api:key:generate "Production-Key" --description="Production environment"

# Generate a key that expires in 90 days
php artisan api:key:generate "Temporary-Key" --expires=90 --description="Temporary access for testing"

# Generate a key with minimal parameters
php artisan api:key:generate "Test-Key"
```

#### Output Example

```
API key generated successfully!
+-------------+----------------------------------+----------+
| Name        | Key                             | Expires  |
+-------------+----------------------------------+----------+
| Developer1  | abc123def456ghi789jkl012mno345... | 2024-02-15 |
+-------------+----------------------------------+----------+
```

### Using API Keys

Include the generated API key in your requests using the `x-api-key` header:

```bash
curl -H "x-api-key: your-api-key-here" \
     -H "Content-Type: application/json" \
     https://api.gtg.com/v1/endpoint
```

### Managing API Keys

- API keys are stored in the `api_keys` table
- Each key has a unique identifier and can be tracked
- Expired keys are automatically invalidated
- You can view all keys in the database or create an admin interface

## 📚 API Documentation

### Interactive Documentation

Access the beautiful, interactive API documentation at:

```
http://your-domain.com/docs
```

The documentation is generated using [Scramble](https://scramble.dedoc.co/) and includes:

- Complete endpoint documentation
- Request/response examples
- Authentication requirements
- Error codes and messages
- Try-it-out functionality

### API Base URL

```
https://api.gtg.com/v1
```

### Authentication

The API supports two authentication methods:

1. **API Key Authentication** (required for all endpoints)
   ```
   x-api-key: your-api-key-here
   ```

2. **Bearer Token Authentication** (required for protected endpoints)
   ```
   Authorization: Bearer your-token-here
   ```

## 🔐 Security Features

- **API Key Validation**: All requests require valid API keys
- **Rate Limiting**: Built-in rate limiting (60 requests per minute)
- **Token Expiration**: Sanctum tokens expire after 24 hours
- **Input Validation**: Comprehensive request validation
- **SQL Injection Protection**: Laravel's built-in protection
- **XSS Protection**: Automatic XSS protection

## 📊 API Endpoints Overview

### Public Endpoints (API Key Only)
- `POST /v1/login` - User authentication
- `POST /v1/register` - User registration
- `GET /v1/vendors` - Get all vendors
- `GET /v1/meter-types` - Get meter types
- `GET /v1/fetch-desco` - Get DESCO providers

### Protected Endpoints (API Key + Bearer Token)
- `GET /v1/user` - Get current user
- `POST /v1/logout` - User logout
- `GET /v1/account/balance` - Get account balance
- `POST /v1/account/transfer` - Transfer funds
- `GET /v1/transactions/history` - Get transaction history
- And many more...

## 🧪 Testing

### Running Tests

```bash
php artisan test
```

### API Testing

You can test the API using the interactive documentation at `/docs` or use tools like:

- Postman
- Insomnia
- curl
- Any HTTP client

## 🚀 Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   # Set production environment
   APP_ENV=production
   APP_DEBUG=false
   
   # Configure database
   DB_HOST=your-db-host
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-user
   DB_PASSWORD=your-db-password
   ```

2. **Optimize for Production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

3. **Generate Production API Keys**
   ```bash
   php artisan api:key:generate "Production-API" --description="Production environment API key"
   ```

## 📝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:

- **Email**: support@gtg.com
- **Documentation**: `/docs` (when running locally)
- **Issues**: Create an issue in the repository

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Complete API implementation
- Scramble documentation integration
- API key management system
- Comprehensive authentication and authorization
