<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <?php if(isset($_SESSION['login'])):?>
        <h2>Home</h2>
        <form action="logout.php" method="post">
            <button>Odhlasit se</button>
        </form>
    <?php else:; ?>
        <h1>jste zde jako neprihlaseny uzivatel</h1>
        <form action="login.php" method="post">
            <button>prihlasit se</button>
        </form>
    <?php endif;?>
</body>
</html>

