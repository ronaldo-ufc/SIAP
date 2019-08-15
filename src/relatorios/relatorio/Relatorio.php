<?php

namespace siap\relatorios\relatorio;

class Relatorio{
  private $titulo;
  private $content;
  protected $header;
  
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

  public function setTitulo($titulo, $sub='') {
    $this->titulo = '<div style="text-align:center;"><h3><b>'. strtoupper($titulo).'</b></h3><small><em>'.$sub.'</em></small></h3></div>';
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
  
  function imprimir($dompdf, $papel = 'A4', $orientacao='portrait', $n_pagina=1){
    $dompdf->setPaper($papel, $orientacao); //landscape // portrait
    if ($this->header[2] != NULL) {
      echo "erro";
    } else {
      $dompdf->load_html($this->header[1]);
      $dompdf->render();
      $canvas = $dompdf->get_canvas();
      if ($n_pagina==1){
        $canvas->page_text(500, 800, "Página {PAGE_NUM} de {PAGE_COUNT}", true, 8, array(0, 0, 0));
      }
      $dompdf->stream("document.pdf", array("Attachment" => false));
    }
  }

}

