<?php
namespace siap\material\models;

class MontaBuscasItens{
  private $produtos;
  function __construct($prod){
    $this->produtos = $prod;
  }
  
  private function getLinhas(){
    if ($this->produtos){
      $linhas = null;
      foreach ($this->produtos as $produto){
        //$linhas .= "<img src='http://10.5.5.10/siap/uploads/imagem/'". $produto->getProduto_codigo().">";
         //$linhas .= "<img class='list-group-item' style='width:5%' src='http://10.5.5.10/siap/uploads/imagem/".$produto->getImagem()."'>";
        $linhas .= '<a onclick="choiceProduto('.$produto->getProduto_codigo().');" href="#" class="list-group-item">'
                      .'<img  style="width:5%" src="http://10.5.5.10/siap/uploads/imagem/'.$produto->getImagem().'"> '
                      . '<label id="'.$produto->getProduto_codigo().'" class="text-muted">'.$produto->getCodigo_ufc(). '</label> - '
                      . ' '. $produto->getNome()
                    //  '<span class="badge badge-primary badge-pill">'.$produto->getQuantidade()." ".$produto->getUnidade()->getNome().'</span> '
                  . '</a>';
      }
      return $linhas;
    }
    return null;
  }
  
  function getTabela(){
    $linhas = self::getLinhas();
    if ($linhas) {     
     $tabela = '<div class="list-group">'.$linhas.'</div>';
      return $tabela;
    }
    return '';
  }
}

