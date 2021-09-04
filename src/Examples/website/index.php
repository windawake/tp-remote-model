<?php 
require_once __DIR__.'/controller.php';

$method = $_SERVER['REQUEST_METHOD'];
$table = $_GET['table'];
$id = $_GET['id'] ?? 0;

if (!$_POST) {
    $_POST = json_decode(file_get_contents('php://input'), true);
}

$controller = new Controller($table, $_POST);
$content = '404';

if ($method == 'GET') {
    if ($id) {
        $content = $controller->show($id);
    } else {
        $content = $controller->index();
    }
} elseif ($method == 'POST') {
    $content = $controller->store();
} elseif ($method == 'PUT') {
    $content = $controller->update($id);
} elseif ($method == 'DELETE') {
    $content = $controller->destory($id);
}

header('content-type:application/json');
echo json_encode($content, JSON_UNESCAPED_UNICODE);


