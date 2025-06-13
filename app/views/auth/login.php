<h1>Login</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $fieldErrors): ?>
                <?php foreach ($fieldErrors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/login" method="POST">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
    
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $old['email'] ?? '' ?>" required>
    </div>
    
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    
    <button type="submit">Login</button>
</form>
