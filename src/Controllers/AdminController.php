<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use PDO;

class AdminController
{
    public function analyticsSources(): void
    {
        require_login();
        require_role(['super_admin']);

        $db = db();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id   = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $dsn  = trim($_POST['dsn'] ?? '');
            $user = trim($_POST['db_user'] ?? '');
            $pass = trim($_POST['db_password'] ?? '');
            $active = isset($_POST['is_active']) ? 1 : 0;

            if ($id) {
                $stmt = $db->prepare("
                    UPDATE analytics_sources
                    SET name=:name, dsn=:dsn, db_user=:user, db_password=:pass, is_active=:active, updated_at=NOW()
                    WHERE id=:id
                ");
                $stmt->execute([
                    ':name' => $name, ':dsn' => $dsn, ':user' => $user,
                    ':pass' => $pass, ':active' => $active, ':id' => $id,
                ]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO analytics_sources (name, dsn, db_user, db_password, is_active, created_at, updated_at)
                    VALUES (:name,:dsn,:user,:pass,:active,NOW(),NOW())
                ");
                $stmt->execute([
                    ':name' => $name, ':dsn' => $dsn, ':user' => $user,
                    ':pass' => $pass, ':active' => $active,
                ]);
            }

            header('Location: /index.php?route=analytics_sources');
            exit;
        }

        $rows = $db->query("SELECT * FROM analytics_sources ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        render('pages/analytics_sources', ['sources' => $rows]);
    }
}
