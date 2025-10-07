<?php

use controllers\LoginController;
use model\UserModel;

global $conn;
include("../dbconfig.php");
include("../controllers/LoginController.php");
$message = "";

session_start();
$UserModel = new UserModel($conn);
$loginController = new LoginController($UserModel);

if(isset($_POST['log-but'])){
    $login = $_POST["login"];
    $password = $_POST["password"];
    $message = $loginController->login($login, $password);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
<div class="form-container">
    <h2>Přihlášení</h2>

    <form action="login.php" method="post">
        <label for="login">Jméno</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Heslo</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" name="log-but" value="Přihlásit se">
    </form>

    <form action="home.php" method="post">
        <button type="submit">Pokračovat nepřihlášený</button>
    </form>

    <form action="register.php" method="post">
        <button type="submit">Registrovat se</button>
    </form>
    <?php if(!empty($message)) : ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>
</div>
</body>
</html>


