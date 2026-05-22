<?php
require_once 'php/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['produit_id'])) {
        $produit_id = $_POST['produit_id'];
        // Ajouter le produit au panier
        ajouterAuPanier($produit_id);
        $_SESSION['message'] = 'Produit ajouté au panier !';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastOrder!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<header>
    <nav>
        <div>
            <p>FastOrder</p>
        </div>
        <div>
            <a href="index.php">Menu</a>
            <a href="pages/login.php">Admin</a>
        </div>
        <div>
            <a href="pages/panier.php"><img src="img/panier.svg" alt="Panier"></a>
        </div>
    </nav>
</header>
<main>
    <h1>FastOrder</h1>
    <div>
        <p>Bienvenue sur FastOrder, votre service de commande en ligne.</p>
        <div class="produits">
            <?php
            $produits = selectAll();
            foreach ($produits as $produit) {
                echo '<div class="produit">';
                echo '<h2>' . $produit['nom'] . '</h2>';
                echo '<p>' . $produit['description'] . '</p>';
                echo '<p>Prix: ' . $produit['prix'] . ' CHF</p>';
                echo '<img src="' . $produit['image'] . '" alt="' . $produit['nom'] . '">';
                echo '<form method="post" action="index.php">';
                echo '<input type="hidden" name="produit_id" value="' . $produit['id'] . '">';
                echo '<button>Ajouter au panier</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
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
<div id="toaster">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="toast">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const toast = document.querySelector('.toast');

    if (toast) {
        setTimeout(() => {
            toast.style.animation = 'slide-out 0.3s forwards';

            toast.addEventListener('animationend', () => {
                toast.remove();
            });
        }, 3000); 
    }
</script>
</body>

</html>