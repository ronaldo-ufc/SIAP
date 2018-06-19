<?php

namespace siap\auth\forms;

use WTForms\Form;
use WTForms\Fields\Simple\PasswordField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
//use WTForms\Exceptions\ValidationError;


class LoginForm extends Form {

    public function __construct(array $options = []) {
        parent::__construct($options);
        $this->login = new StringField(["validators" => [
            new InputRequired("Campo obrigatório")
                      
        ]]);
        $this->senha = new PasswordField(["validators" => [
            new InputRequired("Campo obrigatório")
        ]]);
       
    }
    public function getLogin(){
        return $this->login->data;
    }
    public function getSenha(){
        return $this->senha->data;
    }
    
//    public function validate_senha(){
//      if ($this->senha->data == 'ok'){
//          throw new ValidationError('ok');
//      }
//    }
}