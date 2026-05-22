<?php
require_once __DIR__ . '/../php/app.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (checkAdminCredentials($username, $password)) {
        header('Location: admin.php');
        exit();
    } else {
        $error = "Identifiants invalides.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastOrder!</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <nav>
            <div>
                <p>FastOrder</p>
            </div>
            <div>
                <a href="../index.php">Menu</a>
                <a href="admin.php">Admin</a>
            </div>
            <div>
                <a href="panier.php"><img src="../img/panier.svg" alt="Panier"></a>
            </div>
        </nav>
    </header>
    <main>
        <div class="login-container">
            <h1>Admin Login</h1>

            <?php if (isset($error)): ?>
                <div class="login-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" class="login-form">
                <div>
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div>
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Se connecter</button>
            </form>
        </div>
    </main>
    <footer>
        <div>
            <div>
                <a href="">Politique de confidentialté</a>
                <span>•</span>
                <a href="">Mentions légales</a>
            </div>
            <div>
                &copy; 2026 FastOrder - Tous droits réservés
            </div>
        </div>
    </footer>
</body>

</html>