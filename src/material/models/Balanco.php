<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace siap\material\models;

use siap\models\DBSiap;

include_once "public/uteis/data.php";

/**
 * Description of Balanco
 *
 * @author Admin
 */
class Balanco {
  private $balanco_codigo;
  private $tipo;
  private $data;
  private $produto_codigo;
  private $usuario_solicitante_login;
  private $setor_id;
  private $usuario_cadastro_login;
  private $requisicao_codigo;
  private $requisicao;
  private $setor;
  private $produto;
  private $quantidade;

  private function bundle($row){
    $u = new Balanco();
    $u->setBalanco_codigo($row['balanco_codigo']);
    $u->setTipo($row['tipo']);
    $u->setData($row['data']);
    $u->setProduto_codigo($row['produto_codigo']);
    $u->setUsuario_cadastro_login($row['usuario_cadastro_login']);
    $u->setUsuario_solicitante_login($row['usuario_solicitante_login']);
    $u->setSetor_id($row['setor_id']);
    $u->setRequisicao_codigo($row['requisicao_codigo']);
    $u->setQuantidade($row['quantidade']);
    $u->setRequisicao(Requisicao::getByCodigo($row['requisicao_codigo']));
    $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));
    $u->setProduto(Produto::getById($row['produto_codigo']));
    return $u;
  }
  static function getAllAgrupadoByProduto($produto_codigo, $data_ini, $data_fim, $setor_id=22){
    $sql = "select setor_id, produto_codigo, siap.qtdRequisitadoBysetor(produto_codigo, setor_id, ?, ?) as quantidade  from siap.balanco b "
            . " where produto_codigo = ? "
            . " and data between ? and ? "
            . " and tipo = 'E' "
            . " and setor_id <> ? "
            . " group by b.setor_id, produto_codigo"
            . " order by quantidade desc";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($data_ini, $data_fim, $produto_codigo, $data_ini, $data_fim, $setor_id));
    $rows = $stmt->fetchAll();
    //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  static function getSetorConsumo($setor_id, $data_ini, $data_fim, $ordem){
    $sql = "select b.produto_codigo, setor_id, p.nome, siap.qtdRequisitadoBysetor(b.produto_codigo, setor_id, ?, ?) as quantidade  from siap.balanco b "
            . " inner join siap.produto p on b.produto_codigo = p.produto_codigo "
            . " where setor_id = ? "
            . " and data between ? and ? "
            . " and tipo = 'E' "
            . " and siap.qtdRequisitadoBysetor(b.produto_codigo, setor_id, ?, ?) > 0"
            . " group by b.produto_codigo, b.setor_id, p.nome "
            . " order by ";
    
    $sql .= $ordem == 1? " quantidade desc": " p.nome ";
    
            
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($data_ini, $data_fim, $setor_id, $data_ini, $data_fim, $data_ini, $data_fim));
    $rows = $stmt->fetchAll();
    //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }

  public function getBalanco_codigo() {
    return $this->balanco_codigo;
  }

  public function getTipo() {
    return $this->tipo;
  }

  public function getData() {
    return $this->data;
  }

  public function getProduto_codigo() {
    return $this->produto_codigo;
  }

  public function getUsuario_solicitante_login() {
    return $this->usuario_solicitante_login;
  }

  public function getSetor_id() {
    return $this->setor_id;
  }

  public function getUsuario_cadastro_login() {
    return $this->usuario_cadastro_login;
  }

  public function getRequisicao_codigo() {
    return $this->requisicao_codigo;
  }

  public function getRequisicao() {
    return $this->requisicao;
  }

  public function getSetor() {
    return $this->setor;
  }

  public function setBalanco_codigo($balanco_codigo) {
    $this->balanco_codigo = $balanco_codigo;
  }

  public function setTipo($tipo) {
    $this->tipo = $tipo;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function setProduto_codigo($produto_codigo) {
    $this->produto_codigo = $produto_codigo;
  }

  public function setUsuario_solicitante_login($usuario_solicitante_login) {
    $this->usuario_solicitante_login = $usuario_solicitante_login;
  }

  public function setSetor_id($setor_id) {
    $this->setor_id = $setor_id;
  }

  public function setUsuario_cadastro_login($usuario_cadastro_login) {
    $this->usuario_cadastro_login = $usuario_cadastro_login;
  }

  public function setRequisicao_codigo($requisicao_codigo) {
    $this->requisicao_codigo = $requisicao_codigo;
  }

  public function setRequisicao($requisicao) {
    $this->requisicao = $requisicao;
  }

  public function setSetor($setor) {
    $this->setor = $setor;
  }
  public function getProduto() {
    return $this->produto;
  }

  public function setProduto($produto) {
    $this->produto = $produto;
  }

  public function getQuantidade() {
    return $this->quantidade;
  }

  public function setQuantidade($quantidade) {
    $this->quantidade = $quantidade;
  }



}
