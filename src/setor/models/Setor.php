<?php

namespace siap\setor\models;

use siap\models\DBSiap;

class Setor {

    private $setor_id;
    private $nome;
    private $ativo;
    private $sigla;
    private $responsavel;
    private $bloco_id;
    private $bloco;

    private function bundle($row) {
        $u = new Setor($row['setor_id']);
        $bloco = \siap\setor\models\Bloco::getById($row['bloco_id']);
        $u->setNome($row['nome']);
        $u->setAtivo($row['ativo']);
        $u->setSigla($row['sigla']);
        $u->setBloco_id($row['bloco_id']);
        $u->setBloco($bloco);

        return $u;
    }

    public function __construct($setor_id) {
        $this->setor_id = $setor_id;
    }

    static function getAll() {
        $sql = "select * from setor order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getAllById($id) {
        $sql = "select * from setor where setor_id = ?  union all  select * from setor  where setor_id <> ? order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($id, $id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getAllSetorId() {
        $sql = "SELECT setor_id FROM public.setor ORDER BY setor_id";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getById($setor_id) {
        $sql = "select * from setor where setor_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function verificaAtualResponsavel($setor_id) {
        $sql = "select * from setor where setor_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($setor_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function verificaResponsabelPeloSetor($setor_id, $data) {
        $responsavel = \siap\setor\models\SetorResponsavel::getResponsavelBySetorAndData($setor_id, $data);
        if ($responsavel) {
            return true;
        }
        return false;
    }

    static function create($nome_setor, $sigla, $bloco_id) {
        $sql = "INSERT INTO setor (nome, sigla, bloco_id) VALUES (?, ?, ?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_setor)), strtoupper($sigla), $bloco_id));
        return $stmt->errorInfo();
    }
    
    function update() {
        $sql = "UPDATE public.setor set nome = ?, sigla = ?, bloco_id = ?, ativo = ? WHERE setor_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($this->getNome())), strtoupper($this->getSigla()), $this->getBloco_id(), $this->getAtivo(), $this->setor_id));
        return $stmt->errorInfo();
    }
    
    function delete() {
        $sql = "DELETE FROM public.setor WHERE setor_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($this->setor_id));
        return $stmt->errorInfo();
    }

    function getSetor_id() {
        return $this->setor_id;
    }

    function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }

    function getNome() {
        return $this->nome;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    function getSigla() {
        return $this->sigla;
    }

    function setSigla($sigla) {
        $this->sigla = $sigla;
    }

    public function getResponsavel() {
        return $this->responsavel;
    }

    public function setResponsavel($responsavel) {
        $this->responsavel = $responsavel;
    }

    public function getBloco() {
        return $this->bloco;
    }

    public function setBloco($bloco) {
        $this->bloco = $bloco;
    }

    public function getBloco_id() {
        return $this->bloco_id;
    }

    public function setBloco_id($bloco_id) {
        $this->bloco_id = $bloco_id;
    }

}
