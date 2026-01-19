<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    
    <?php if(!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach($errors as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/login">
        <input type="hidden" name="csrf_token" value="<?= \App\core\Security::csrfToken() ?>">
        
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
