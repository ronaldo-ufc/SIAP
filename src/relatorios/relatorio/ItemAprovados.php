<?php

namespace siap\relatorios\relatorio;

class ItemAprovados extends Relatorio{
 
  function criar($requisicao_codigo) {
        
    $itens = \siap\material\models\RequisicaoItens::getByRequisicao($requisicao_codigo);
    $requisicao = \siap\material\models\Requisicao::getByCodigo($requisicao_codigo);
    if (!$itens) {
        return array('Erro', 'info', 'Não existem registros para os filtros especificados na consulta.');
    }
    
    $relatorio = new ItemAprovados();
    $relatorio->setTitulo('RELATÓRIO DE ITENS SOLICITADOS');
    $tabela =  '<h4>REQUISIÇÃO Nº '.$requisicao->getNumero()." - ".$requisicao->getDestino()->getNome();
    $tabela .=  '<br>SOLICITADO EM '.$requisicao->getDataFormatada()." POR ".$requisicao->getUsuario()->getNome()."</h4>";
    $tabela .= "<table style='width:100%; page-break-inside:auto; cellpadding=3px; cellspacing=0;'>"
            . "<tr style='background:#C1CDCD; page-break-inside:avoid; page-break-after:auto;'><td>..</td><td style='text-align: center;'>ÍTEM</td>"
            . "<td style='text-align: center;'>SOLICI</td>"
            . "<td style='text-align: center;'>ATEND</td>"
            . "<td style='text-align: center;'>%</td>"
            . "</tr>";
    $i = 1;
    $q = 0;
    $a=0;
    foreach ($itens as $item){
      $q += $item->getQuantidade();
      $a += $item->getQuantidade_atendida();
     $tabela .= "<tr>";
     $tabela .= "<td>".$i++."</td><td>". $item->getProduto()->getNome()."</td>"
             . "<td style='text-align: right;'>".$item->getQuantidade()."</td>"
             . "<td style='text-align: right;'>".$item->getQuantidade_atendida()."</td>"
             . "<td style='text-align: right;'>".(int)($item->getQuantidade_atendida() * 100 / $item->getQuantidade()) ."%</td>";
     $tabela .= "</tr>";
    }
    $tabela .= "<tr style='background:#E0EEEE;'><td colspan='2'>TOTAL</td><td style='text-align: right;'>$q</td><td style='text-align: right;'>$a</td><td style='text-align: right;'>". (int)($a*100/$q) ."%</td></tr>";
    $tabela .= "</table>";
    
    $relatorio->setContent($tabela);
    $this->header = array('Sucess', $relatorio->imprime(), NULL);
  }
     
}
