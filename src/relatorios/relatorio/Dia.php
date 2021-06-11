<?php

namespace siap\relatorios\relatorio;

use siap\relatorios\models\relatorioDIA;

class Dia {

    function geraXls() {
        $relatorio_dia = new relatorioDIA();
        $materiais = $relatorio_dia->getAll();
        $table = $this->getCabecalho()."<body>";
        foreach ($materiais as $material) {
            $table .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                    . '<td>' . $material->getGrupo() . '</td>'
                    . '<td>' . $material->getMedia_material() . '</td>'
                    . '<td>' . $material->getQuantidade() . '</td>'
                    . '<td>' . $material->getSetor_ordem() . '</td>'
                    . '<td>' . $material->getSetor_nome() . '</td>'
                    . '<td>' . $material->getSetor_sigla() . '</td>'
                    . '<td>' . $material->getNumero_mes() . '</td>'
                    . '<td>' . $material->getAno() . '</td>'
                    . '</tr>';
        }
        $table .= '</body>     
        </table>';           
       return  print $table;
    }
                        
    public function getCabecalho(){
        return  '<table>
                <thead>
                    <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;">
                        <th >Tipo</th>
                        <th >Media de Valor</th>
                        <th >Quantidade</th>
                        <th >Ordem Setor</th>
                        <th >Setor</th>
                        <th >Abreviacao</th>
                        <th >Ordem do Mes</th>
                        <th >Ano</th>
                    </tr>
                </thead>';
    }



}
