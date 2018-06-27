<?php

namespace siap\home\forms;

use siap\setor\models\Setor;
use siap\usuario\models\Privilegio;
use WTForms\Form;
use WTForms\Fields\Simple\PasswordField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Fields\HTML5\TelField;
use WTForms\Fields\Core\SelectField;
 

class PessoaForm extends Form {
    
  public function __construct(array $options = []) {
    parent::__construct($options);
    $setores = Setor::getAll();
             
    #Cria o Array para alimenta os select dos setores
    $setor = [];
    foreach($setores as $val){
      array_push($setor, array($val->getSetor_Id(), $val->getNome()));
    }
    
    $this->usuario = new StringField(["validators" => [
        new InputRequired("Campo obrigatório") 
    ]]);
    $this->nome = new StringField(["validators" => [
        new InputRequired("Campo obrigatório")
    ]]);
    $this->senha_ = new PasswordField(["validators" => [
        new InputRequired("Campo obrigatório")
    ]]);
    $this->telefone = new TelField(["validators" => [
        new InputRequired("Campo obrigatório")
    ]]);
    $this->setor = new SelectField(["choices" =>  $setor,
                                    "validators" => [new InputRequired("Campo obrigatório")]
    ]);

    $this->privilegio = new StringField(["validators" => [new InputRequired("Campo obrigatório")]
    ]);
        
    $this->ativo = new StringField(["validators" => [new InputRequired("Campo obrigatório")]
    ]);

  }
 
  public function getLogin(){
      return $this->usuario->data;
  }
  function getNome() {
    return $this->nome->data;
  }
  public function getSenha(){
      return $this->senha_->data;
  }
  function getTelefone() {
    return $this->telefone->data;
  }
  function getSetor() {
    return $this->setor->data;
  }
  function getPrivilegio() {
    return $this->privilegio->data;
  }
  function getUsuarios() {
    return $this->usuarios->data;
  }
  function getAtivo() {
    return $this->ativo->data;
  }
  public function setAtivo($ativo){
    $this->ativo = $ativo;
  }
  public function setLogin($login){
    $this->usuario = $login;
  }
  function setNome($nome) {
    $this->nome = $nome;
  }
  public function setSenha($senha){
    $this->senha_ = $senha;
  }
  function setTelefone($data) {
    $this->telefone = $data;
  }
  function setSetor($data) {
    $this->setor = $data;
  }
  function setPrivilegio($data) {
    $privilegio = Privilegio::getByCodigo($data);
    $this->privilegio = $privilegio->getPrivilegio_nome();
  }
 
  function preencheFormularioByLogin($login){
    $usuario = \siap\usuario\models\Usuario::getByLogin($login);
    $u = new PessoaForm();
    $u->setLogin($usuario->getLogin());
    $u->setNome($usuario->getNome());
    $u->setSenha($usuario->getSenha());
    $u->setTelefone($usuario->getTelefone());
    $u->setSetor($usuario->getSetor());
    $u->setPrivilegio($usuario->getNivelAcesso());
    $u->setAtivo($usuario->getAtivo());
    return $u;
  }
 
//    public function validate_senha(){
//      if ($this->senha->data == 'ok'){
//          throw new ValidationError('ok');
//      }
//    }
}