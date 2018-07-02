<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace siap\produto\models;
use siap\models\DBSiap;
/**
 * Description of Ativos
 *
 * @author Ronaldo
 */
class Ativos {
  private $patrimonio;
  private $nome;
  private $data_atesto;
  private $nota_fiscal;
  private $fornecedor;
  private $descricao;
  private $observacao;
  private $foto;
  private $fabricante_id;
  private $modelo_id;
  private $aquisicao_id;
  private $status_id;
  private $setor_id;
  private $conservacao_id;
  private $template_id;
  private $usuario_id;
  private $fabricante;
  private $modelo;
  private $aquisicao;
  private $status;
  private $setor;
  private $conservacao;
  private $template;
  private $usuario;
  
  private function bundle($row){
    $u = new Ativos();
    $u->setPatrimonio($row['patrimonio']);
    $u->setNome($row['nome']);
    $u->setData_atesto($row['data_atesto']);
    $u->setNota_fiscal($row['nota_fiscal']);
    $u->setFornecedor($row['fornecedor']);
    $u->setDescricao($row['descricao']);
    $u->setObservacao($row['observacao']);
    $u->setFoto($row['foto']);
    $u->setFabricante_id($row['fabricante_id']);
    $u->setModelo_id($row['modelo_id']);
    $u->setStatus_id($row['status_id']);
    $u->setSetor_id($row['setor_id']);
    $u->setConservacao_id($row['conservacao_id']);
    $u->setTemplate_id($row['template_id']);
    $u->setUsuario_id($row['usuario_id']);
    
    $u->setFabricante(\siap\cadastro\models\Fabricante::getById($row['fabricante_id']));
    $u->setModelo(\siap\cadastro\models\Modelo::getById($row['modelo_id']));
    $u->setSetor(\siap\setor\models\Setor::getById($row['setor_id']));
    $u->setStatus(\siap\cadastro\models\Status::getById($row['status_id']));
    $u->setAquisicao(\siap\cadastro\models\Aquisicao::getById($row['aquisicao_id']));
    $u->setConservacao(\siap\cadastro\models\EConservacao::getById($row['conservacao_id']));
    return $u;
  }
  
  static function create($patrimonio,
                         $nome ,  
                         $data_atesto,  
                         $nota_fiscal ,  
                         $fornecedor ,  
                         $descricao ,  
                         $observacao ,  
                         $foto ,  
                         $fabricante_id ,  
                         $modelo_id ,  
                         $aquisicao_id ,  
                         $status_id ,  
                         $setor_id ,  
                         $conservacao_id,
                         $template_id, 
                         $usuario)
  {
    $sql = "INSERT INTO siap.ativo (patrimonio, "
                              . "nome ,  "
                              . "data_atesto,  "
                              . "nota_fiscal ,  "
                              . "fornecedor , "
                              . "descricao ,  "
                              . "observacao ,  "
                              . "foto ,  "
                              . "fabricante_id , "
                              . "modelo_id ,  "
                              . "aquisicao_id ,  "
                              . "status_id ,  "
                              . "setor_id ,  "
                              . "conservacao_id, "
                              . "template_id, "
                              . "usuario_id) "
            . " VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($patrimonio,
                        strtoupper(tirarAcentos($nome)) ,  
                        $data_atesto,  
                        strtoupper(tirarAcentos($nota_fiscal)) ,  
                        strtoupper(tirarAcentos($fornecedor)) ,  
                        strtoupper(tirarAcentos($descricao)) ,  
                        strtoupper(tirarAcentos($observacao)) ,  
                        $foto ,  
                        $fabricante_id ,  
                        $modelo_id , 
                        $aquisicao_id ,  
                        $status_id ,  
                        $setor_id ,  
                        $conservacao_id,
                        $template_id,
                        $usuario
    ));
    return $stmt->errorInfo();
  }

  static function getQtdById($template_id){
    $sql = "SELECT count(*) as quantidade FROM siap.ativo where template_id = ?";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($template_id));
    $row = $stmt->fetch();
    if ($row == null){
      return 0;
    }
    return $row['quantidade'];
  }
  
  static function getAll(){
    $sql = "select * from siap.ativo order by cadastro desc";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  public function getPatrimonio() {
    return $this->patrimonio;
  }

  public function getNome() {
    return $this->nome;
  }

  public function getData_atesto() {
    return $this->data_atesto;
  }

  public function getNota_fiscal() {
    return $this->nota_fiscal;
  }

  public function getFornecedor() {
    return $this->fornecedor;
  }

  public function getDescricao() {
    return $this->descricao;
  }

  public function getObservacao() {
    return $this->observacao;
  }

  public function getFoto() {
    return $this->foto;
  }

  public function getFabricante_id() {
    return $this->fabricante_id;
  }

  public function getModelo_id() {
    return $this->modelo_id;
  }

  public function getAquisicao_id() {
    return $this->aquisicao_id;
  }

  public function getStatus_id() {
    return $this->status_id;
  }

  public function getSetor_id() {
    return $this->setor_id;
  }

  public function getConservacao_id() {
    return $this->conservacao_id;
  }

  public function getTemplate_id() {
    return $this->template_id;
  }

  public function setPatrimonio($patrimonio) {
    $this->patrimonio = $patrimonio;
  }

  public function setNome($nome) {
    $this->nome = $nome;
  }

  public function setData_atesto($data_atesto) {
    $this->data_atesto = $data_atesto;
  }

  public function setNota_fiscal($nota_fiscal) {
    $this->nota_fiscal = $nota_fiscal;
  }

  public function setFornecedor($fornecedor) {
    $this->fornecedor = $fornecedor;
  }

  public function setDescricao($descricao) {
    $this->descricao = $descricao;
  }

  public function setObservacao($observacao) {
    $this->observacao = $observacao;
  }

  public function setFoto($foto) {
    $this->foto = $foto;
  }

  public function setFabricante_id($fabricante_id) {
    $this->fabricante_id = $fabricante_id;
  }

  public function setModelo_id($modelo_id) {
    $this->modelo_id = $modelo_id;
  }

  public function setAquisicao_id($aquisicao_id) {
    $this->aquisicao_id = $aquisicao_id;
  }

  public function setStatus_id($status_id) {
    $this->status_id = $status_id;
  }

  public function setSetor_id($setor_id) {
    $this->setor_id = $setor_id;
  }

  public function setConservacao_id($conservacao_id) {
    $this->conservacao_id = $conservacao_id;
  }

  public function setTemplate_id($template_id) {
    $this->template_id = $template_id;
  }
  public function getUsuario_id() {
    return $this->usuario_id;
  }

  public function setUsuario_id($usuario_id) {
    $this->usuario_id = $usuario_id;
  }


  public function getFabricante() {
    return $this->fabricante;
  }

  public function getModelo() {
    return $this->modelo;
  }

  public function setFabricante($fabricante) {
    $this->fabricante = $fabricante;
  }

  public function setModelo($modelo) {
    $this->modelo = $modelo;
  }

  public function getAquisicao() {
    return $this->aquisicao;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getSetor() {
    return $this->setor;
  }

  public function getConservacao() {
    return $this->conservacao;
  }

  public function setAquisicao($aquisicao) {
    $this->aquisicao = $aquisicao;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function setSetor($setor) {
    $this->setor = $setor;
  }

  public function setConservacao($conservacao) {
    $this->conservacao = $conservacao;
  }

}
