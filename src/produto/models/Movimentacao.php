<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace siap\produto\models;

use siap\models\DBSiap;

/**
 * Description of Movimentacao
 *
 * @author Ronaldo
 */
class Movimentacao {

    private $movimentacao_id;
    private $patrimonio;
    private $setor_id;
    private $movimentacao_data;
    private $documento;
    private $observacao;
    private $data;
    private $usuario;
    private $setor;
    private $mov_entrada; //Dizer se a movimentação é de ENTRADA(True) ou SAÍDA(FALSE)

    private function bundle($row) {
        $u = new Movimentacao();
        $u->setMovimentacao_id($row['movimentacao_id']);
        $u->setPatrimonio($row['patrimonio']);
        $u->setSetor_id($row['setor_id']);
        $u->setMovimentacao_data($row['movimentacao_data']);
        $u->setMov_entrada($row['mov_entrada']);
        $u->setDocumento($row['documento']);
        $u->setObservacao($row['observacao']);
        $u->setData($row['data']);
        $u->setUsuario($row['usuario']);
        $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));

        // $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));

        $u->setSetorResponsavel(\siap\setor\models\SetorResponsavel::getResponsavelBySetorAndData($row['setor_id'], $row['movimentacao_data']));

        return $u;
    }

    static function create($patrimonio, $setor_id, $movimentacao_data, $documento, $observacao, $usuario) {
        #Verifica se tem responsavel pelo setor
        if (!Ativos::verificaResponsabelPeloSetor($setor_id, $movimentacao_data)) {
            return array('Erro', 'info', 'No período desta movimentação não existe um Agente Setorial responsável pelo setor. Cadastre primeiro o Agente Setorial para o setor');
        }
        $msg = self::mov_saida($patrimonio, $usuario, $movimentacao_data);
        $sql = "select siap.geramovimentacao (?, ?, ?, ?, ?, ?)";
        $stmt = DBSiap::getSiap()->prepare($sql);

        $stmt->execute(Array($patrimonio, $setor_id, $movimentacao_data, $documento, $observacao, $usuario));

        return $stmt->errorInfo();
    }

    static function getAll() {

        $sql = "SELECT * FROM siap.movimentacao order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getAllByPatrimonio($patrimonio) {

        $sql = "SELECT * FROM siap.movimentacao WHERE patrimonio = ? order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    static function getAllByPatrimonioEntradaOrderById($patrimonio) {

        $sql = "SELECT * FROM siap.movimentacao WHERE patrimonio = ? AND mov_entrada = TRUE order by movimentacao_id asc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getRecentByPatrimonio($patrimonio) {

        $sql = "SELECT * FROM siap.movimentacao WHERE patrimonio = ? AND mov_entrada = TRUE order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($patrimonio));
        $rows = $stmt->fetch();
        return self::bundle($rows);
    }

    private function mov_saida($patrimonio, $usuario, $movimentacao_data) {
        //Pegando os dados da última movimentação de entrada (Setor atual do objeto e etc.)
        $mov = self::getRecentByPatrimonio($patrimonio);
        //Criando a movimentação de saída para o setor.
        $sql = "INSERT INTO siap.movimentacao (patrimonio, setor_id, movimentacao_data, documento, observacao, usuario, mov_entrada) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($mov->getPatrimonio(), $mov->getSetor_id(), $movimentacao_data, 'Sem documento', 'Movimentação de Saída', $usuario, 'FALSE'));
        return $stmt->errorInfo();
    }

    static function getMovData_ini($movimentacao_data) {
        $sql = "SELECT * FROM siap.movimentacao WHERE movimentacao_data >= ? order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($movimentacao_data));
        return $stmt->errorInfo();
    }

    static function getMovData_fim($movimentacao_data) {
        $sql = "SELECT * FROM siap.movimentacao WHERE movimentacao_data <= ? order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($movimentacao_data));
        return $stmt->errorInfo();
    }

    static function getMovData_range($data_ini, $data_fim) {

        $sql = "select * FROM siap.movimentacao WHERE (movimentacao_data BETWEEN ? AND ?) order by movimentacao_id desc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($data_ini, $data_fim));
        return $stmt->errorInfo();
    }

    public function getMovimentacao_id() {
        return $this->movimentacao_id;
    }

    public function getPatrimonio() {
        return $this->patrimonio;
    }

    public function getSetor_id() {
        return $this->setor_id;
    }

    public function getMovimentacao_data() {
        return $this->movimentacao_data;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function getObservacao() {
        return $this->observacao;
    }

    public function setMovimentacao_id($movimentacao_id) {
        $this->movimentacao_id = $movimentacao_id;
    }

    public function setPatrimonio($patrimonio) {
        $this->patrimonio = $patrimonio;
    }

    public function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }

    public function setMovimentacao_data($movimentacao_data) {
        $this->movimentacao_data = $movimentacao_data;
    }

    public function setDocumento($documento) {
        $this->documento = $documento;
    }

    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    public function getSetorResponsavel() {
        return $this->setor;
    }

    public function setSetorResponsavel($setor) {
        $this->setor = $setor;
    }

    public function getData() {
        return $this->data;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function getSetor() {
        return $this->setor;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }

    function getMov_entrada() {
        return $this->mov_entrada;
    }

    function setMov_entrada($mov_entrada) {
        $this->mov_entrada = $mov_entrada;
    }

}
