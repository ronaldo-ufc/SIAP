<?php
namespace siap\material\models;
use siap\models\DBSiap;

class Estoque {
  
  static function entrada($produto, $quantidade, $usuario){
    $almoxarifado = 22;
    $sql = "INSERT INTO siap.balanco (quantidade, produto_codigo, setor_id, usuario_cadastro_login) VALUES (?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($quantidade, $produto, $almoxarifado, $usuario));
    return $stmt->errorInfo();
  }
  
  
  static function saida($requisicao, $produto, $quantidade, $usuario, $solicitante, $origem, $destino){
    $sql = "select * from siap.estoque_saida(?, ?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao, $produto, $quantidade, $usuario, $solicitante, $origem, $destino));
    echo $stmt->errorInfo(); 
  }
}

