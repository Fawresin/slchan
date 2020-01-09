<?php

class BanModel {
    const TABLE = 'bans';

    public function insert(string $ip_address, string $reason, int $created, ?int $expires, ?string $post_id)
    {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE . '(ip_address, reason, created, expires, post_id) VALUES(?, ?, ?, ?, ?)');
        $stmt->execute(array(
            $ip_address,
            $reason,
            $created,
            $expires,
            $post_id
        ));

        return $pdo->lastInsertId();
    }

    public function remove(string $ip_address) {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE . ' WHERE ip_address = ?');
        $stmt->execute(array($ip_address));
    }

    public function getByIpAddress(string $ip_address) {
        $pdo = NuPDO::getInstance();
        $bans = self::TABLE;
        $posts = PostModel::TABLE;
        $files = FileModel::TABLE;
        $stmt = $pdo->prepare("SELECT $bans.*, $posts.id AS post_id, $posts.subject AS post_subject, $posts.created AS post_created, $posts.message AS post_message, $files.id AS file_id, $files.name AS file_name FROM $bans
        LEFT JOIN $posts ON $bans.post_id = $posts.id
        LEFT JOIN $files ON $posts.file_id = $files.id
        WHERE $bans.ip_address = ? AND $bans.expires > ?
        LIMIT 1");
        $stmt->execute(array($ip_address, time()));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
