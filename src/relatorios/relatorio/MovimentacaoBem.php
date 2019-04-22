<?php

namespace siap\relatorios\relatorio;

use siap\models\DBSiap;
use siap\produto\models\Movimentacao;
use siap\produto\models\Ativos;
use siap\setor\models\Setor;
use Dompdf\Dompdf;

class MovimentacaoBem {

    static function bundle() {
        $u = new MovimentacaoBem();
        return $u;
    }

    function start_pdf($patrimonio) {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("d-m-Y");
        $hora = date('H:i:s');
        $movimentacoes = Movimentacao::getAllByPatrimonioEntradaOrderById($patrimonio);
        $tamanho = retornaTamanhoLista($movimentacoes);
        $header = '<style>th, td { border-bottom: 1px solid black } .relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEARÁ<br />CAMPUS DE CRATEÚS<br />SISTEMA DE ALMOXARIFADO E PATRIMÔNIO - SIAP</b><br />
            EMITIDO EM ' . $data . ' ' . $hora . '</p>'
                . '</div><br />';
        $titulo = '<div style="text-align:center;"><p><b>RELATÓRIO MOVIMENTAÇÕES DO BEM</b></p></div><br />';
        $tabel = '';
        $tabel .= '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                . '<div class="panel panel-default">'
                . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">' . Ativos::getById($patrimonio)->getNome() . '</div>'
                . '<div class="panel-body" style="padding:10px;">';
        $tabel .= '<table style="width:100%; font-size:13px; page-break-inside:auto; cellpadding=3px; cellspacing=0;">
                <thead class="bg-primary" style="width:12px; display:table-header-group">
                    <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto; width:100%;"><th >Patrimônio</th><th >Setor Origem</th><th >Data Movimentação</th><th >Observação</th><th >Setor Destino</th></tr>
                </thead>
                <tbody>';
        $i = 0;
        if ($tamanho == 1) {
            $tabel .= '<tr style="width:100%; page-break-inside:avoid; page-break-after:auto;">'
                    . '<td>' . $movimentacoes[0]->getPatrimonio() . '</td>'
                    . '<td>   </td>'
                    . '<td>' . formData($movimentacoes[0]->getData()) . '</td>'
                    . '<td>' . $movimentacoes[0]->getObservacao() . '</td>'
                    . '<td>' . Setor::getById($movimentacoes[0]->getSetor_id())->getNome() . '</td>'
                    . '</tr>';
        } else if($tamanho == 2){
            $tabel .= '<tr style="width:100%; page-break-inside:avoid; page-break-after:auto;">'
                    . '<td>' . $movimentacoes[0]->getPatrimonio() . '</td>'
                    . '<td>   </td>'
                    . '<td>' . formData($movimentacoes[0]->getData()) . '</td>'
                    . '<td>' . $movimentacoes[0]->getObservacao() . '</td>'
                    . '<td>' . Setor::getById($movimentacoes[0]->getSetor_id())->getNome() . '</td>'
                    . '</tr>';
            $tabel .= '<tr style="width:100%; page-break-inside:avoid; page-break-after:auto;">'
                    . '<td>' . $movimentacoes[1]->getPatrimonio() . '</td>'
                    . '<td>' . Setor::getById($movimentacoes[0]->getSetor_id())->getNome() . '</td>'
                    . '<td>' . formData($movimentacoes[1]->getData()) . '</td>'
                    . '<td>' . $movimentacoes[1]->getObservacao() . '</td>'
                    . '<td>' . Setor::getById($movimentacoes[1]->getSetor_id())->getNome() . '</td>'
                    . '</tr>';
        } else {
            for ($i = 0; $i < ($tamanho) - 1; $i++) {
                if ($i == 0) {
                    $tabel .= '<tr style="width:100%; page-break-inside:avoid; page-break-after:auto;">'
                            . '<td>' . $movimentacoes[$i]->getPatrimonio() . '</td>'
                            . '<td>    </td>'
                            . '<td>' . formData($movimentacoes[$i]->getData()) . '</td>'
                            . '<td>' . $movimentacoes[$i]->getObservacao() . '</td>'
                            . '<td>' . Setor::getById($movimentacoes[$i + 1]->getSetor_id())->getNome() . '</td>'
                            . '</tr>';
                } else {
                    $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                            . '<td>' . $movimentacoes[$i]->getPatrimonio() . '</td>'
                            . '<td>' . Setor::getById($movimentacoes[$i]->getSetor_id())->getNome() . '</td>'
                            . '<td>' . formData($movimentacoes[$i]->getData()) . '</td>'
                            . '<td>' . $movimentacoes[$i]->getObservacao() . '</td>'
                            . '<td>' . Setor::getById($movimentacoes[$i + 1]->getSetor_id())->getNome() . '</td>'
                            . '</tr>';
                }
            }
            $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                            . '<td>' . $movimentacoes[($tamanho) - 2]->getPatrimonio() . '</td>'
                            . '<td>' . Setor::getById($movimentacoes[($tamanho) - 2]->getSetor_id())->getNome() . '</td>'
                            . '<td>' . formData($movimentacoes[($tamanho) - 2]->getData()) . '</td>'
                            . '<td>' . $movimentacoes[$i]->getObservacao() . '</td>'
                            . '<td>' . Setor::getById($movimentacoes[($tamanho) - 1]->getSetor_id())->getNome() . '</td>'
                            . '</tr>';
        }
        $tabel .= '</tbody>'
                . '</table><br />';
        $tabel .= '<div style="text-align:left;"><b>TOTAL DE MOVIMENTAÇÕES: ' . $tamanho . '</b></div>'
                . '</div>'
                . '</div>'
                . '</div><br />';

        $tabel .= '</body>';

        $header .= $titulo;
        $header .= $tabel;
        return $header;
    }

}
