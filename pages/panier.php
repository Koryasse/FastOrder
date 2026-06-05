<?php
require_once __DIR__ . '/../php/app.php';

// traitement des actions du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action'])) {
        // vider le panier
        if ($_POST['action'] === 'vider') {
            viderPanier();
            $_SESSION['message'] = 'Panier vidé.';
            header('Location: panier.php');
            exit();
        }
        // retirer un produit
        if ($_POST['action'] === 'retirer') {
            retirerDuPanier((int)$_POST['produit_id']);
            $_SESSION['message'] = 'Produit retiré.';
            header('Location: panier.php');
            exit();
        }
        // valider la commande
        if ($_POST['action'] === 'valider') {
            validerCommande();
            $_SESSION['message'] = 'Commande validée !';
            header('Location: panier.php');
            exit();
        }
    }
}

// récupération des produits dans le panier
$produits = selectProduitDansPanier();
$total = array_sum(array_column($produits, 'prix'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastOrder!</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<header>
    <nav>
        <div>
            <p>FastOrder</p>
        </div>
        <div>
            <a href="../index.php">Menu</a>
            <a href="login.php">Admin</a>
        </div>
        <div>
            <a href="panier.php"><img src="../img/panier.svg" alt="Panier"></a>
        </div>
    </nav>
</header>
<main>
    <h1>Panier</h1>
    <div class="panier">
        <?php if (empty($produits)): ?>
            <p class="panier-vide">Votre panier est vide.</p>
        <?php else: ?>
            <ul class="panier-liste">
                <?php foreach ($produits as $p): ?>
                    <li class="panier-item">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                        <div class="panier-item-info">
                            <span class="panier-item-nom"><?= htmlspecialchars($p['nom']) ?></span>
                            <span class="panier-item-desc"><?= htmlspecialchars($p['description']) ?></span>
                        </div>
                        <span class="panier-item-prix"><?= $p['prix'] ?> CHF</span>
                        <form method="post" action="panier.php">
                            <input type="hidden" name="action" value="retirer">
                            <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="panier-retirer">✕</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="panier-footer">
                <span class="panier-total">Total : <?= $total ?>€</span>
                <div class="panier-actions">
                    <form method="post" action="panier.php">
                        <input type="hidden" name="action" value="vider">
                        <button type="submit" class="btn-vider">Vider le panier</button>
                    </form>
                    <form method="post" action="panier.php">
                        <input type="hidden" name="action" value="valider">
                        <button type="submit" class="btn-valider">Valider la commande</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<footer>
    <div>
        <div>
            <a href="">Politique de confidentialité</a>
            <span>•</span>
            <a href="">Mentions légales</a>
        </div>
        <div>
            &copy; 2026 FastOrder - Tous droits réservés
        </div>
    </div>
</footer>

<?php if (isset($_SESSION['message'])): ?>
    <div id="toaster">
        <div class="toast" id="toast">
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    </div>
<?php endif; ?>

<script>
    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.style.animation = 'slide-out 0.3s forwards';
            toast.addEventListener('animationend', () => toast.remove());
        }, 3000);
    }
</script>
</body>
</html>