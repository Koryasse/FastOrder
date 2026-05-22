<?php
session_start();
require_once __DIR__ . '/database.php';

if(!isset($_SESSION["panier"]))
    $_SESSION["panier"] = array();

function selectAll() : array {
    $statement = db()->prepare(
        "SELECT *
        FROM Produit"
    );
    
    $statement->execute();

    return $statement->fetchAll();
}

function selectAllCommande() : array {
    $statement = db()->prepare(
        "SELECT *
        FROM Commande"
    );
    
    $statement->execute();

    return $statement->fetchAll();
}

function add(string $nom, string $description, int $prix,string $image) : void {
    $statement = db()->prepare(
        "INSERT INTO Produit(nom, description, prix, image)
         VALUES(:nom, :description, :prix, :image)"
    );

    $statement->execute([
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'image' => $image
    ]);
}


function delete(int $id) : void {
    $statement = db()->prepare(
        "DELETE FROM Produit
         WHERE id = :id"
    );

$statement->execute([
    'id' => $id
]);
}

function update(int $id, string $nom, string $description, int $prix,string $image) : void {
    $statement = db()->prepare(
        "UPDATE Produit
         SET nom = :nom,  description = :description, prix = :prix, image = :image
         WHERE id = :id"
    );

    $statement->execute([
        'id' => $id,
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'image' => $image
    ]);
}

function ajouterAuPanier(int $productId){
    $produit = selectOne($productId);
    if(!$produit) return;

    $_SESSION["panier"][$productId] = $produit;

}

function selectOne(int $id) : array|false {
    $statement = db()->prepare(
        "SELECT *
        FROM Produit
        WHERE id = :id"
    );

    $statement->execute([
        'id' => $id
    ]);

    return $statement->fetch();
}


function selectProduitDansPanier() : array {
    if(empty($_SESSION["panier"]))
        return array();

    $produits = array();

    foreach($_SESSION["panier"] as $item) {
        $produit = selectOne($item["id"]);
        if($produit) {
            $produits[] = $produit;
        }
    }

    return $produits;
}


function validerCommande(){
    if(empty($_SESSION["panier"]))
        return;

    $statement = db()->prepare(
        "INSERT INTO Commande()
         VALUES()"
    );
    $statement->execute();

    $commandeId = db()->lastInsertId();

    foreach($_SESSION["panier"] as $item) {
        $statement = db()->prepare(
            "INSERT INTO Commande_produit(id_commande, id_produit)
             VALUES(:id_commande, :id_produit)"
        );
        $statement->execute([
            'id_commande' => $commandeId,
            'id_produit' => $item["id"]
        ]);
    }

    $_SESSION["panier"] = array();
}

function checkAdminCredentials(string $username, string $password): bool {
    if ($username === 'admin' && $password === 'Super') {
        $_SESSION['admin'] = true;
        return true;
    }
    return false;
}

function selectProduitsByCommande(int $commandeId) : array {
    $statement = db()->prepare(
        "SELECT p.*
         FROM Produit p
         JOIN Commande_produit cp ON cp.id_produit = p.id
         WHERE cp.id_commande = :commande_id"
    );

    $statement->execute([
        'commande_id' => $commandeId
    ]);

    return $statement->fetchAll();
}

function viderPanier() : void {
    $_SESSION['panier'] = array();
}

function retirerDuPanier(int $productId) : void {
    if (isset($_SESSION['panier'][$productId])) {
        unset($_SESSION['panier'][$productId]);
    }
}

function deleteCommande(int $idCommande) : void {

    $statement = db()->prepare(
        "DELETE FROM Commande_produit
         WHERE id_commande = :id"
    );

    $statement->execute([
        'id' => $idCommande
    ]);

    $statement = db()->prepare(
        "DELETE FROM Commande
         WHERE id = :id"
    );

    $statement->execute([
        'id' => $idCommande
    ]);
}