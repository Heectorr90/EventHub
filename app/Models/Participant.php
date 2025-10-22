<?php
class Participant
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Registrar usuario en un evento
    public function register($event_id, $user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO participants (event_id, user_id) VALUES (?, ?)"
            );
            return $stmt->execute([$event_id, $user_id]);
        } catch (PDOException $e) {
            // Si ya está registrado (UNIQUE constraint)
            if ($e->getCode() == 23000) {
                error_log("Participant::register() - Usuario ya registrado");
                return false;
            }
            error_log("Participant::register() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Cancelar registro
    public function unregister($event_id, $user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM participants WHERE event_id = ? AND user_id = ?"
            );
            return $stmt->execute([$event_id, $user_id]);
        } catch (PDOException $e) {
            error_log("Participant::unregister() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si un usuario está registrado en un evento
    public function isRegistered($event_id, $user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM participants WHERE event_id = ? AND user_id = ?"
            );
            $stmt->execute([$event_id, $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Participant::isRegistered() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Obtener participantes de un evento
    public function getByEvent($event_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.name, u.email 
                 FROM participants p
                 INNER JOIN users u ON p.user_id = u.id
                 WHERE p.event_id = ?
                 ORDER BY p.registered_at DESC"
            );
            $stmt->execute([$event_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Participant::getByEvent() - Error: " . $e->getMessage());
            return [];
        }
    }

    // Contar participantes de un evento
    public function countByEvent($event_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM participants WHERE event_id = ?"
            );
            $stmt->execute([$event_id]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Participant::countByEvent() - Error: " . $e->getMessage());
            return 0;
        }
    }

    // Obtener eventos en los que el usuario está registrado
    public function getEventsByUser($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT e.*, p.registered_at,
                        (SELECT COUNT(*) FROM participants WHERE event_id = e.id) as total_participants
                 FROM participants p
                 INNER JOIN events e ON p.event_id = e.id
                 WHERE p.user_id = ?
                 ORDER BY e.date ASC"
            );
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Participant::getEventsByUser() - Error: " . $e->getMessage());
            return [];
        }
    }

    // Verificar si hay cupo disponible
    public function hasAvailableSlots($event_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT e.capacity, COUNT(p.id) as registered
                 FROM events e
                 LEFT JOIN participants p ON e.id = p.event_id
                 WHERE e.id = ?
                 GROUP BY e.id, e.capacity"
            );
            $stmt->execute([$event_id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return false;
            }
            
            return (int)$result['registered'] < (int)$result['capacity'];
        } catch (PDOException $e) {
            error_log("Participant::hasAvailableSlots() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Agregar participante manualmente por el creador del evento
    public function addParticipant($event_id, $user_email)
    {
        try {
            // Buscar el usuario por email
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$user_email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado con ese correo'];
            }

            // Verificar si ya está registrado
            if ($this->isRegistered($event_id, $user['id'])) {
                return ['success' => false, 'message' => 'El usuario ya está registrado en este evento'];
            }

            // Verificar cupo
            if (!$this->hasAvailableSlots($event_id)) {
                return ['success' => false, 'message' => 'No hay cupos disponibles para este evento'];
            }

            // Registrar
            if ($this->register($event_id, $user['id'])) {
                return ['success' => true, 'message' => 'Participante agregado exitosamente'];
            }

            return ['success' => false, 'message' => 'Error al agregar el participante'];
        } catch (PDOException $e) {
            error_log("Participant::addParticipant() - Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del servidor. Intenta nuevamente.'];
        }
    }

    // Eliminar participante manualmente
    public function removeParticipant($event_id, $user_id)
    {
        return $this->unregister($event_id, $user_id);
    }

    // Obtener estadísticas de participación de un usuario
    public function getUserStats($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT 
                    COUNT(*) as total_events,
                    COUNT(CASE WHEN e.date >= NOW() THEN 1 END) as upcoming_events,
                    COUNT(CASE WHEN e.date < NOW() THEN 1 END) as past_events
                 FROM participants p
                 INNER JOIN events e ON p.event_id = e.id
                 WHERE p.user_id = ?"
            );
            $stmt->execute([$user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Participant::getUserStats() - Error: " . $e->getMessage());
            return [
                'total_events' => 0,
                'upcoming_events' => 0,
                'past_events' => 0
            ];
        }
    }
}