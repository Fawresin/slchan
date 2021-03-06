<?php

class FileModel {
    const TABLE = 'files';

    public function insert(string $name, int $size, string $extension, int $width, int $height, int $thumbnail_width, int $thumbnail_height, string $hash, int $created, string $ip_address): string {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE . '(name, size, extension, width, height, thumbnail_width, thumbnail_height, hash, created, ip_address) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array(
            $name,
            $size,
            $extension,
            $width,
            $height,
            $thumbnail_width,
            $thumbnail_height,
            $hash,
            $created,
            $ip_address
        ));

        return $pdo->lastInsertId();
    }

    public function getByHash(string $hash) {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE hash = ? LIMIT 1');
        $stmt->execute(array($hash));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hideById(string $id) {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('UPDATE ' . self::TABLE . ' SET hidden = TRUE WHERE id = ?');
        $stmt->execute(array($id));
    }
}
