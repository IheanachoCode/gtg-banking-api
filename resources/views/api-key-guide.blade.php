<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Key Management - GTG API</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .section h3 {
            color: #34495e;
            font-size: 1.4rem;
            margin: 25px 0 15px 0;
        }
        
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 15px 0;
            overflow-x: auto;
        }
        
        .code-block pre {
            margin: 0;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .code-block code {
            color: #e83e8c;
        }
        
        .example {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 6px 6px 0;
        }
        
        .example h4 {
            color: #2980b9;
            margin-bottom: 10px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .nav-links {
            background: white;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
        }
        
        .nav-links a {
            color: #3498db;
            text-decoration: none;
            margin-right: 20px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: #e3f2fd;
        }
        
        .nav-links a.active {
            background: #3498db;
            color: white;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .output-example {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>🔑 API Key Management</h1>
            <p>Learn how to generate and manage API keys for the GTG API</p>
        </div>
    </div>
    
    <div class="nav-links">
        <div class="container">
            <a href="/docs/api">📚 API Documentation</a>
            <a href="/api-key-guide" class="active">🔑 API Key Guide</a>
            <a href="/">🏠 Home</a>
        </div>
    </div>
    
    <div class="container">
        <div class="content">
            <div class="section">
                <h2>Quick Start</h2>
                <p>Generate your first API key with a simple command:</p>
                <div class="code-block">
                    <pre><code>php artisan api:key:generate "MyApp" --description="My application API key"</code></pre>
                </div>
            </div>
            
            <div class="section">
                <h2>Command Syntax</h2>
                <div class="code-block">
                    <pre><code>php artisan api:key:generate {name} {--expires=} {--description=}</code></pre>
                </div>
                
                <h3>Parameters</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Required</th>
                            <th>Description</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>name</code></td>
                            <td>✅ Yes</td>
                            <td>Descriptive name for the API key</td>
                            <td><code>"MobileApp"</code></td>
                        </tr>
                        <tr>
                            <td><code>--expires</code></td>
                            <td>❌ No</td>
                            <td>Days until expiration (default: never)</td>
                            <td><code>--expires=30</code></td>
                        </tr>
                        <tr>
                            <td><code>--description</code></td>
                            <td>❌ No</td>
                            <td>Additional description</td>
                            <td><code>--description="Production key"</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h2>Examples</h2>
                
                <div class="example">
                    <h4>Basic Key Generation</h4>
                    <div class="code-block">
                        <pre><code>php artisan api:key:generate "TestKey"</code></pre>
                    </div>
                </div>
                
                <div class="example">
                    <h4>Key with Expiration</h4>
                    <div class="code-block">
                        <pre><code>php artisan api:key:generate "TemporaryKey" --expires=7 --description="7-day temporary access"</code></pre>
                    </div>
                </div>
                
                <div class="example">
                    <h4>Production Key</h4>
                    <div class="code-block">
                        <pre><code>php artisan api:key:generate "ProductionKey" --description="Production environment API key"</code></pre>
                    </div>
                </div>
                
                <div class="example">
                    <h4>Your Specific Examples</h4>
                    <div class="code-block">
                        <pre><code>php artisan api:key:generate "Developer1" --expires=30 --description="Mobile App Developer"
php artisan api:key:generate "Developer2" --expires=30 --description="Web Developer"</code></pre>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>Output Format</h2>
                <div class="output-example">
API key generated successfully!
+-------------+----------------------------------+----------+
| Name        | Key                             | Expires  |
+-------------+----------------------------------+----------+
| Developer1  | abc123def456ghi789jkl012mno345... | 2024-02-15 |
+-------------+----------------------------------+----------+
                </div>
            </div>
            
            <div class="section">
                <h2>Using Your API Key</h2>
                
                <h3>cURL Example</h3>
                <div class="code-block">
                    <pre><code>curl -H "x-api-key: your-generated-key-here" \
     -H "Content-Type: application/json" \
     https://api.gtg.com/v1/login</code></pre>
                </div>
                
                <h3>JavaScript Example</h3>
                <div class="code-block">
                    <pre><code>fetch('https://api.gtg.com/v1/login', {
  method: 'POST',
  headers: {
    'x-api-key': 'your-generated-key-here',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
})</code></pre>
                </div>
                
                <h3>PHP Example</h3>
                <div class="code-block">
                    <pre><code>$response = Http::withHeaders([
    'x-api-key' => 'your-generated-key-here'
])->post('https://api.gtg.com/v1/login', [
    'email' => 'user@example.com',
    'password' => 'password'
]);</code></pre>
                </div>
            </div>
            
            <div class="section">
                <h2>Security Best Practices</h2>
                <div class="alert alert-info">
                    <strong>💡 Tip:</strong> Follow these guidelines to keep your API keys secure.
                </div>
                
                <ul style="margin-left: 20px; line-height: 2;">
                    <li><strong>Store keys securely:</strong> Never commit API keys to version control</li>
                    <li><strong>Use environment variables:</strong> Store keys in <code>.env</code> files</li>
                    <li><strong>Rotate keys regularly:</strong> Generate new keys and deprecate old ones</li>
                    <li><strong>Use descriptive names:</strong> Make it easy to identify key purposes</li>
                    <li><strong>Set expiration dates:</strong> Use the <code>--expires</code> parameter for temporary access</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>Managing Multiple Keys</h2>
                <p>You can generate multiple keys for different purposes:</p>
                <div class="code-block">
                    <pre><code># Development
php artisan api:key:generate "DevKey" --expires=30 --description="Development environment"

# Testing
php artisan api:key:generate "TestKey" --expires=7 --description="Testing environment"

# Production
php artisan api:key:generate "ProdKey" --description="Production environment"

# Mobile App
php artisan api:key:generate "MobileKey" --description="Mobile application"

# Web App
php artisan api:key:generate "WebKey" --description="Web application"</code></pre>
                </div>
            </div>
            
            <div class="section">
                <h2>Troubleshooting</h2>
                
                <h3>Key Not Working?</h3>
                <ul style="margin-left: 20px; line-height: 2;">
                    <li>Check if the key is expired</li>
                    <li>Verify the key is copied correctly (no extra spaces)</li>
                    <li>Ensure the <code>x-api-key</code> header is included</li>
                    <li>Check if the key exists in the database</li>
                </ul>
                
                <h3>Command Not Found?</h3>
                <ul style="margin-left: 20px; line-height: 2;">
                    <li>Ensure you're in the project directory</li>
                    <li>Run <code>composer install</code> if needed</li>
                    <li>Check if the command is registered in <code>app/Console/Commands/</code></li>
                </ul>
                
                <h3>Database Issues?</h3>
                <ul style="margin-left: 20px; line-height: 2;">
                    <li>Run <code>php artisan migrate</code> to ensure the <code>api_keys</code> table exists</li>
                    <li>Check database connection in <code>.env</code></li>
                    <li>Verify the <code>ApiKey</code> model exists</li>
                </ul>
            </div>
            
            <div class="section">
                <h2>Need Help?</h2>
                <div class="alert alert-success">
                    <ul style="margin-left: 20px; line-height: 2;">
                        <li>Check the full API documentation: <a href="/docs/api">📚 API Documentation</a></li>
                        <li>View interactive docs: <a href="/docs/api">🔍 Interactive Documentation</a></li>
                        <li>Contact support: <a href="mailto:support@gtg.com">📧 support@gtg.com</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 