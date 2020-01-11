<?php
declare(strict_types=1);

class PostModel {
    const TABLE = 'posts';

    public function insert(string $name, string $subject, int $created, int $last_updated, string $message, string $file_id = null, string $ip_address, string $password, string $parent_id = null, bool $hidden, bool $commit = true): string {
        $pdo = NuPDO::getInstance();
        if ($commit)
            $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE . '(name, subject, created, last_updated, message, file_id, ip_address, password, parent_id, hidden) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $subject);
            $stmt->bindValue(3, $created);
            $stmt->bindValue(4, $last_updated);
            $stmt->bindValue(5, $message);
            $stmt->bindValue(6, $file_id);
            $stmt->bindValue(7, $ip_address);
            $stmt->bindValue(8, $password);
            $stmt->bindValue(9, $parent_id);
            $stmt->bindValue(10, $hidden, PDO::PARAM_BOOL);
            $stmt->execute();

            $last_id = $pdo->lastInsertId();

            if ($parent_id !== null) {
                // Bump parent
                $stmt = $pdo->prepare('UPDATE ' . self::TABLE . ' SET last_updated = ? WHERE id = ?');
                $stmt->execute(array(
                    time(),
                    $parent_id
                ));
            }

            if ($commit)
                $pdo->commit();
        }
        catch (Exception $e) {
            if ($commit)
                $pdo->rollback();
        }

        return $last_id;
    }

    public function getByParentId(string $parent_id, bool $hidden = false): array {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $stmt = $pdo->prepare("SELECT $posts.*, $files.id AS file_id, $files.name AS file_name, $files.size AS file_size, $files.extension AS file_extension, $files.width AS file_width, $files.height AS file_height FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id = ? AND $posts.hidden = ?
            ORDER BY $posts.created ASC");
        $stmt->bindValue(1, $parent_id);
        $stmt->bindValue(2, $hidden, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParents(int $limit, int $offset, bool $hidden = false): array {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $stmt = $pdo->prepare("SELECT $posts.*, $files.id AS file_id, $files.name AS file_name, $files.size AS file_size, $files.extension AS file_extension, $files.width AS file_width, $files.height AS file_height FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id IS NULL AND $posts.hidden = ?
            ORDER BY $posts.last_updated DESC
            LIMIT ?, ?");
        $stmt->bindValue(1, $hidden, PDO::PARAM_BOOL);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildren(array $ids, bool $hidden = false) {
        $ids_length = count($ids);
        if ($ids_length === 0) {
            return array();
        }

        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $query = "SELECT $posts.*, $files.id AS file_id, $files.name AS file_name, $files.size AS file_size, $files.extension AS file_extension, $files.width AS file_width, $files.height AS file_height FROM $posts
            LEFT JOIN $files ON $posts.file_id = $files.id
            WHERE $posts.parent_id IN ( ";
        for ($i=0; $i<$ids_length; ++$i) {
            $query .= "?,";
        }
        $query = substr($query, 0, -1) . ")
        AND $posts.hidden = ?
        ORDER BY $posts.parent_id, $posts.created DESC";

        $stmt = $pdo->prepare($query);
        for ($i=0; $i<$ids_length; ++$i) {
            $stmt->bindValue($i + 1, $ids[$i]);
        }
        $stmt->bindValue($i + 1, $hidden, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChildCount(array $ids, bool $hidden = false): array {
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

        $query = substr($query, 0, -1) . ") AND $posts.hidden = ? GROUP BY $posts.parent_id";
        $stmt = $pdo->prepare($query);
        for ($i=0; $i<$ids_length; ++$i) {
            $stmt->bindValue($i + 1, $ids[$i]);
        }
        $stmt->bindValue($i + 1, $hidden, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getThread(string $id, bool $hidden = false) {
        $pdo = NuPDO::getInstance();
        $posts = self::TABLE;
        $files = FileModel::TABLE;
        $query = "SELECT $posts.*, $files.name AS file_name, $files.size AS file_size, $files.extension as file_extension, $files.width as file_width, $files.height as file_height FROM $posts
        LEFT JOIN $files ON $posts.file_id = $files.id
        WHERE ($posts.id = :id OR $posts.parent_id = :id) AND $posts.hidden = :hidden
        ORDER BY $posts.created ASC";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':hidden', $hidden, PDO::PARAM_BOOL);
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

    public function getThreadCount(bool $hidden = false) {
        $pdo = NuPDO::getInstance();
        $query = 'SELECT COUNT(*) FROM ' . self::TABLE . ' WHERE parent_id IS NULL AND hidden = ?';
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $hidden, PDO::PARAM_BOOL);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
