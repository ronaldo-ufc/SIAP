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
            self::$siap = new \PDO("pgsql:host=sistemas.crateus.ufc.br port=5433 dbname=ufccrateus user=ronaldo password=cc@ufc!_");
//            self::$phoenix = new \PDO("pgsql:host=10.0.5.6 dbname=phoenix user=postgres password=IBMpostgreS", array(
//                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
//                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
//            ));
            self::$siap->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ['models\DBStatement', [self::$siap]]);
        }
        return self::$siap;
    }

}