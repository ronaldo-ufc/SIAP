<?php
include_once 'public/uteis/funcoes.php';
use siap\home\forms\PessoaForm;
use siap\auth\models\Autenticador;
use siap\usuario\models\Usuario;
$app->map(['GET', 'POST'], '/usuario/dados', function($request, $response, $args){   
  if($request->isGet()){
    $form = PessoaForm::create(["formdata" => $_GET]);
    #Pega instancia do usuário autenticado
    $aut = Autenticador::instanciar();
    
   
    return $this->renderer->render($response, 'meus_dados.html' , array('form' => $form->preencheFormularioByLogin($aut->getUsuario()), 'mensagem'=>$msg));
  }else{
    $form = PessoaForm::create(["formdata" => $_POST]);
    if (validaCPF($form->getLogin())) {
      if ($form->getSenha()){
        $senha = md5($form->getSenha());
      }else{
        #Pega instancia do usuário autenticado
        $aut = Autenticador::instanciar();
        $senha = $aut->getSenha();
      }
      Usuario::updatePessoa($form->getLogin(), strtoupper($form->getNome()), $senha, $form->getTelefone(), $form->getSetor());
      $msg = $form->getNome().', seus dados foram salvos com sucesso.';
      $form->errors = 'success';
    }else{ 
      $msg = 'Não foi possível salvar o usuário, verifique os dados inseridos!';
      $form->errors = 'danger';
    }
    return $this->renderer->render($response, 'meus_dados.html' , array('form' => $form, 'mensagem'=>$msg));

  }
})->setName('logout');
