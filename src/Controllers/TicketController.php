<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use PDO;

class TicketController
{
    public function adminList(): void
    {
        require_login();
        require_role(['super_admin', 'support']);

        $db = db();
        $stmt = $db->query("SELECT * FROM tickets ORDER BY created_at DESC");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        render('pages/tickets_admin', ['tickets' => $tickets]);
    }

    public function view(): void
    {
        require_login();
        require_role(['super_admin', 'support']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = db();

        if (!$id) {
            header('Location: /index.php?route=tickets');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status  = $_POST['status'] ?? 'open';
            $priority = $_POST['priority'] ?? 'normal';
            $note    = trim($_POST['note'] ?? '');

            $stmt = $db->prepare("UPDATE tickets SET status=:status, priority=:priority, updated_at=NOW() WHERE id=:id");
            $stmt->execute([':status' => $status, ':priority' => $priority, ':id' => $id]);

            if ($note !== '') {
                $user = current_user();
                $stmt = $db->prepare("
                    INSERT INTO ticket_notes (ticket_id, user_id, note, created_at)
                    VALUES (:ticket_id, :user_id, :note, NOW())
                ");
                $stmt->execute([
                    ':ticket_id' => $id,
                    ':user_id'   => $user ? $user['id'] : null,
                    ':note'      => $note,
                ]);
            }

            header('Location: /index.php?route=ticket_view&id=' . $id);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            SELECT tn.*, u.name AS user_name
            FROM ticket_notes tn
            LEFT JOIN users u ON tn.user_id = u.id
            WHERE tn.ticket_id = :id
            ORDER BY tn.created_at ASC
        ");
        $stmt->execute([':id' => $id]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        render('pages/ticket_view', ['ticket' => $ticket, 'notes' => $notes]);
    }

    public function publicForm(): void
    {
        $db = db();
        $success = false;
        $error   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if ($name === '' || $email === '' || $subject === '' || $message === '') {
                $error = 'Please fill in all fields.';
            } else {
                $stmt = $db->prepare("
                    INSERT INTO tickets (public_name, public_email, subject, message, status, priority, created_at, updated_at)
                    VALUES (:name,:email,:subject,:message,'open','normal',NOW(),NOW())
                ");
                $stmt->execute([
                    ':name'    => $name,
                    ':email'   => $email,
                    ':subject' => $subject,
                    ':message' => $message,
                ]);
                $success = true;
            }
        }

        render('pages/ticket_public', [
            'success' => $success,
            'error'   => $error,
        ]);
    }
}
