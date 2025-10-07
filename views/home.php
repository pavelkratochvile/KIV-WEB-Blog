<?php
session_start();
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="home.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="home.php">Home</a></li>
        <?php if(isset($_SESSION['login'])): ?>
            <li>
                <?php echo htmlspecialchars($_SESSION['name'] . " " . $_SESSION['surname']); ?>
            </li>
            <li>
                <form action="logout.php" method="post">
                    <button type="submit">Odhlásit se</button>
                </form>
            </li>
        <?php endif; ?>

    </ul>
</nav>

<main>
    <?php if(isset($_SESSION['login'])): ?>
        <h2>Home</h2>

        <?php if($_SESSION['userRole'] == 1): ?>
            <form action="articleadd.php" method="post" style="margin-bottom: 1rem;">
                <button type="submit">Přidat článek</button>
            </form>
        <?php endif; ?>

    <?php else: ?>
        <h1>Jste zde jako nepřihlášený uživatel</h1>
        <form action="login.php" method="post">
            <button type="submit">Přihlásit se</button>
        </form>
    <?php endif; ?>
</main>

</body>
</html>


