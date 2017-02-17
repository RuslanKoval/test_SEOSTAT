<?php

class Database {
    protected static $_instance = null;

    public static function instance() {

        if ( !isset( self::$_instance ) ) {

            self::$_instance = new Database();

        }

        return self::$_instance;
    }

    protected function __construct() {}

    function __destruct(){}


    public function getConnection($dsn, $username, $password) {

        $conn = null;
        try {

            $conn = new \PDO($dsn, $username, $password);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $e) {
            throw $e;
        }
        catch(Exception $e) {
            throw $e;
        }
    }

    public function __clone()
    {
        return false;
    }
    public function __wakeup()
    {
        return false;
    }
}