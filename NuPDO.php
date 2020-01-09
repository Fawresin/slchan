<?php

class NuPDO extends PDO {
    private static $_instance = null;

    private static $_totalQueries = 0;

    // Do not use the constructor! Use getInstance() instead.
    // Can't have it private in older versions of PHP it seems.
    public function __construct(string $dsn, string $user = null, string $password = null, array $options = array()) {
        parent::__construct($dsn, $user, $password, $options);
    }

    public static function getInstance(): NuPDO {
        if (self::$_instance !== null) {
            return self::$_instance;
        }

        try {
            $instance = new NuPDO(DBDRIVER . ':host=' . DBHOST . ':' . DBPORT . ';dbname=' . DBNAME, DBUSER, DBPASSWORD);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$_instance = $instance;
            return $instance;
        }
        catch (PDOException $e) {
            throw new Exception('Could not connect: ' . $e->getMessage());
        }
    }

    public function exec($query): int {
        RuntimeStats::incrementTotalQueries();
        return parent::exec($statement);
    }

    public function prepare($statement, $driver_options = array()): PDOStatement {
        RuntimeStats::incrementTotalQueries();
        return parent::prepare($statement, $driver_options);
    }

    public function query($statement, $fetch_style = null, $arg3 = null, $arg4 = null): PDOStatement {
        RuntimeStats::incrementTotalQueries();
        return parent::query($statement, $fetch_style, $arg3, $arg4);
    }
}
