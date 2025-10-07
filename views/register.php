<?php
use controllers\RegisterController;
use model\UserModel;

global $conn;
include("../dbconfig.php");
include("../controllers/registerController.php");
$message = "";

session_start();
$userModel = new UserModel($conn);
$registerController = new RegisterController($userModel);

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password-reg'], PASSWORD_DEFAULT);

    $message = $registerController->register($name, $surname, $login, $email, $password);

}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
    <link rel="stylesheet" href="styles/register.css">
</head>
<body>
<div class="form-container">
    <h1>Registrace</h1>
    <form action="register.php" method="post">
        <label>Jméno</label>
        <input type="text" name="name" required>

        <label>Příjmení</label>
        <input type="text" name="surname" required>

        <label>Přihlašovací jméno</label>
        <input type="text" name="login" required>

        <label>Heslo</label>
        <input type="password" name="password-reg" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <input type="submit" name="register" value="Registrovat">
    </form>

    <form action="login.php" method="post">
        <button>Přihlásit se</button>
    </form>

    <?php if(!empty($message)) : ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
</div>
</body>
</html>

