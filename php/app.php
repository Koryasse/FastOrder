<?php
session_start();
require_once ROOT . '/php/database.php';

if($_SESSION["panier"] == null)
    $_SESSION["panier"] = array();

function selectAll() : array {
    // Préparer la requête
    $statement = db()->prepare(
        "SELECT *
        FROM Produit"
    );
    
    // Exécuter la requête
    $statement->execute();

    // Lire tous les enregistrement
    return $statement->fetchAll();
}

function selectAllCommande() : array {
    // Préparer la requête
    $statement = db()->prepare(
        "SELECT *
        FROM Commande"
    );
    
    // Exécuter la requête
    $statement->execute();

    // Lire tous les enregistrement
    return $statement->fetchAll();
}

function add(string $nom, string $description, int $prix,string $image) : void {
    // Préparer la requête
    $statement = db()->prepare(
        "INSERT INTO Produit(nom, description, prix, image)
         VALUES(:nom, :description, :prix, :image)"
    );

    // Exécuter la requête avec les paramètres
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
    // Vérifier si le produit existe
    $produit = selectOne($productId);
    if(!$produit) return;

    // Ajouter le produit au panier
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

    // 1. Créer une nouvelle commande
    $statement = db()->prepare(
        "INSERT INTO Commande()
         VALUES()"
    );
    $statement->execute();

    // 2. Récupérer l'ID de la commande
    $commandeId = db()->lastInsertId();

    // 3. Ajouter les produits du panier à la commande

    foreach($_SESSION["panier"] as $item) {
        $statement = db()->prepare(
            "INSERT INTO Commande_Produit(commande_id, produit_id)
             VALUES(:commande_id, :produit_id)"
        );
        $statement->execute([
            'commande_id' => $commandeId,
            'produit_id' => $item["id"]
        ]);
    }

    // 4. Vider le panier
    $_SESSION["panier"] = array();
}
