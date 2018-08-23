<?php

namespace siap\produto\forms;

use siap\setor\models\Setor;
use siap\cadastro\models\Fabricante;
use siap\cadastro\models\Modelo;
use siap\cadastro\models\Aquisicao;
use siap\cadastro\models\Status;
use siap\cadastro\models\EConservacao;
use WTForms\Form;
use WTForms\Validators\InputRequired;
use WTForms\Fields\Core\SelectField;
use WTForms\Fields\HTML5\DateField;
use WTForms\Fields\Core\StringField;
use WTForms\Fields\Simple\FileField;
use WTForms\Fields\Simple\TextAreaField;

class AtivosForm extends Form {
 
  public function __construct(array $options = []) {
       
    parent::__construct($options);
 
      foreach($options as $val){
        $data_atesto = $val['data_atesto'];
        $foto = $val['foto'];
        $fabricante_id = $val["fabricante"];
        $modelo_id = $val['modelo'];
        $aquisicao_id = $val["tipo_de_aquisicao"];
        $status_id = $val["status"];
        $setor_id = $val["setor"];
        $categoria_id = $val['categoria'];
        $conservacao_id = $val["estado_de_conservacao"];
      }
      
        
    #Caso o usuário esteja cadastrando um ativo sem modelo
    #pega todos os objetos sem a necessidade de um id específico  
        
    $data_atesto = $data_atesto?$data_atesto:date('Y-m-d');

    $setores = Setor::getAllById($setor_id);
    if (!$setores){
       
      $setores = Setor::getAll();
    }
    $categorias = \siap\cadastro\models\Categoria::getAllById($categoria_id);
    if(!$categorias){
      $categorias = \siap\cadastro\models\Categoria::getAll();
    }
    $fabricantes = Fabricante::getAllById($fabricante_id);
    if(!$fabricantes){
      $fabricantes = Fabricante::getAll();
    }
    $aquisicoes = Aquisicao::getAllById($aquisicao_id);
    if(!$aquisicoes){
      $aquisicoes = Aquisicao::getAll();
    }
    $_status = Status::getAllById($status_id);
    if(!$_status){
      $_status = Status::getAll();
    }
    $econservacao = EConservacao::getAllById($conservacao_id);
    if(!$econservacao){
      $econservacao = EConservacao::getAll();
    }
    $modelos = Modelo::getAllById($modelo_id);
     
     #Cria o Array para alimentar os select dos Estados de Conservação
    $conservacao = [];
    foreach($econservacao as $val){
      array_push($conservacao, array($val->getConservacao_id(), $val->getNome()));
    }
    
     #Cria o Array para alimentar os select dos Status
    $status = [];
    foreach($_status as $val){
      array_push($status, array($val->getStatus_id(), $val->getNome()));
    }
    
    #Cria o Array para alimentar os select dos Aquisicão
    $aquisicao = [];
    foreach($aquisicoes as $val){
      array_push($aquisicao, array($val->getAquisicao_id(), $val->getNome()));
    }
    
    #Cria o Array para alimentar os select dos Modelos
    $modelo = [];
    foreach($modelos as $val){
      array_push($modelo, array($val->getModelo_id(), $val->getNome()));
    }
    
    #Cria o Array para alimentar os select dos fabricantes
    $fabricante = [];
    foreach($fabricantes as $val){
      array_push($fabricante, array($val->getFabricante_id(), $val->getNome()));
    }
        
    #Cria o Array para alimenta os select dos setores
    $setor = [];
    foreach($setores as $val){
      array_push($setor, array($val->getSetor_Id(), $val->getNome()));
    }
    
    #Cria o Array para alimenta os select dos Categorias
    $categoria = [];
    foreach($categorias as $val){
      array_push($categoria, array($val->getCategoria_Id(), $val->getNome()));
    }
    
    $this->patrimonio = new StringField(["validators" => [new InputRequired("Número do patrimônio é obrigatório")]
    ]);
    
    $this->nome = new StringField([
                                   "validators" => [new InputRequired("Nome do Ativo é obrigatório")]
    ]);
    
    $this->data_atesto = new DateField(["value" => $data_atesto,
                                      "validators" => [new InputRequired("Data de Atesto é obrigatória")]
    ]);
    
    $this->nota_fiscal = new StringField([
    ]);
    
    $this->fornecedor = new StringField([
    ]);
    
    $this->descricao = new TextAreaField(["validators" => [new InputRequired("Descrição do ativo é obrigatória")]
    ]);
    
    $this->observacao = new TextAreaField([
    ]);
    
    $this->foto = new FileField(["value" => $foto
    ]);
    
    $this->marca = new SelectField(["choices" =>  $fabricante,
                                    "onclick" => ['modeloSelect()'],
                                    "validators" => [new InputRequired("Campo Marca é obrigatório")]
    ]);
    
    $this->categoria = new SelectField(["choices" =>  $categoria,
                                    "validators" => [new InputRequired("Campo categoria é obrigatória")]
    ]);
    
    $this->setor = new SelectField(["choices" =>  $setor,
                                "validators" => [new InputRequired("Campo setor é obrigatório")]
    ]);
   
    $this->modelo = new SelectField(["choices" => $modelo,
                                    "validators" => [new InputRequired("Campo modelo é obrigatório")]
    ]);
    
    $this->tipo_de_aquisicao = new SelectField(["choices" => $aquisicao,
                                    "validators" => [new InputRequired("Campo de aquisição é obrigatório")]
    ]);
    
    $this->status = new SelectField(["choices" => $status,
                                    "validators" => [new InputRequired("Campo de status é obrigatório")]
    ]);
    
    $this->estado_de_conservacao = new SelectField(["choices" => $conservacao,
                                    "validators" => [new InputRequired("Campo de aquisição é obrigatório")]
    ]);
    //$this->data_inicio->data = date('Y-m-d');
  }
  function setNome($template_id){
    $template_count = \siap\produto\models\Ativos::getQtdById($template_id);
    $_nome = explode("_", $this->nome->data);
    $this->nome->data = $_nome[0]."_".($template_count+1); 
  }
  function getPatrimonio() {
    return $this->patrimonio->data;
  }
  
  function getNome() {
    return $this->nome->data;
  }

  function getData_atesto() {
    return $this->data_atesto->data;
  }

  function getNota_fiscal() {
    return $this->nota_fiscal->data;
  }

  function getFornecedor() {
    return $this->fornecedor->data;
  }

  function getDescricao() {
    return $this->descricao->data;
  }

  function getObservacao() {
    return $this->observacao->data;
  }

  function getFabricante() {
    return $this->marca->data;
  }

  function getModelo() {
    return $this->modelo->data;
  }

  function getAquisicao() {
    return $this->tipo_de_aquisicao->data;
  }

  function getStatus() {
    return $this->status->data;
  }

  function getSetor() {
    return $this->setor->data;
  }
  
  function getFoto() {
    return $this->foto->data;
  }
  
  function getConservacao() {
    return $this->estado_de_conservacao->data;
  }
  
  function getCategoria() {
    return $this->categoria->data;
  }
  
}