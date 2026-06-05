<?php
require_once __DIR__ . '/../php/app.php';

// vérifie si l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

// traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ajout d'un produit
    if ($_POST['action'] === 'add') {
        add($_POST['nom'], $_POST['description'], (int) $_POST['prix'], $_POST['image']);
        $_SESSION['message'] = 'Produit ajouté !';
        header('Location: admin.php');
        exit();
    }

    // modification d'un produit
    if ($_POST['action'] === 'edit') {
        $id = (int) $_POST['id'];
        $produit = selectOne($id);
        $image = $_POST['image'] ?: $produit['image'];
        update($id, $_POST['nom'], $_POST['description'], (int) $_POST['prix'], $image);
        $_SESSION['message'] = 'Produit modifié !';
        header('Location: admin.php');
        exit();
    }

    // suppression d'un produit
    if ($_POST['action'] === 'delete') {
        delete((int) $_POST['id']);
        $_SESSION['message'] = 'Produit supprimé.';
        header('Location: admin.php');
        exit();
    }

    // suppression d'une commande
    if ($_POST['action'] === 'delete_commande') {
        deleteCommande((int) $_POST['id_commande']);
        $_SESSION['message'] = 'Commande supprimée.';
        header('Location: admin.php');
        exit();
    }
}

// récupération des produits et des commandes
$produits = selectAll();
$commandes = selectAllCommande();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastOrder! - Admin</title>
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
    <h1>Admin</h1>
    <div class="dashboard">
        <h2>Tableau de bord</h2>

        <select name="choseAction" id="choseAction">
            <option value="add">Ajouter un produit</option>
            <option value="edit">Modifier un produit</option>
            <option value="delete">Supprimer un produit</option>
        </select>

        <div class="productCrud">

            <!-- FORMULAIRE AJOUT -->
            <form method="post" action="admin.php" id="addProductForm">
                <input type="hidden" name="action" value="add">
                <h3>Ajouter un produit</h3>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="number" name="prix" placeholder="Prix" required>
                <input type="text" name="image" placeholder="URL de l'image" required>
                <button type="submit">Ajouter</button>
            </form>

            <!-- FORMULAIRE MODIFICATION -->
            <form method="post" action="admin.php" id="editProductForm" style="display:none">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <h3>Modifier un produit</h3>
                <select name="produit_select" id="produit_select" onchange="remplirFormEdit(this.value)">
                    <option value="">-- Choisir un produit --</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= $p['id'] ?>" data-nom="<?= htmlspecialchars($p['nom']) ?>"
                            data-description="<?= htmlspecialchars($p['description']) ?>" data-prix="<?= $p['prix'] ?>"">
                            <?= htmlspecialchars($p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="nom" id="edit_nom" placeholder="Nom" required>
                <input type="text" name="description" id="edit_description" placeholder="Description" required>
                <input type="number" name="prix" id="edit_prix" placeholder="Prix" required>
                <input type="text" name="image" id="edit_image" placeholder="URL de l'image">
                <small>Laisser vide pour garder l'image actuelle</small>
                <button type="submit">Modifier</button>
            </form>

            <!-- FORMULAIRE SUPPRESSION -->
            <form method="post" action="admin.php" id="deleteProductForm" style="display:none">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <h3>Supprimer un produit</h3>
                <select name="produit_select_delete" onchange="document.getElementById('delete_id').value = this.value">
                    <option value="">-- Choisir un produit --</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" onclick="return confirm('Supprimer ce produit ?')">Supprimer</button>
            </form>

            <!-- LISTE DES PRODUITS -->
            <div id="productList">
                <h3>Liste des produits</h3>
                <ul>
                    <?php foreach ($produits as $p): ?>
                        <li>
                            <?= htmlspecialchars($p['nom']) ?> —
                            <?= $p['prix'] ?> CHF
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <!-- LISTE DES COMMANDES -->
        <div class="commandes">
            <h3>Liste des commandes</h3>
            <div>
                <?php if (empty($commandes)): ?>
                    <p class="commandes-vide">Aucune commande pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($commandes as $commande): ?>
                        <div class="commande">
                            <div class="commande-header">
                                <span class="commande-id">Commande #
                                    <?= $commande['id'] ?>
                                </span>
                            </div>
                            <ul class="commande-produits">
                                <?php foreach (selectProduitsByCommande($commande['id']) as $p): ?>
                                    <li>
                                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
                                        <span>
                                            <?= htmlspecialchars($p['nom']) ?>
                                        </span>
                                        <span class="commande-prix">
                                            <?= $p['prix'] ?> CHF
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="commande-total">
                                    <span>Total:</span>
                                    <span class="commande-prix" style="font-weight: bold;">
                                        <?= array_sum(array_column(selectProduitsByCommande($commande['id']), 'prix')) ?> CHF
                                    </span>
                                </li>
                                <li>
                                    <form action="admin.php" method="post">
                                        <input type="hidden" name="action" value="delete_commande">
                                        <input type="hidden" name="id_commande" value="<?= $commande['id'] ?>">
                                        <button type="submit" onclick="return confirm('Supprimer cette commande ?')">Supprimer</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<footer>
    <div>
        <div>
            <a href="">Politique de confidentialité</a>
            <span>•</span>
            <a href="">Mentions légales</a>
        </div>
        <div>&copy; 2026 FastOrder - Tous droits réservés</div>
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

    // gestion de l'affichage des formulaires
    const choseAction = document.getElementById('choseAction');
    choseAction.addEventListener('change', (e) => {
        document.querySelectorAll('.productCrud form').forEach(f => f.style.display = 'none');
        const map = { add: 'addProductForm', edit: 'editProductForm', delete: 'deleteProductForm' };
        document.getElementById(map[e.target.value]).style.display = 'block';
    });

    function remplirFormEdit(id) {
        const option = document.querySelector(`#produit_select option[value="${id}"]`);
        if (!option) return;
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nom').value = option.dataset.nom;
        document.getElementById('edit_description').value = option.dataset.description;
        document.getElementById('edit_prix').value = option.dataset.prix;
        // document.getElementById('edit_image').value = option.dataset.image;
    }

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