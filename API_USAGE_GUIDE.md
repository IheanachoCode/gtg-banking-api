# API Usage Guide

## Quick Start

### 1. Authentication Setup

#### Get API Key
```bash
php artisan make:api-key
```

#### Login to get Bearer Token
```bash
POST /api/v1/login
Headers:
  x-api-key: your-api-key
  Content-Type: application/json

Body:
{
  "userID": "user123",
  "password": "password123"
}
```

#### Response:
```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "token": "1|abc123token...",
    "token_type": "Bearer",
    "expires_in": 86400,
    "user": {
      "id": 1,
      "user_id": "user123",
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

### 2. Making Authenticated Requests

```bash
POST /api/v1/account/balance
Headers:
  x-api-key: your-api-key
  Authorization: Bearer 1|abc123token...
  Content-Type: application/json

Body:
{
  "userID": "user123"
}
```

## Core API Endpoints

### User Management
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout
- `POST /forgot-password` - Password reset

### Account Operations
- `POST /fetch-account-name` - Get account name
- `POST /account/balance` - Get account balance
- `POST /account/statement` - Get account statement
- `POST /transfer` - Transfer funds

### Transactions
- `GET /transactions/recent` - Recent transactions
- `POST /transactions/dates` - Transactions by date range
- `POST /bill-payment` - Pay utility bills

### Products & Services
- `GET /products` - Available products
- `POST /orders` - Place product order
- `POST /insurance/view` - View insurance policies

## Error Handling

### Common Error Responses

#### 401 Unauthorized
```json
{
  "status": false,
  "message": "Invalid credentials",
  "data": null
}
```

#### 422 Validation Error
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "userID": ["The userID field is required."],
    "password": ["The password field is required."]
  }
}
```

#### 500 Server Error
```json
{
  "status": false,
  "message": "An error occurred",
  "data": null
}
```

## Rate Limiting

- **Public endpoints**: 60 requests per minute
- **Authenticated endpoints**: Higher limits based on user type

## Security Best Practices

1. **Always use HTTPS** in production
2. **Store API keys securely** - Never commit to version control
3. **Implement token refresh** - Tokens expire after 24 hours
4. **Validate all inputs** - API includes comprehensive validation
5. **Handle errors gracefully** - Always check response status

## Example Code

### JavaScript/Node.js
```javascript
const axios = require('axios');

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'x-api-key': 'your-api-key',
    'Content-Type': 'application/json'
  }
});

// Login
const login = async (userID, password) => {
  try {
    const response = await api.post('/login', { userID, password });
    const token = response.data.data.token;
    
    // Set token for future requests
    api.defaults.headers.Authorization = `Bearer ${token}`;
    
    return response.data;
  } catch (error) {
    console.error('Login failed:', error.response.data);
  }
};

// Get account balance
const getBalance = async (userID) => {
  try {
    const response = await api.post('/account/balance', { userID });
    return response.data;
  } catch (error) {
    console.error('Balance fetch failed:', error.response.data);
  }
};
```

### PHP/Laravel
```php
use Illuminate\Support\Facades\Http;

class GTGApiClient
{
    private $baseUrl;
    private $apiKey;
    private $token;

    public function __construct($baseUrl, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function login($userID, $password)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/login', [
            'userID' => $userID,
            'password' => $password
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->token = $data['data']['token'];
            return $data;
        }

        throw new Exception('Login failed');
    }

    public function getAccountBalance($userID)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/account/balance', [
            'userID' => $userID
        ]);

        return $response->json();
    }
}
```

## Testing

### Using Postman
1. Import the API collection (if available)
2. Set up environment variables:
   - `base_url`: http://localhost:8000/api/v1
   - `api_key`: your-api-key
   - `bearer_token`: (set after login)

### Using cURL
```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "x-api-key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{"userID": "user123", "password": "password123"}'

# Get balance (after login)
curl -X POST http://localhost:8000/api/v1/account/balance \
  -H "x-api-key: your-api-key" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"userID": "user123"}'
```

## Support

For API support and questions:
- Email: philipchibuike1@gmail.com
- Documentation: Available at `/api/documentation` when running the application
