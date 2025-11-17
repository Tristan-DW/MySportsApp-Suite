<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use PDO;

class KnowledgeController
{
    public function adminList(): void
    {
        require_login();
        require_role(['super_admin', 'support']);

        $stmt = db()->query("SELECT * FROM knowledge_articles ORDER BY created_at DESC");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        render('pages/knowledge_admin', ['articles' => $articles]);
    }

    public function edit(): void
    {
        require_login();
        require_role(['super_admin', 'support']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $db = db();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete']) && $id) {
                $stmt = $db->prepare("DELETE FROM knowledge_articles WHERE id = :id");
                $stmt->execute([':id' => $id]);
                header('Location: /index.php?route=knowledge');
                exit;
            }

            $title    = trim($_POST['title'] ?? '');
            $content  = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $isPublic = isset($_POST['is_public']) ? 1 : 0;

            if ($id) {
                $stmt = $db->prepare("
                    UPDATE knowledge_articles
                    SET title=:title, content=:content, category=:category, is_public=:is_public, updated_at=NOW()
                    WHERE id=:id
                ");
                $stmt->execute([
                    ':title'     => $title,
                    ':content'   => $content,
                    ':category'  => $category,
                    ':is_public' => $isPublic,
                    ':id'        => $id,
                ]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO knowledge_articles (title, content, category, is_public, created_at, updated_at)
                    VALUES (:title,:content,:category,:is_public,NOW(),NOW())
                ");
                $stmt->execute([
                    ':title'     => $title,
                    ':content'   => $content,
                    ':category'  => $category,
                    ':is_public' => $isPublic,
                ]);
            }

            header('Location: /index.php?route=knowledge');
            exit;
        }

        $article = null;
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM knowledge_articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        render('pages/knowledge_edit', ['article' => $article]);
    }

    public function publicFaq(): void
    {
        $stmt = db()->prepare("SELECT * FROM knowledge_articles WHERE is_public = 1 ORDER BY category, created_at DESC");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        render('pages/faq_public', ['articles' => $articles]);
    }
}
