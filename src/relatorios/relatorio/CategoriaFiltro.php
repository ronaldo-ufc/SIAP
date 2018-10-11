<?php

namespace siap\relatorios\relatorio;

use siap\models\DBSiap;
use \siap\cadastro\models\Categoria;
use \siap\produto\models\Ativos;
use Dompdf\Dompdf;

class CategoriaFiltro {

    static function bundle() {
        $u = new CategoriaFiltro();
        return $u;
    }

    function start_pdf($nome, $categoria, $modelo, $atesto, $status, $conservacao, $setor, $fornecedor, $nota_fiscal, $empenho, $descricao) {
        $data = date("d-m-Y");
        $hora = date('H:i:s');
        $ativos = Ativos::Filtrar($nome, $categoria, $modelo, $atesto, $status, $conservacao, $setor, $fornecedor, $nota_fiscal, $empenho, $descricao);
        $header = '<style>.relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="assets/img/brasao_ufc.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEARÁ<br />CAMPUS DE CRATEÚS<br />SISTEMA DE ALMOXARIFADO E PATRIMÔNIO - SIAP</b><br />
            EMITIDO EM ' . $data . ' ' . $hora . '</p>'
                . '</div><br />';
        $titulo = '<div style="text-align:center;"><p><b>RELATÓRIO DOS BENS PERMANENTES POR CATEGORIA</b></p></div><br />';
        if (sizeof($ativos) == 0) {
            $header .= $titulo;
            $header .= '<div style="text-align: center;"><p><b>NÃO FOI ENCONTRADO NENHUM BEM COM ESTES FILTROS !</b></p></div>';
            $html = '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                    . '<div class="panel panel-default">'
                    . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">Filtros</div>'
                    . '<div class="panel-body" style="padding:10px; font-size:12px;">';
            if ($nome != '')
                $html .= '<span><b> | Nome: </b>' . strtoupper($nome) . '</span>';
            if ($categoria != "n")
                $html .= '<span><b> | Categoria: </b>' . Categoria::getById($categoria)->getNome() . '</span>';
            if ($modelo != "n")
                $html .= '<span><b> | Modelo: </b>' . \siap\cadastro\models\Modelo::getById($modelo)->getNome() . '</span>';
            if ($atesto != '')
                $html .= '<span><b> | Data de Atesto: </b>' . $atesto . '</span>';
            if ($status != 'n')
                $html .= '<span><b> | Status: </b>' . \siap\cadastro\models\Status::getById($status)->getNome() . '</span>';
            if ($conservacao != 'n')
                $html .= '<span><b> | Estado de Conservação: </b>' . \siap\cadastro\models\EConservacao::getById($conservacao)->getNome() . '</span>';
            if ($setor != 'n')
                $html .= '<span><b> | Setor: </b>' . \siap\setor\models\Setor::getById($status)->getNome() . '</span>';
            if ($fornecedor != '')
                $html .= '<span><b> | Fornecedor: </b>' .strtoupper ($fornecedor) . '</span>';
            if ($nota_fiscal != '')
                $html .= '<span><b> | Nota Fiscal: </b>' . $nota_fiscal . '</span>';
            if ($empenho != '')
                $html .= '<span><b> | Empenho: </b>' . strtoupper($empenho) . '</span>';
            if ($descricao != '')
                $html .= '<span><b> | Descrição: </b>' . strtoupper($descricao) . '</span>';
            $html .= '</div>'
                    . '</div>'
                    . '</div><br />';
            $header .= $html;
            return $header;
        } else {
            $tabel = '';
            foreach (Categoria::getAll() as $categoria) {
                $aux = categoriaFiltro($ativos, $categoria->getCategoria_id());
                $tamanho = retornaTamanhoLista($aux);
                if ($tamanho == 0) {
                    continue;
                }
                $tabel .= '<div class="container" style="border:2px solid #f0f0f0; border-radius:10px;font-family:sans-serif;">'
                        . '<div class="panel panel-default">'
                        . '<div class="panel-heading" style="width:100%;display:block;background:#f0f0f0;padding:5px 10px;">' . $categoria->getNome() . '</div>'
                        . '<div class="panel-body" style="padding:10px;">'
                        . '<table class="relatorio" style="width:100%; font-size:13px;" cellpadding="3px" cellspacing="0">
                <thead class="bg-primary" style="width:12px;">
                    <tr style="background:#FFF;"><th >Patrimônio</th><th >Nome</th><th >Setor</th><th >Modelo</th><th >Fabricante</th><th >Est. Conservação</th></tr>
                </thead>
                <tbody>';
                foreach ($aux as $ativo) {
                    $tabel .= '<tr style="border-bottom:2px solid #f0f0f0;width:12px;">'
                            . '<td>' . $ativo->getPatrimonio() . '</td>'
                            . '<td>' . $ativo->getNome() . '</td>'
                            . '<td>' . $ativo->getSetor()->getNome() . '</td>'
                            . '<td>' . $ativo->getModelo()->getNome() . '</td>'
                            . '<td>' . $ativo->getFabricante()->getNome() . '</td>'
                            . '<td>' . $ativo->getConservacao()->getNome() . '</td>'
                            . '</tr>';
                }
                $tabel .= '</tbody>'
                        . '<tfoot style="width:12px;">
                    <tr style="background:#FFF;"><th >Patrimônio</th><th >Nome</th><th >Setor</th><th >Modelo</th><th >Fabricante</th><th >Est. Conservação</th></tr>
                    <tr><th ></th><th ></th><th ></th><th ></th><th >TOTAL DE BENS:</th><th >' . $tamanho . '</th></tr>
                </tfoot>
            </table>'
                        . '</div>'
                        . '</div>'
                        . '</div><br />';
            }
            $tabel .= '</body>';

////        $footer = '<footer style="position:absolute;bottom:0;width:100%;" ><p>Posted by: Hege Refsnes</p><p>Contact information: <a href="mailto:someone@example.com">someone@example.com</a>.</p></footer>';
            $header .= $titulo;
            $header .= $tabel;
////        $header .= $footer;
            return $header;
        }
    }

}
