<?php
use siap\auth\forms\LoginForm;
use siap\auth\models\Autenticador;
#Todas essas rotas estão em /autenticacao

$app->map(['GET', 'POST'], '/login', function ($request, $response, $args) {
    $form = LoginForm::create(["formdata" => $_POST]);
    if($request->isGet()){
      $messages = $this->flash->getMessages();
      #Verificando se tem mensagem de erro
      if($messages){
        foreach ($messages as $_msg){
          $msg = $_msg[0];
        }
        $form->errors = 'danger';
      }
      return $this->renderer->render($response, 'login.html' , array('form' => $form, 'mensagem'=>$msg));
    }else{
      if ($form->validate()){       
        # Uso do singleton para instanciar
        # apenas um objeto de autenticação
        # e esconder a classe real de autenticação
        $aut = Autenticador::instanciar();

        # efetua o processo de autenticação
        if ($aut->logar($form->getLogin(), $form->getSenha())) {
          # redireciona o usuário para dentro do sistema
          $url = $this->router->pathFor('home');
          return $response->withRedirect($url);
        }else{  
          $this->flash->addMessage('erro', 'Usuário ou senha incorretos');
        }
      }
      return $response->withStatus(302)->withHeader('Location', 'login');
    }
   
})->setName('login');

$app->map(['GET', 'POST'], '/logout', function($request, $response, $args){
    session_destroy();
    $url = $this->router->pathFor('home');
    return $response->withRedirect($url);
})->setName('logout');
