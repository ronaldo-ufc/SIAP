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

class TemplateProdutoForm extends Form {
    
  public function __construct(array $options = []) {
    parent::__construct($options);
    $setores = Setor::getAll();
    $marcas = Fabricante::getAll();
    $empenho = $val['empenho'];
    
    $aquisicoes = Aquisicao::getAll();
    $_status = Status::getAll();
    $econservacao = EConservacao::getAll();
    $categorias = \siap\cadastro\models\Categoria::getAll();
    
     #Cria o Array para alimentar os select dos Estados de Conservação
    $conservacao = [];
    foreach($econservacao as $val){
      array_push($conservacao, array($val->getConservacao_id(), $val->getNome()));
    }
    
     #Cria o Array para alimentar os select da categoria
    $categoria = [];
    foreach($categorias as $val){
      array_push($categoria, array($val->getCategoria_id(), $val->getNome()));
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
    
//    #Cria o Array para alimentar os select dos Modelos
//    $modelo = [];
//    foreach($modelos as $val){
//      array_push($modelo, array($val->getModelo_id(), $val->getNome()));
//    }
    
    #Cria o Array para alimentar os select dos fabricantes
    $marca = [];
    foreach($marcas as $val){
      array_push($marca, array($val->getFabricante_id(), $val->getNome()));
    }
        
    #Cria o Array para alimenta os select dos setores
    $setor = [];
    foreach($setores as $val){
      array_push($setor, array($val->getSetor_Id(), $val->getNome()));
    }
    
    $this->nome = new StringField(["validators" => [new InputRequired("Nome do modelo é obrigatório")]
    ]);
    
    $this->data_atesto = new DateField([
    ]);
    
    $this->empenho = new StringField([
    ]);
    
    $this->nota_fiscal = new StringField([
    ]);
    
    $this->fornecedor = new StringField([
    ]);
    
    $this->descricao = new TextAreaField(["validators" => [new InputRequired("Descrição do produto é obrigatória")]
    ]);
    
    $this->observacao = new TextAreaField([
    ]);
    
    $this->foto = new FileField([
    ]);
    
    $this->marca = new SelectField(["choices" =>  $marca,
                                    "onclick" => ['modeloSelect()'],
                                    "validators" => [new InputRequired("Campo Fabricante é obrigatório")]
    ]);
    
    $this->setor = new SelectField(["choices" =>  $setor,
                                    "validators" => [new InputRequired("Campo setor é obrigatório")]
    ]);
    
    $this->categoria = new SelectField(["choices" =>  $categoria,
                                    "validators" => [new InputRequired("Campo categoria é obrigatória")]
    ]);
    
    $this->modelo = new SelectField(["onclick" => ['teste()'],
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

  function getMarca() {
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
  
  function getEmpenho() {
    return $this->empenho->data;
  }
}