<?php

namespace siap\relatorios\relatorio;

use siap\models\DBSiap;
use siap\setor\models\Setor;
use siap\produto\models\Movimentacao;
use Dompdf\Dompdf;

class MovimentacaoBemSetor {

    static function bundle() {
        $u = new MovimentacaoBemSetor();
        return $u;
    }

    function start_pdf($data_ini, $data_fim) {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("d-m-Y");
        $hora = date('H:i:s');
        if ($data_ini == "" && $data_fim == "") {
            $movimentacoes = Movimentacao::getAll();
        } else if ($data_ini != '' && $data_fim == '') {
            $movimentacoes = Movimentacao::getMovData_ini($data_ini);
        } else if ($data_ini == '' && $data_fim != '') {
            $movimentacoes = Movimentacao::getMovData_fim($data_fim);
        } else {
            $movimentacoes = Movimentacao::getMovData_range($data_ini, $data_fim);
        }
        if (retornaTamanhoLista($movimentacoes) == 0) {
            return array('Erro', 'info', 'Não existem registros para os filtros especificados na consulta.');
        } else {

            $header = '<style>th, td { border-bottom: 1px solid black } .relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEARÁ<br />CAMPUS DE CRATEÚS<br />SISTEMA DE ALMOXARIFADO E PATRIMÔNIO - SIAP</b><br />
            EMITIDO EM ' . $data . ' ' . $hora . '</p>'
                    . '</div><br />';
            $titulo = '<div style="text-align:center;"><p><b>RELATÓRIO MOVIMENTAÇÃO DE BENS POR SETOR</b></p></div><br />';
            $tabel = '';
            foreach (Setor::getAll() as $setor) {
                $aux = movFiltro($movimentacoes, $setor->getSetor_id());
                $tamanho = retornaTamanhoLista($aux);
                if ($tamanho == 0) {
                    continue;
                }
                $tabel .= '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                        . '<div class="panel panel-default">'
                        . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">' . $setor->getNome() . '</div>'
                        . '<div class="panel-body" style="padding:10px;">';
                $mov_entrada = movEntradaFiltro($aux);
                $mov_saida = movSaidaFiltro($aux);
                $tam_entrada = retornaTamanhoLista($mov_entrada);
                $tam_saida = retornaTamanhoLista($mov_saida);
                if ($tam_entrada == 0) {
                    ;
                } else {
                    $tabel .= '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;"> MOVIMENTAÇÕES DE ENTRADA </div>'
                            . '<table style="width:100%; font-size:13px; page-break-inside:auto; cellpadding=3px; cellspacing=0;">
                <thead class="bg-primary" style="width:12px; display:table-header-group">
                    <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;"><th >Patrimônio</th><th >Data</th><th >Observação</th></tr>
                </thead>
                <tbody>';
                    foreach ($mov_entrada as $mov) {
                        $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                                . '<td>' . $mov->getPatrimonio() . '</td>'
                                . '<td>' . formData($mov->getData()) . '</td>'
                                . '<td>' . $mov->getObservacao() . '</td>'
                                . '</tr>';
                    }
                    $tabel .= '</tbody>'
                            . '<div style="text-align:left; page-break-inside:avoid; page-break-after:auto;"><br /><b>TOTAL: ' . $tam_entrada . '</b></div>
            </table><br />';
                }
                if ($tam_saida == 0) {
                    ;
                } else {
                    $tabel .= '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;"> MOVIMENTAÇÕES DE SAÍDA </div>'
                            . '<table style="width:100%; font-size:13px; page-break-inside:auto" cellpadding="3px" cellspacing="0">
                <thead class="bg-primary" style="width:12px; display:table-header-group">
                    <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;"><th >Patrimônio</th><th >Data</th><th >Observação</th></tr>
                </thead>
                <tbody>';
                    foreach ($mov_saida as $mov) {
                        $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                                . '<td>' . $mov->getPatrimonio() . '</td>'
                                . '<td>' . formData($mov->getData()) . '</td>'
                                . '<td>' . $mov->getObservacao() . '</td>'
                                . '</tr>';
                    }
                    $tabel .= '</tbody>'
                            . '<div style="text-align:left; page-break-inside:avoid; page-break-after:auto;"><br /><b>TOTAL: ' . $tam_saida . '</b></div>
            </table><br />';
                }
                $tabel .= '<div style="text-align:left;"><b>TOTAL DE MOVIMENTAÇÕES NO SETOR: ' . $tamanho . '</b></div>'
                        . '</div>'
                        . '</div>'
                        . '</div><br />';
            }
            $tabel .= '</body>';

            $header .= $titulo;
            $header .= $tabel;
            return array('Sucess', $header, NULL);
        }
    }

}
