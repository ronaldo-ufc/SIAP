<?php

namespace siap\relatorios\relatorio;

use siap\relatorios\models\relatorioDIA;

include_once '../../../public/uteis/funcoes.php';

class Dia {

    function geraXls() {
        $relatorio_dia = new relatorioDIA();
        $materiais = $relatorio_dia->getAll();
        $table = $this->getCabecalho() . "<body>";
        foreach ($materiais as $material) {
            $table .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                    . '<td>' . upperInitial($material->getGrupo()) . '</td>'
                    . '<td>' . upperInitial($material->getMedia_material()) . '</td>'
                    . '<td>' . upperInitial($material->getQuantidade()) . '</td>'
                    . '<td>' . upperInitial($material->getSetor_ordem()) . '</td>'
                    . '<td>' . upperInitial($material->getSetor_nome()) . '</td>'
                    . '<td>' . $material->getSetor_sigla() . '</td>'
                    . '<td>' . upperInitial($material->getBloco()) . '</td>'
                    . '<td>' . mesAbreviado($material->getNumero_mes()) . '</td>'
                    . '<td>' . upperInitial($material->getNumero_mes()) . '</td>'
                    . '<td>' . upperInitial($material->getAno()) . '</td>'
                    . '</tr>';
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
