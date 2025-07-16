<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
</head>
<body>
    <form action="/api/login" method="POST">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html> 