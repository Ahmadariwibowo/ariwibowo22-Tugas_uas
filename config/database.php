<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->fashion_store;
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}

// Test connection
$result = $database->command(['ping' => 1]);
echo "<!-- Connected successfully to MongoDB -->\n";

// Get collection stats
try {
    $productsCollection = $database->products;
    $productCount = $productsCollection->countDocuments();
    echo "<!-- Products collection count: " . $productCount . " -->\n";
} catch (Exception $e) {
    echo "<!-- Error getting collection stats: " . $e->getMessage() . " -->\n";
}

// Collections
$products = $database->products;
$users = $database->users;
$orders = $database->orders;
?> 