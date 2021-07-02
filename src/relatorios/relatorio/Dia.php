<?php

namespace siap\relatorios\relatorio;

use siap\relatorios\models\relatorioDIA;

include_once '../../../public/uteis/funcoes.php';

class Dia {

    function geraXls() {
        $relatorio_dia = new relatorioDIA();
        
        $grupos = $relatorio_dia->getGrupos();
        
        $table = $this->getCabecalho() . "<body>";
        foreach ($grupos as $grupo) {
            $ano = 2019;
            while ($ano <= date('Y')) {   
                $mes = 1;
                while ($mes <= 12) {
                    $result = relatorioDIA::getAll($grupo->getGrupo_codigo(), $grupo->getSetor_ordem(), $mes, $ano);
                    $media = $result[1]== null? 0 : $result[1];
                    $quantidade = $result[0] == null? 0: $result[0];
                    
                    $table .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                            . '<td>' . upperInitial($grupo->getGrupo()) . '</td>'
                            . '<td>' . $media . '</td>'
                            . '<td>' . $quantidade . '</td>'
                            . '<td>' . upperInitial($grupo->getSetor_ordem()) . '</td>'
                            . '<td>' . upperInitial($grupo->getSetor_nome()) . '</td>'
                            . '<td>' . $grupo->getSetor_sigla() . '</td>'
                            . '<td>' . upperInitial($grupo->getBloco()) . '</td>'
                            . '<td>' . mesAbreviado($mes) . '</td>'
                            . '<td>' . upperInitial($mes) . '</td>'
                            . '<td>' . upperInitial($ano) . '</td>'
                            . '</tr>';
                    
                    $mes += 1;
                }
                $ano += 1;
            }
        }
        $table .= '</body>     
        </table>';
        return print $table;
    }

    public function getCabecalho() {
        return '<table>
                <thead>
                    <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;">
                        <th >Tipo</th>
                        <th >Media de Valor</th>
                        <th >Quantidade</th>
                        <th >Ordem Setor</th>
                        <th >Setor</th>
                        <th >Abreviacao</th>
                        <th >Bloco</th>
                        <th >Mes abreviado</th>
                        <th >Ordem do Mes</th>
                        <th >Ano</th>
                    </tr>
                </thead>';
    }

}
