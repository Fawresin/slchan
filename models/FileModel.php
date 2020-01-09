<?php

class FileModel {
    public const TABLE = 'files';

    public function insert(string $name, int $size, string $extension, int $width, int $height, string $hash, int $created, ?string $post_id, string $ip_address): string {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('INSERT INTO ' . self::TABLE . '(name, size, extension, width, height, hash, created, post_id, ip_address) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(array(
            $name,
            $size,
            $extension,
            $width,
            $height,
            $hash,
            $created,
            $post_id,
            $ip_address
        ));

        return $pdo->lastInsertId();
    }

    public function getByHash(string $hash): File {
        $pdo = NuPDO::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE hash = ? LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'File');
        $stmt->execute(array($hash));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
