<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    
    <?php if(!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach($errors as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/register">
        <input type="hidden" name="csrf_token" value="<?= \App\core\Security::csrfToken() ?>">
        
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p>Already have an account? <a href="/login">Login</a></p>
</body>
</html>
