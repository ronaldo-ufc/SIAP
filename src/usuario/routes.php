<?php
include_once 'public/uteis/funcoes.php';
use siap\usuario\forms\UsuarioForm;
use siap\usuario\models\Usuario;
use siap\usuario\models\Privilegio;
use siap\usuario\models\Permicao;
use siap\home\models\Menu;

$app->map(['GET', 'POST'], '/novo', function($request, $response, $args){
  if($request->isGet()){
    $form = UsuarioForm::create(["formdata" => $_GET]);
        
    #Verificando se foi pesquisado algum formulario para fazer alteração de usuario
    $usuario_login = $form->getUsuarios();
    if($usuario_login){
      $form = $form->preencheFormularioByLogin($usuario_login);
      $form->errors = 'success';
    }
    return $this->renderer->render($response, 'usuario_novo.html' , array('form' => $form, 'mensagem'=>$msg));
  }else{
    $form = UsuarioForm::create(["formdata" => $_POST]);
    if (validaCPF($form->getLogin())) {
      if ($form->getSenha()){
        $senha = md5($form->getSenha());
      }else{
        $senha = $form->getSenha();
      }
      Usuario::createOrUpdate($form->getLogin(), strtoupper($form->getNome()), $senha, $form->getTelefone(), $form->getSetor(), $form->getPrivilegio(), $form->getAtivo());
      $msg = 'Usuário '.$form->getNome().' Salvo com Sucesso!';
      $form->errors = 'success';

    }else{ 
      $msg = 'CPF '.$form->getLogin().' digitado é inválido';
      $form->errors = 'danger';
    }
    return $this->renderer->render($response, 'usuario_novo.html' , array('form' => $form, 'mensagem'=>$msg));
    
  }
})->setName('novo');

#Rota para os grupos de usuários
$app->map(['GET', 'POST'], '/permissoes', function($request, $response, $args){
  if($request->isPost()){
    $postParam = $request->getParams();
    if ($postParam){
      $priv    = $postParam['privilegio'];
      $menu    = $postParam['menu'];
      if ($menu){
        #Pega todos os menus filhos do menu pai passado
        #Para depois comparar com os submenus que o usuario escolheu
        $filho_codigo = [];
        $_filhos = Menu::getFilhosByPai($menu);
        foreach ($_filhos as $filho){
          array_push($filho_codigo, $filho->getMenu_codigo());
        }
        #Pegando os submenus que o usuário escolheu
        $subMenu = $postParam['chk'];
        $sub_codigo = [];
        foreach ($subMenu as $sub){
          array_push($sub_codigo, $sub);
      
        }
        $flag_menu_pai_hab = false;
        
        #Verificando se os submenus são os mesmo que o usuário escolheu
        #Caso positivo habilito menu no banco como sim caso negativo fica como não
        for($i = 0; $i < count($filho_codigo); $i++){
          if (in_array($filho_codigo[$i], $sub_codigo)){
            Permicao::update($priv, $filho_codigo[$i], 'S');
            
            #Se pelo menos um submenu esta ativo então habilita o pai
            $flag_menu_pai_hab = true;
          }else{
            Permicao::update($priv, $filho_codigo[$i], 'N');
          }
        }
        
        #Se pelo menos um submenu esta ativo então habilita o pai
        if($flag_menu_pai_hab){
          Permicao::update($priv, $menu, 'S');
        }else{
          Permicao::update($priv, $menu, 'N');
        }
        $mensagem = "Operação Realizada com sucesso!";
      }
    }
  }
  $privilegios = Privilegio::getAll();
  return $this->renderer->render($response, 'usuario_permissoes.html' , array('privilegios'=>$privilegios, 'mensagem'=>$mensagem));
})->setName('permissoes');
