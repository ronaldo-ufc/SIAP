<?php

namespace siap\relatorios\relatorio;

class ItemConsumo extends Relatorio{
 
  function criar($produto_codigo, $data_ini, $data_fim) {
        
    $itens = \siap\material\models\Balanco::getAllAgrupadoByProduto($produto_codigo, $data_ini, $data_fim);
    $produto = \siap\material\models\Produto::getById($produto_codigo);
    
    if (!$itens or !$produto){
      return false;
    }
    $relatorio = new ItemAprovados();
    $relatorio->setTitulo('RELATÓRIO DE CONSUMO', 'PERÍODO '.formatoDMY($data_ini). ' A '.formatoDMY($data_fim));
    $tabela =  '<h4>PRODUTO: '.$produto->getNome();

    $tabela .= "<table style='width:100%; page-break-inside:auto; cellpadding=3px; cellspacing=0;'>"
            . "<tr style='background:#C1CDCD; page-break-inside:avoid; page-break-after:auto;'><td>..</td><td style='text-align: center;'>SETOR</td>"
            . "<td style='text-align: center;'>QUANTIDADE</td>"
            . "</tr>";
    $t = 0;
    $i = 1;
    foreach ($itens as $item){
      $t += $item->getQuantidade();

     $tabela .= "<tr>";
     $tabela .= "<td>".$i++."</td><td>". $item->getSetor()->getNome()."</td>"
             . "<td style='text-align: right;'>".$item->getQuantidade()."</td>";

     $tabela .= "</tr>";
    }
    $tabela .= "<tr style='background:#E0EEEE;'><td colspan='2'>TOTAL</td><td style='text-align: right;'>". $t ."</td></tr>";
    $tabela .= "</table>";
    
    $relatorio->setContent($tabela);
    $this->header = array('Sucess', $relatorio->imprime(), NULL);
    return true;
  }
     
}
