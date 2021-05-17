<?php 
namespace siap\material\models;
use siap\models\DBSiap;
use siap\auth\models\Autenticador;
include_once 'public/uteis/funcoes.php';

class Estoque {
  const ALMOXARIFADO = 22;
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
    return $stmt->errorInfo(); 
  }
  
  static function movimentacao($postParam){
    $aut = Autenticador::instanciar();    
    $sql = "INSERT INTO siap.balanco (tipo ,
                                        quantidade ,
                                        produto_codigo ,
                                        setor_id,
                                        usuario_cadastro_login,
                                        requisicao_central,
                                        movimentacao_tipo,
                                        vlr_uni)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($postParam['tipo'],
                        $postParam['quantidade'],
                        $postParam['produto_codigo'],
                        self::ALMOXARIFADO, 
                        $aut->getUsuario(),
                        $postParam['requisicao_central'],
                        $postParam['movimentacao_tipo'],
                        moedaBanco($postParam['vlr_uni'])
            ));
           
    return $stmt->errorInfo();
  }
}

