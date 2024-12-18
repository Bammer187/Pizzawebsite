<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'getPizzas.php';

try {
    // Request all products
    $query = "SELECT * FROM pizzas";
    $result = mysqli_query($DB_Connect, $query);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    // JSON response
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(['message' => 'Fehler beim Abrufen der Daten', 'error' => $e->getMessage()]);

}
?>
