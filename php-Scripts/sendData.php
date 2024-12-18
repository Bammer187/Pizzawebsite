<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$host = 'localhost';
$db_name = 'pizzawebsite';
$username = 'root';
$password = '';
$link1 = 'customers';
$link2 = 'Orders';

$DB_Connect = mysqli_connect($host, $username, $password)
            or exit ("Es konnte keine Verbindung zur Datenbank ergestellt werden");

$DB_SELECT = mysqli_select_db($DB_Connect, $db_name)
            or exit ("Es konnte keine Verbindung zur Datenbank ergestellt werden");

$rawInput = file_get_contents("php://input");

$decodedInput = json_decode($rawInput, true);
if ($decodedInput) {
    $adresse = $decodedInput["CustomerInfo"]['Adresse'] ?? '';
    $postleitzahl = $decodedInput["CustomerInfo"]['Postleitzahl'] ?? '';
    $stadt = $decodedInput["CustomerInfo"]['Stadt'] ?? '';
    $vorname = $decodedInput["CustomerInfo"]['Vorname'] ?? '';
    $nachname = $decodedInput["CustomerInfo"]['Nachname'] ?? '';
    $telefon = $decodedInput["CustomerInfo"]['Telefon'] ?? '';
    $email = $decodedInput["CustomerInfo"]['Email'] ?? '';

    $insertCustomerQuery = "INSERT INTO $link1 (FirstName, LastName, Adress, Postal, City, Phone, Email) 
                            VALUES ('$vorname', '$nachname', '$adresse', '$postleitzahl', '$stadt' ,'$telefon', '$email')";
    $customerResult = mysqli_query($DB_Connect, $insertCustomerQuery);

    $customerID = mysqli_insert_id($DB_Connect) ?? 1;
    $date = $decodedInput["date"] ?? '';

    $pizzas = $decodedInput['pizzas'] ?? [];
    foreach ($pizzas as $pizzaName => $pizzaData) {
        $pizzaID = $pizzaData['id'] ?? 0;
        $anzahl = $pizzaData['amount'] ?? 0;

        $insertOrderQuery = "INSERT INTO $link2 (CustomerID, PizzaID, Amount, OrderDate)
                            VALUES ('$customerID', '$pizzaID', '$anzahl', '$date')";
        $orderResult = mysqli_query($DB_Connect, $insertOrderQuery);
    }
}
mysqli_close($DB_Connect);

?>