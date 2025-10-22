<?php
class Event
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Crear evento
    public function create($title, $description, $date, $capacity, $user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO events (user_id, title, description, date, capacity) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$user_id, $title, $description, $date, $capacity]);
        } catch (PDOException $e) {
            error_log("Event::create() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los eventos de un usuario
    public function allByUser($user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM events WHERE user_id = ? ORDER BY date DESC"
            );
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Event::allByUser() - Error: " . $e->getMessage());
            return [];
        }
    }

    // Buscar evento por ID
    public function find($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Event::find() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar evento
    public function update($id, $title, $description, $date, $capacity)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE events 
                 SET title = ?, description = ?, date = ?, capacity = ? 
                 WHERE id = ?"
            );
            return $stmt->execute([$title, $description, $date, $capacity, $id]);
        } catch (PDOException $e) {
            error_log("Event::update() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar evento
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Event::delete() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si un evento pertenece a un usuario
    public function belongsToUser($event_id, $user_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM events WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$event_id, $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Event::belongsToUser() - Error: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los eventos públicos (excepto los del usuario)
    public function allPublic($exclude_user_id = null)
    {
        try {
            if ($exclude_user_id) {
                $stmt = $this->pdo->prepare(
                    "SELECT e.*, u.name as creator_name 
                     FROM events e
                     INNER JOIN users u ON e.user_id = u.id
                     WHERE e.user_id != ? AND e.date >= NOW()
                     ORDER BY e.date ASC"
                );
                $stmt->execute([$exclude_user_id]);
            } else {
                $stmt = $this->pdo->query(
                    "SELECT e.*, u.name as creator_name 
                     FROM events e
                     INNER JOIN users u ON e.user_id = u.id
                     WHERE e.date >= NOW()
                     ORDER BY e.date ASC"
                );
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Event::allPublic() - Error: " . $e->getMessage());
            return [];
        }
    }

    // Obtener eventos próximos (útil para dashboard)
    public function upcoming($limit = 5)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT e.*, u.name as creator_name 
                 FROM events e
                 INNER JOIN users u ON e.user_id = u.id
                 WHERE e.date >= NOW()
                 ORDER BY e.date ASC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Event::upcoming() - Error: " . $e->getMessage());
            return [];
        }
    }
}