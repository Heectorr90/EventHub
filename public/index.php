<?php
// Permitir CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$page = $_GET['page'] ?? $_GET['url'] ?? 'login';

// Limpiar la URL
$page = strtok($page, '/');

switch ($page) {
    case 'login':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        AuthController::login();
        break;
    
    case 'register':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        AuthController::register();
        break;
    
    case 'dashboard':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        AuthController::dashboard();
        break;
    
    case 'event_create':
        require_once __DIR__ . '/../app/Controllers/EventController.php';
        EventController::create();
        break;
    
    case 'event_edit':
        require_once __DIR__ . '/../app/Controllers/EventController.php';
        if (isset($_POST['id'])) {
            EventController::edit($_POST['id']);
        } else {
            header("Location: " . url('dashboard'));
            exit;
        }
        break;
    case 'register_event':
        require_once __DIR__ . '/../app/Controllers/ParticipantController.php';
        ParticipantController::register();
        break;
    
    case 'unregister_event':
        require_once __DIR__ . '/../app/Controllers/ParticipantController.php';
        ParticipantController::unregister();
        break;
    
    case 'view_participants':
        require_once __DIR__ . '/../app/Controllers/ParticipantController.php';
        ParticipantController::viewParticipants();
        break;
    
    case 'add_participant':
        require_once __DIR__ . '/../app/Controllers/ParticipantController.php';
        ParticipantController::addParticipant();
        break;
    
    case 'remove_participant':
        require_once __DIR__ . '/../app/Controllers/ParticipantController.php';
        ParticipantController::removeParticipant();
        break;
    
    case 'logout':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        AuthController::logout();
        break;
    
    default:
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>404 - Página no encontrada</title>
            <link rel="stylesheet" href="<?= asset('assets/css/bootstrap.min.css') ?>">
        </head>
        <body class='bg-light d-flex align-items-center justify-content-center' style='min-height: 100vh;'>
            <div class='text-center'>
                <i class='bi bi-exclamation-triangle text-warning' style='font-size: 5rem;'></i>
                <h1 class='display-1 fw-bold text-primary mt-3'>404</h1>
                <p class='fs-3 text-muted'>Página no encontrada</p>
                <p class='text-secondary'>La página que buscas no existe o fue movida.</p>
                <a href='<?= url('login') ?>' class='btn btn-primary mt-3'>
                    <i class='bi bi-house-door'></i> Volver al inicio
                </a>
            </div>
        </body>
        </html>
        <?php
}