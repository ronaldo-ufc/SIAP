<?php

namespace siap\relatorios\relatorio;

class Relatorio{
  private $titulo;
  private $content;
  
  public function getCabecalho() {
     $data = date("d/m/Y H:m:s");
        
    return '<style>th, td { border-bottom: 1px solid black } .relatorio tr:nth-child(even) {background: #FFF} .relatorio tr:nth-child(odd) {background: #EEE}@page {@bottom-right {content: counter(page) " of " counter(pages);}}</style><body><div style="text-align: center;  border-style: solid; border-width: 1px; padding: 10px 2px 10px 2px;">
        <img style="max-width: 100px; max-height: 100px; margin-left: 20px;" src="assets/img/brasao.png" align="left">
        <p><b>UNIVERSIDADE FEDERAL DO CEARÁ<br />CAMPUS DE CRATEÚS<br />SISTEMA DE ALMOXARIFADO E PATRIMÔNIO - SIAP</b><br />
            EMITIDO EM ' . $data . '</p>'
                    . '</div><br />';
  }

  public function getTitulo() {
    return $this->titulo;
  }

  public function setTitulo($titulo) {
    $this->titulo = '<div style="text-align:center;"><p><b>'. strtoupper($titulo).'</b></p></div>';
  }
  
  public function setContent($content) {
    $this->content = '<div class="container" style="font-size:12px; font-family:Calibri; padding:10px">';
    $this->content.=  $content;
    $this->content.=  " </div>";
  }
  public function getContent() {
    return $this->content;
  }

    public function imprime(){
    return $this->getCabecalho().$this->getTitulo().$this->getContent();
  }

}

