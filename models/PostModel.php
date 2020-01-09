<?php
declare(strict_types=1);

class PostModel {
    const TABLE = 'posts';

    public function insert(string $name, string $subject, int $created, int $last_updated, string $message, string $file_id = null, string $ip_address, string $password, string $parent_id = null, bool $hidden): string {
        $pdo = NuPDO::getInstance();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE . '(name, subject, created, last_updated, message, file_id, ip_address, password, parent_id, hidden) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute(array(
                $name,
                $subject,
                $created,
                $last_updated,
                $message,
                $file_id,
                $ip_address,
                $password,
                $parent_id,
                (int)$hidden
            ));

            $last_id = $pdo->lastInsertId();

            if ($parent_id !== null) {
                // Bump parent
                $stmt = $pdo->prepare('UPDATE ' . self::TABLE . ' SET last_updated = ? WHERE id = ?');
                $stmt->execute(array(
                    time(),
                    $parent_id
                ));
            }

            $pdo->commit();
        }
        catch (Exception $e) {
            $pdo->rollback();
        }

        return $last_id;
    }

    public function getByParentId(string $parent_id): array {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $stmt = $pdo->prepare("SELECT $posts.*, $files.name AS file_name, $files.size AS file_size FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id = ?
            ORDER BY $posts.created ASC");
        $stmt->execute(array($parent_id));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParents(int $limit, int $offset): array {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $stmt = $pdo->prepare("SELECT $posts.*, $files.name AS file_name, $files.size AS file_size FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id IS NULL
            ORDER BY $posts.last_updated DESC
            LIMIT ?, ?");
        $stmt->bindValue(1, $offset, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildren(array $ids, int $limit) {
        $ids_length = count($ids);
        if ($ids_length === 0) {
            return array();
        }

        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $query = "SELECT $posts.*, $files.name AS file_name, $files.size AS file_size, $files.extension as file_extension, $files.width as file_width, $files.height as file_height FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id IN ( ";
        for ($i=0; $i<$ids_length; ++$i) {
            $query .= "?,";
        }
        $query = substr($query, 0, -1) . ")
        ORDER BY $posts.parent_id, $posts.created DESC
        LIMIT ?";

        $stmt = $pdo->prepare($query);
        for ($i=0; $i<$ids_length; ++$i) {
            $stmt->bindValue($i + 1, $ids[$i]);
        }
        $stmt->bindValue($i + 1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildCount(array $ids): array {
        $ids_length = count($ids);
        if ($ids_length === 0) {
            return array();
        }

        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $query = "SELECT COUNT(*) AS total, $posts.parent_id AS parent_id FROM $posts
        WHERE $posts.parent_id IN (";
        for ($i=0; $i<$ids_length; ++$i) {
            $query .= "?,";
        }

        $query = substr($query, 0, -1) . ") GROUP BY $posts.parent_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getThread(string $id) {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $query = "SELECT $posts.*, $files.name AS file_name, $files.size AS file_size, $files.extension as file_extension, $files.width as file_width, $files.height as file_height FROM $posts
        LEFT JOIN $files ON $posts.file_id = $files.id
        WHERE ($posts.id = :id AND $posts.parent_id IS NULL) OR ($posts.parent_id = :id)
        ORDER BY $posts.created ASC";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastPostByIpAddress(string $ip_address) {
        $pdo = NuPDO::getInstance();
        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE ip_address = ? ORDER BY created DESC LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($ip_address));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
