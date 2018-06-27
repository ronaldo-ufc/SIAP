<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Categoria {

    private $categoria_id;
    private $nome;

    private function bundle($row) {
        $u = new Categoria();
        $u->setCategoria_id($row['categoria_id']);
        $u->setNome($row['nome']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from categoria order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getById($categoria_id) {
        $sql = "select * from categoria where categoria_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($categoria_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_categoria) {
        $sql = "INSERT INTO categoria (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_categoria))));
    }

    static function delete($categoria_id) {
        $sql = 'DELETE FROM categoria WHERE categoria_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($categoria_id));
        return $stmt->errorInfo();
    }

    function getCategoria_id() {
        return $this->categoria_id;
    }

    function getNome() {
        return $this->nome;
    }

    function setCategoria_id($categoria_id) {
        $this->categoria_id = $categoria_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

}
