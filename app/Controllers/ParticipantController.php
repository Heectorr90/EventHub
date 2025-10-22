<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Participant.php';
require_once __DIR__ . '/../Models/Event.php';

class ParticipantController
{
    // Registrarse en un evento
    public static function register()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }

        if (isset($_GET['event_id'])) {
            $event_id = intval($_GET['event_id']);
            $user_id = $_SESSION['user_id'];
            
            $participantModel = new Participant($pdo);
            
            // Verificar si hay cupo
            if (!$participantModel->hasAvailableSlots($event_id)) {
                $_SESSION['error'] = "El evento está lleno.";
                header("Location: " . url('events'));
                exit;
            }
            
            // Registrar
            if ($participantModel->register($event_id, $user_id)) {
                $_SESSION['success'] = "Te has registrado exitosamente en el evento.";
            } else {
                $_SESSION['error'] = "Ya estás registrado en este evento.";
            }
            
            header("Location: " . url('events'));
            exit;
        }
        
        // Si no hay event_id, redirigir
        header("Location: " . url('events'));
        exit;
    }

    // Cancelar registro
    public static function unregister()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }

        if (isset($_GET['event_id'])) {
            $event_id = intval($_GET['event_id']);
            $user_id = $_SESSION['user_id'];
            
            $participantModel = new Participant($pdo);
            
            if ($participantModel->unregister($event_id, $user_id)) {
                $_SESSION['success'] = "Has cancelado tu registro.";
            } else {
                $_SESSION['error'] = "No pudimos cancelar tu registro.";
            }
            
            header("Location: " . url('my_events'));
            exit;
        }
        
        header("Location: " . url('my_events'));
        exit;
    }

    // Ver mis eventos registrados
    public static function myEvents()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }
        
        include __DIR__ . '/../../views/participants/my_events.php';
    }

    // Ver participantes de un evento
    public static function viewParticipants()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }
        
        include __DIR__ . '/../../views/participants/list.php';
    }

    // Agregar participante manualmente (AJAX)
    public static function addParticipant()
    {
        global $pdo;
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_id = intval($_POST['event_id'] ?? 0);
            $user_email = trim($_POST['user_email'] ?? '');

            // Validaciones
            if (empty($user_email)) {
                echo json_encode(['success' => false, 'message' => 'El correo es obligatorio']);
                exit;
            }

            if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'El correo no es válido']);
                exit;
            }

            // Verificar que el evento pertenezca al usuario
            $eventModel = new Event($pdo);
            $event = $eventModel->find($event_id);

            if (!$event || $event['user_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para este evento']);
                exit;
            }

            $participantModel = new Participant($pdo);
            $result = $participantModel->addParticipant($event_id, $user_email);
            
            echo json_encode($result);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Eliminar participante manualmente
    public static function removeParticipant()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }

        if (isset($_GET['event_id']) && isset($_GET['user_id'])) {
            $event_id = intval($_GET['event_id']);
            $user_id = intval($_GET['user_id']);

            // Verificar que el evento pertenezca al usuario logueado
            $eventModel = new Event($pdo);
            $event = $eventModel->find($event_id);

            if ($event && $event['user_id'] == $_SESSION['user_id']) {
                $participantModel = new Participant($pdo);
                if ($participantModel->removeParticipant($event_id, $user_id)) {
                    $_SESSION['success'] = "Participante eliminado correctamente.";
                } else {
                    $_SESSION['error'] = "Error al eliminar el participante.";
                }
            } else {
                $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
            }

            header("Location: " . url('view_participants') . "&event_id=$event_id");
            exit;
        }
        
        // Si faltan parámetros, redirigir al dashboard
        header("Location: " . url('dashboard'));
        exit;
    }
}