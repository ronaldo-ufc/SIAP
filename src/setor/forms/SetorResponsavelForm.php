<?php

namespace siap\setor\forms;

use siap\setor\models\Setor;
use siap\usuario\models\Usuario;
use WTForms\Form;
use WTForms\Validators\InputRequired;
use WTForms\Fields\Core\SelectField;
use WTForms\Fields\HTML5\DateField; 

class SetorResponsavelForm extends Form {
    
  public function __construct(array $options = []) {
    parent::__construct($options);
    $setores = Setor::getAll();
    $usuarios = Usuario::getAllAtivos();
    
    #Cria o Array para alimenta os select dos usuarios
    $usuario = [];
    foreach($usuarios as $val){
      array_push($usuario, array($val->getLogin(), $val->getNome()));
    }
    
    #Cria o Array para alimenta os select dos setores
    $setor = [];
    foreach($setores as $val){
      array_push($setor, array($val->getSetor_Id(), $val->getNome()));
    }
    
    $this->data_inicio = new DateField(["value" => date('Y-m-d'),
                                       "validators" => [new InputRequired("Data de início é obrigatória")]
    ]);
    
    $this->data_fim = new DateField(["validators" => [
        new InputRequired("Data de fim é obrigatória")
       
    ]]);
    
    $this->setor = new SelectField(["choices" =>  $setor,
                                    "validators" => [new InputRequired("Campo setor é obrigatório")]
    ]);
    
    $this->responsavel = new SelectField(["choices" => $usuario,
                                    "validators" => [new InputRequired("Campo usuário é obrigatório")]
    ]);
    
    //$this->data_inicio->data = date('Y-m-d');
  }
 
  public function getDataInicio(){
      return $this->data_inicio->data;
  }
  function getDataFim() {
    return $this->data_fim->data;
  }
  function getSetor() {
    return $this->setor->data;
  }
  function getResponsavel() {
    return $this->responsavel->data;
  }
  public function valida_data_fim(){
    if (strtotime($this->data_fim->data) < strtotime($this->data_inicio->data)){
      $this->errors = 'danger';
       return ('Data Final não pode ser menor que a data de início');
    }else{
      $this->errors = null;
    }
    return false;
  }
  public function validaChoqueDePeriodo(){
    $setor = \siap\setor\models\SetorResponsavel::getLastBySetor($this->getSetor());
    $hoje = $setor?$setor->getData_fim():date('Y-m-d');
    if (strtotime($this->data_inicio->data) <= (strtotime($hoje))){
      $this->errors = 'danger';
       return ('Existe um Responsável Atual para o Setor Informado. Não Informar Datas Retroativas a Data de Hoje');
    }else{
      $this->errors = null;
    }
    return false;
  }
}