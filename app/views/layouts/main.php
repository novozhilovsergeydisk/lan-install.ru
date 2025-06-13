<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'LAN Install' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/login">Login</a></li>
                <li><a href="/register">Register</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <?php include $content; ?>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> LAN Install. All rights reserved.</p>
    </footer>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
