<?php

namespace siap\relatorios\relatorio;

use siap\setor\models\Setor;
use siap\setor\models\SetorResponsavel;
use siap\relatorios\relatorio\Relatorio;
include_once 'public/uteis/data.php';

class Responsavel extends Relatorio{

    function start_pdf($data_ini, $data_fim) {
        $relatorio = new Responsavel();
        if ($data_fim <= $data_ini) {
            return array('Erro', 'info', 'Datas inválidas');
        } 
        $relatorio->setTitulo('RELATÓRIO DOS BENS PERMANENTES POR RESPONSÁVEL', 'Período '.formatoDMY($data_ini).' a '.formatoDMY($data_fim));
        foreach (Setor::getAllAtivos() as $setor) {
            $responsavel = SetorResponsavel::getResponsavelBySetor($setor->getSetor_id(), $data_ini, $data_fim);
            if ($responsavel){
                $nome_do_responsavel = $responsavel->getResponsavel()->getNome();
            }
            $ativos = \siap\produto\models\Ativos::getAllBySetor($setor->getSetor_id());
            $tamanho = retornaTamanhoLista($ativos);
            $tabel .= '<table style="width:100%; font-size:13px; page-break-inside:auto; cellpadding=3px; cellspacing=0;">
            <thead class="bg-primary" style="width:12px; display:table-header-group">
                <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;"><th >Patrimônio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Fabricante</th><th >Est. Conservação</th></tr>
            </thead>
            <tbody>';
            foreach ($ativos as $ativo) {
                $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                        . '<td>' . $ativo->getPatrimonio() . '</td>'
                        . '<td>' . $ativo->getNome() . '</td>'
                        . '<td>' . $ativo->getCategoria()->getNome() . '</td>'
                        . '<td>' . $ativo->getModelo()->getNome() . '</td>'
                        . '<td>' . $ativo->getFabricante()->getNome() . '</td>'
                        . '<td>' . $ativo->getConservacao()->getNome() . '</td>'
                        . '</tr>';
            }
            $tabel .= '</tbody>'
                    . '<div style="text-align:left; page-break-inside:avoid; page-break-after:auto;"><br /><b>TOTAL: ' . $tamanho . '</b></div>
        </table>';
        }
        $relatorio->setContent($tabel);
        $this->header = array('Sucess', $relatorio->imprime(), NULL);
        return true;
    }
}
