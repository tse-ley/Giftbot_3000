<?php

use App\Controller\GiftSearchController;
require_once __DIR__ . '/../vendor/autoload.php';

$controller = new GiftSearchController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criteria = [
        'gender' => $_POST['gender'] ?? '',
        'age' => $_POST['age'] ?? '',
        'interests' => $_POST['interests'] ?? ''
    ];
    
    header('Content-Type: application/json');
    echo json_encode($controller->search($criteria));
    exit;
}


echo $controller->renderSearchPage();
