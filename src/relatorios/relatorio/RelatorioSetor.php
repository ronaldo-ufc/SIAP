<?php

namespace siap\relatorios\relatorio;

use siap\models\DBSiap;
use siap\setor\models\Setor;
use Dompdf\Dompdf;

class RelatorioSetor {

    private $nome;
    private $setor;
    private $setor_id;
    private $status;

    static function bundle($setor_id, $nome, $status = null) {
        $u = new RelatorioSetor();
        $u->setSetor_id($setor_id);
        $u->setNome($nome);
        $u->setStatus($status);
        return $u;
    }

    function geraHtml() {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("d-m-Y");
        $hora = date('H:i:s');
        $ativos = \siap\produto\models\Ativos::getAllBySetor($this->setor_id, 'patrimonio asc', $this->status);
        $tamanho = retornaTamanhoLista($ativos);
        if ($tamanho == 0) {
            return array('Erro', 'info', 'Não existem registros cadastrados para este setor.');
        } else {
            $header = '<style>th, td { border-bottom: 1px solid black } .relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="../assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEAR&Aacute;<br />CAMPUS DE CRATE&Uacute;S<br />SISTEMA DE ALMOXARIFADO E PATRIM&Ocirc;NIO - SIAP</b><br />
            EMITIDO EM ' . $data . ' ' . $hora . '</p>'
                    . '</div><br />';
            $titulo = '<div style="text-align:center;"><p><b>RELAT&Oacute;RIO DOS BENS PERMANENTES NO SETOR: ' . $this->getNome() . '</b></p></div><br />';
            $tabel = '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                    . '<div class="panel panel-default">'
                    . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;"></div>'
                    . '<div class="panel-body" style="padding:10px;">'
                    . '<table style="width:100%; font-size:13px; page-break-inside:auto; cellpadding=3px; cellspacing=0;">
                        <thead class="bg-primary" style="width:12px; display:table-header-group">
                            <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;"><th >Patrim&ocirc;nio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Fabricante</th><th >Estado</th><th >Status</th></tr>
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
                        . '<td>' . $ativo->getStatus()->getNome() . '</td>'
                        . '</tr>';
            }
//        <tr style="background:#FFF;"><th >Patrimônio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Fabricante</th><th >Est. Conservação</th></tr>
            $tabel .= '</tbody>'
                    . '<div style="text-align:left; page-break-inside:avoid; page-break-after:auto;"><br /><b>TOTAL: ' . $tamanho . '</b></div>
                    </table>'
                    . '</div>'
                    . '</div>'
                    . '</div><br /></body>';
//        $footer = '<footer style="position:absolute;bottom:0;width:100%;" ><p>Posted by: Hege Refsnes</p><p>Contact information: <a href="mailto:someone@example.com">someone@example.com</a>.</p></footer>';
            $header .= $titulo;
            $header .= $tabel;
//        $header .= $footer;
            return array('Sucess', $header, NULL);
        }
    }
    function geraXls() {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("d-m-Y");
        $hora = date('H:i:s');
        $ativos = \siap\produto\models\Ativos::getAllBySetor($this->setor_id, 'patrimonio asc', $this->status);
        $tamanho = retornaTamanhoLista($ativos);
        if ($tamanho == 0) {
            return array('Erro', 'info', 'Não existem registros cadastrados para este setor.');
        } else {
            $header = '<style>th, td { border-bottom: 1px solid black } .relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="../assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEAR&Aacute;<br />CAMPUS DE CRATE&Uacute;S<br />SISTEMA DE ALMOXARIFADO E PATRIM&Ocirc;NIO - SIAP</b><br />
            EMITIDO EM ' . $data . ' ' . $hora . '</p>'
                    . '</div><br />';
            $titulo = '<div style="text-align:center;"><p><b>RELAT&Oacute;RIO DOS BENS PERMANENTES NO SETOR: ' . $this->getNome() . '</b></p></div><br />';
            $tabel = '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                    . '<div class="panel panel-default">'
                    . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;"></div>'
                    . '<div class="panel-body" style="padding:10px;">'
                    . '<table style="width:100%; font-size:13px; page-break-inside:auto; cellpadding=3px; cellspacing=0;">
                        <thead class="bg-primary" style="width:12px; display:table-header-group">
                            <tr style="background:#FFF; page-break-inside:avoid; page-break-after:auto;">
                                <th >Patrim&ocirc;nio</th>
                                <th >Nome</th>
                                <th >Categoria</th>
                                <th >Modelo</th>
                                <th >Fabricante</th>
                                <th >Empenho</th>
                                <th >Estado</th>
                                <th >Status</th>
                                <th >Setor</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($ativos as $ativo) {
                $tabel .= '<tr style="width:12px; page-break-inside:avoid; page-break-after:auto;">'
                        . '<td>' . $ativo->getPatrimonio() . '</td>'
                        . '<td>' . $ativo->getNome() . '</td>'
                        . '<td>' . $ativo->getCategoria()->getNome() . '</td>'
                        . '<td>' . $ativo->getModelo()->getNome() . '</td>'
                        . '<td>' . $ativo->getFabricante()->getNome() . '</td>'
                        . '<td>' . $ativo->getEmpenho() . '</td>'
                        . '<td>' . $ativo->getConservacao()->getNome() . '</td>'
                        . '<td>' . $ativo->getStatus()->getNome() . '</td>'
                        . '<td>' . $this->getNome() . '</td>'
                        . '</tr>';
            }
//        <tr style="background:#FFF;"><th >Patrimônio</th><th >Nome</th><th >Categoria</th><th >Modelo</th><th >Fabricante</th><th >Est. Conservação</th></tr>
            $tabel .= '</tbody>'
                    . '<div style="text-align:left; page-break-inside:avoid; page-break-after:auto;"><br /><b>TOTAL: ' . $tamanho . '</b></div>
                    </table>'
                    . '</div>'
                    . '</div>'
                    . '</div><br /></body>';
//        $footer = '<footer style="position:absolute;bottom:0;width:100%;" ><p>Posted by: Hege Refsnes</p><p>Contact information: <a href="mailto:someone@example.com">someone@example.com</a>.</p></footer>';
            $header .= $titulo;
            $header .= $tabel;
//        $header .= $footer;
            return array('Sucess', $header, NULL);
        }
    }

    function getNome() {
        return $this->nome;
    }

    function getSetor() {
        return $this->setor;
    }

    function getSetor_id() {
        return $this->setor_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }

    function setSetor_id($setor_id) {
        $this->setor_id = $setor_id;
    }
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
