<?php

require_once ROOT . '/php/database.php';

function selectAll() : array {
    // Préparer la requête
    $statement = db()->prepare(
        "SELECT id, author, title, pubYear, genreId
        FROM Books"
    );
    
    // Exécuter la requête
    $statement->execute();

    // Lire tous les enregistrement
    return $statement->fetchAll();
}

function selectOne($id) : array | false {
    // Préparer la requête
    $statement = db()->prepare(
        "SELECT author, title, pubYear, genreId
        FROM Books
        WHERE id = :id"
    );

    // Exécuter la requête.
    $statement->execute([
        'id' => $id
    ]);

    // Lire l'enregistrement
    return $statement->fetch();
}

function add(string $author, string $title, int $pubYear, string $gender) : void {
    // Préparer la requête
    $statement = db()->prepare(
        "INSERT INTO Books(author, title, pubYear, gender)
         VALUES(:author, :title, :pubYear, :gender)"
    );

    // Exécuter la requête avec les paramètres
    $statement->execute([
        'author' => $author,
        'title' => $title,
        'pubYear' => $pubYear,
        'gender' => $gender
    ]);
}

function delete() {

}

function update() {

}