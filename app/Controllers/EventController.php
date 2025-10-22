<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Event.php';

class EventController
{
    public static function handleRequest($pdo)
    {
        $eventModel = new Event($pdo);

        // Si no hay sesión activa, no hacemos nada
        if (!isset($_SESSION['user_id'])) {
            return [];
        }

        // Crear evento
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_event') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $date = $_POST['date'] ?? '';
            $capacity = intval($_POST['capacity'] ?? 100);
            $user_id = $_SESSION['user_id'];

            // Validaciones
            if (empty($title)) {
                $_SESSION['error'] = "El título es obligatorio.";
                header("Location: " . url('dashboard') . "&show_create_modal=1");
                exit;
            }

            if (empty($date)) {
                $_SESSION['error'] = "La fecha es obligatoria.";
                header("Location: " . url('dashboard') . "&show_create_modal=1");
                exit;
            }

            if ($capacity < 1) {
                $_SESSION['error'] = "La capacidad debe ser mayor a 0.";
                header("Location: " . url('dashboard') . "&show_create_modal=1");
                exit;
            }

            // Crear evento
            if ($eventModel->create($title, $description, $date, $capacity, $user_id)) {
                header("Location: " . url('dashboard') . "&event_created=1");
            } else {
                $_SESSION['error'] = "Error al crear el evento.";
                header("Location: " . url('dashboard'));
            }
            exit;
        }

        // Actualizar evento
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_event') {
            $id = intval($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $date = $_POST['date'] ?? '';
            $capacity = intval($_POST['capacity'] ?? 100);
            $user_id = $_SESSION['user_id'];

            // Validaciones
            if (empty($title) || empty($date)) {
                $_SESSION['error'] = "El título y la fecha son obligatorios.";
                header("Location: " . url('dashboard') . "&show_edit_modal=1&edit_id=$id");
                exit;
            }

            // Verificar propiedad y actualizar
            if ($eventModel->belongsToUser($id, $user_id)) {
                if ($eventModel->update($id, $title, $description, $date, $capacity)) {
                    header("Location: " . url('dashboard') . "&event_updated=1");
                } else {
                    $_SESSION['error'] = "Error al actualizar el evento.";
                    header("Location: " . url('dashboard'));
                }
            } else {
                $_SESSION['error'] = "No tienes permisos para editar este evento.";
                header("Location: " . url('dashboard'));
            }
            exit;
        }

        // Eliminar evento
        if (isset($_GET['delete'])) {
            $event_id = intval($_GET['delete']);
            $user_id = $_SESSION['user_id'];
            
            if ($eventModel->belongsToUser($event_id, $user_id)) {
                if ($eventModel->delete($event_id)) {
                    $_SESSION['success'] = "Evento eliminado correctamente.";
                } else {
                    $_SESSION['error'] = "Error al eliminar el evento.";
                }
            } else {
                $_SESSION['error'] = "No tienes permisos para eliminar este evento.";
            }
            
            header("Location: " . url('dashboard'));
            exit;
        }

        // Listar todos los eventos del usuario
        return $eventModel->allByUser($_SESSION['user_id']);
    }

    public static function create()
    {
        // Este método se usa cuando se accede vía index.php?page=event_create
        global $pdo;
        self::handleRequest($pdo);
    }

    public static function edit($id)
    {
        // Este método se usa cuando se accede vía index.php?page=event_edit
        global $pdo;
        self::handleRequest($pdo);
    }
}