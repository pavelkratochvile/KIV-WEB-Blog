<?php
use controllers\UserController;
use model\UserModel;

global $conn;
include("../dbconfig.php");
include("../controllers/UserController.php");

session_start();
$userModel = new UserModel($conn);
$userController = new UserController($userModel);

$roles = [
    1 => 'Uživatel',
    2 => 'Recenzent',
    3 => 'Admin',
    4 => 'Superadmin'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['roles'])) {
    foreach ($_POST['roles'] as $user_id => $role_id) {
        $userController->updateRoles((int)$user_id, (int)$role_id);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$users = $userController->getAllUsers();

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/admin-users.css?v=1.2">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="admin-users.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="admin-reviews.php">Správa recenzí</a></li>
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

<h2 class="users-title">Správa uživatelů</h2>

<form method="post" action="" class="user-management-form"">
    <table class="user-table">
        <thead class="user-table-head">
        <tr>
            <th class="user-table-id">ID</th>
            <th class="user-table-login">Login</th>
            <th class="user-table-name">Jméno</th>
            <th class="user-table-role">Role</th>
        </tr>
        </thead>
        <tbody class="user-table-body">
        <?php foreach ($users as $u): ?>
            <tr class="user-row">
                <td class="user-id"><?= htmlspecialchars($u['user_id']) ?></td>
                <td class="user-login"><?= htmlspecialchars($u['login']) ?></td>
                <td class="user-name"><?= htmlspecialchars($u['name'] . ' ' . $u['surname']) ?></td>
                <td class="user-role-cell">
                    <?php
                    $isLocked = ($_SESSION['userRole'] == 3 && ($u['role_id'] == 3 || $u['role_id'] == 4))
                        || ($u['user_id'] == $_SESSION['userId']);
                    ?>
                    <select
                            name="<?= $isLocked ? '' : 'roles[' . $u['user_id'] . ']' ?>"
                            class="role-select <?= $isLocked ? 'role-select-locked' : '' ?>"
                        <?= $isLocked ? 'disabled' : '' ?>
                    >
                        <?php foreach ($roles as $id => $roleName): ?>
                            <?php

                            if ($id == 4 && $u['role_id'] != 4) continue;
                            ?>
                            <option value="<?= $id ?>" <?= $id == $u['role_id'] ? 'selected' : '' ?>>
                                <?= $roleName ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-actions">
        <button type="submit" class="btn save-btn">💾 Uložit změny</button>
    </div>
</form>
</body>

