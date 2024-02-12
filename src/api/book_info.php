<?php
require_once __DIR__ . '/../utils.php';

header('Content-Type: application/json');

if (!isset($_GET["id"])) {
    echo json_encode(array());
    exit;
}

$book_id = $_GET["id"];

if (!is_numeric($book_id)) {
    echo json_encode(array());
    exit;
}

$books_matching = execute_query('SELECT * FROM `books` WHERE id=:id', ['id' => $book_id])->fetchAll();

if (count($books_matching) == 0) {
    //no books found, send back nothing
    echo json_encode(array());
    exit;
}

$book = $books_matching[0];

$book_data = array(
    "title" => $book['title'],
    "image" => $book['image'],
    "price" => $book['price'] / 100,
);


echo json_encode($book_data);
