<?php

namespace siap\models;

class DBSiap {

    /** @var \PDO */
    private static $siap = null;

//    private function __construct() {
//
//    }

    /**
     * @return \PDO
     */
    public static function getSiap() {
        if(self::$siap == null) {
            self::$siap = new \PDO("pgsql:host=10.5.5.10 port=5432 dbname=ufccrateus user=postgres password=IBMpostgreS123");
//            self::$phoenix = new \PDO("pgsql:host=10.0.5.6 dbname=phoenix user=postgres password=IBMpostgreS", array(
//                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
//            ));
            self::$siap->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ['models\DBStatement', [self::$siap]]);
        }
        return self::$siap;
    }

}