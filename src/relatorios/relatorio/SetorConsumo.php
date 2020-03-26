<?php

namespace siap\relatorios\relatorio;
use siap\relatorios\relatorio\Relatorio;
include_once 'public/uteis/data.php';

class SetorConsumo extends Relatorio{
 
  function criar($params) {
    $relatorio = new SetorConsumo();
    $itens = \siap\material\models\Balanco::getSetorConsumo($params['setor_id'], $params['dataInicio'], $params['dataFim'], $params['ordem']);
    if (!$itens){
      return false;
    }
    $relatorio->setTitulo('Materiais Requisitados <br><small> '.$itens[0]->getSetor()->getNome()."</small>", 'Período '.formatoDMY($params['dataInicio']).' a '.formatoDMY($params['dataFim']));

    $tblp = "<tr><th></th><th align='left'>ÍTEM</th><th align='left'>MEDIDA</th><th align='center'>QTDE.</th></tr>";
    $i = 1;
    $total = 0;
    foreach ($itens as $item){
      $valor = ($item->getQuantidade() > 0 and $item->getQuantidade() < 10)? '0'.$item->getQuantidade() : $item->getQuantidade();
      $tblp .= "<tr><td>".$i++.".</td><td>".$item->getProduto()->getNome()."</td><td>".$item->getProduto()->getUnidade()->getNome()."</td><td align='center'>".$valor."</td></tr>";
      $total += $item->getQuantidade();
    }
    $tblp .= "<tr><td colspan='3'><strong>TOTAL</strong></td><td align='center'><strong>".$total."</strong></td></tr>";    
    $tabela .= ' <table class="tabela">'.$tblp.'</table>';   
    
        
    $relatorio->setContent($tabela);
    $this->header = array('Sucess', $relatorio->imprime(), NULL);
    return true;
  }
     
}