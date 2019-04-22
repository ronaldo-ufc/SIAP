<?php

namespace siap\setor\models;

use siap\models\DBSiap;
use siap\setor\models\Setor;
use siap\usuario\models\Usuario;

class SetorResponsavel {

    private $setor_id;
    private $responsavel_id;
    private $data_inicio;
    private $data_fim;
    private static $setor;
    private static $responsavel;
    private static $ativo;

    private function bundle($row) {
        $u = new SetorResponsavel();
        $u->setSetor_id($row['setor_id']);
        $u->setResponsavel_id($row['responsavel_id']);
        $u->setData_inicio($row['data_inicio']);
        $u->setData_fim($row['data_fim']);
        $u->setAtivo($row['ativo']);

        $u->setResponsavel(Usuario::getByLogin($row['responsavel_id']));

        $u->setSetor(Setor::getById($row['setor_id']));
        return $u;
    }

    static function getAll() {
        $sql = "select * from setor_responsavel where data_fim >= current_date ";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getLastBySetor($setor) {
        $sql = "select * from setor_responsavel where setor_id = ? order by data_fim desc limit 1 ";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor));
        $row = $stmt->fetch();
        if ($row == null) {
            return false;
        }
        return self::bundle($row);
    }

    static function create($setor, $responsavel, $inicio, $fim) {
        $sql = 'INSERT INTO setor_responsavel (setor_id, responsavel_id, data_inicio, data_fim) VALUES (?, ?, ?, ?)';
        $des = self::desabilita($setor);
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor, $responsavel, $inicio, $fim));
        return $stmt->errorInfo();
    }

    static function desabilita($setor) {
        $antes = self::getLastBySetor($setor);
        if ($antes == FALSE) {
            return;
        }
        $sql = 'UPDATE public.setor_responsavel SET ativo = FALSE WHERE setor_id = ? and responsavel_id = ? and data_inicio = ? and data_fim = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor, $antes->getResponsavel_id(), $antes->getData_inicio(), $antes->getData_fim()));
        return $stmt->errorInfo();
    }

    static function delete($setor, $responsavel, $inicio) {
        $sql = 'DELETE FROM setor_responsavel WHERE setor_id = ? and responsavel_id = ? and data_inicio = ?';

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor, $responsavel, $inicio));
        return $stmt->errorInfo();
    }

    static function getResponsavelBySetorAndData($setor, $data) {
        $sql = "select * from setor_responsavel where setor_id = ? and (? <= data_fim and ? >= data_inicio)";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor, $data, $data));
        $row = $stmt->fetch();
        if ($row == null) {
            return false;
        }
        return self::bundle($row);
    }

    static function getCurrentResponsavelBySetor($setor) {
        $sql = "select * from setor_responsavel where setor_id = ? and (now() <= data_fim and now() > data_inicio )";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor));
        $row = $stmt->fetch();
        if ($row == null) {
            return false;
        }
        return self::bundle($row);
    }

    static function getCurrentResponsavelAllSetor() {
        //Eu achava que essa primeira funcionava, essas comparações de data estão me bugando
        //$sql = "select * from setor_responsavel where now() <= data_fim and now() > data_inicio and ativo = TRUE";
        //$sql = "select * from setor_responsavel where now() <= data_fim and now() <= data_inicio and ativo = TRUE";
        $sql = "select * from setor_responsavel where now() <= data_fim and now() <= data_inicio";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        if (sizeof($result) == 0) {
            return false;
        }
        return $result;
    }

    static function getResponsavelByDataIni($data_ini) {
        //$sql = "select * from setor_responsavel where data_inicio >= ? and ativo = TRUE";
        $sql = "select * from setor_responsavel where data_inicio >= ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($data_ini));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        if (sizeof($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    static function getResponsavelByDataFim($data_fim) {
        //$sql = "select * from setor_responsavel where data_fim <= ? and ativo = TRUE";
        $sql = "select * from setor_responsavel where data_fim <= ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($data_fim));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        if (sizeof($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    static function getResponsavelByRange($data_ini, $data_fim) {
        //$sql = "select * from setor_responsavel where ? >= '07-07-2018' and ? <= '31-12-2019' and ativo = TRUE";
        $sql = "select * from setor_responsavel where ? >= '07-07-2018' and ? <= '31-12-2019'";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($data_ini, $data_fim));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        if (sizeof($result) == 0) {
            return false;
        } else {
            return $result;
        }
    }

    function getSetor_id() {
        return $this->setor_id;
    }

    function getResponsavel_id() {
        return $this->responsavel_id;
    }

    function getData_inicio() {
        return $this->data_inicio;
    }

    function getData_fim() {
        return $this->data_fim;
    }

    function getSetor() {
        return $this->setor;
    }

    function getResponsavel() {

        return $this->responsavel;
    }

    function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }

    function setResponsavel_id($responsavel_id) {
        $this->responsavel_id = $responsavel_id;
    }

    function setData_inicio($data_inicio) {
        $this->data_inicio = $data_inicio;
    }

    function setData_fim($data_fim) {
        $this->data_fim = $data_fim;
    }

    function setSetor($setor) {

        $this->setor = $setor;
    }

    function setResponsavel($responsavel) {

        $this->responsavel = $responsavel;
    }

    static function getAtivo() {
        return self::$ativo;
    }

    static function setAtivo($ativo) {
        self::$ativo = $ativo;
    }

}
