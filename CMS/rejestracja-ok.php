<?php


require_once __DIR__ . '/classes/Database/DatabaseConnection.php';

require_once __DIR__ . '/controllers/RejestracjaOkController.php';

use App\Controllers\RejestracjaOkController;

$controller = new RejestracjaOkController();
$controller->showSuccessPage();
