<?php

namespace siap\usuario\forms;

use siap\setor\models\Setor;
use siap\usuario\models\Privilegio;
use siap\usuario\models\Usuario;
use WTForms\Form;
use WTForms\Fields\Simple\PasswordField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Fields\HTML5\TelField;
use WTForms\Fields\Core\SelectField;
 

class UsuarioForm extends Form {
    
  public function __construct(array $options = []) {
    parent::__construct($options);
    $setores = Setor::getAll();
    $privilegios = Privilegio::getAll();
    $usuarios = Usuario::getAll();
    
    #Cria o Array para alimenta os select dos setores
    $usuario = [];
    array_push($usuario, array(null, 'Selecione um usuário'));
    foreach($usuarios as $val){
      array_push($usuario, array($val->getLogin(), $val->getNome()));
    }
    
    #Cria o Array para alimenta os select dos setores
    $setor = [];
    foreach($setores as $val){
      array_push($setor, array($val->getSetor_Id(), $val->getNome()));
    }
    
    #Cria o array para alimentar o select do privilegio
    $privilegio = [];
    foreach($privilegios as $val){
      array_push($privilegio, array($val->getPrivilegio_codigo(), $val->getPrivilegio_nome()));
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

    $this->privilegio = new SelectField(["choices" => $privilegio,
                                         "validators" => [new InputRequired("Campo obrigatório")]
    ]);
    
    $this->usuarios = new SelectField(["choices" => $usuario
    ]);
    
    $this->ativo = new SelectField(["choices" => [['S', 'SIM'],['N','Não']]
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
    $this->privilegio = $data;
  }
 
  function preencheFormularioByLogin($login){
    $usuario = \siap\usuario\models\Usuario::getByLogin($login);
    $u = new UsuarioForm();
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