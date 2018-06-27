<?php
include_once 'public/uteis/funcoes.php';
use siap\setor\models\Setor;
use siap\setor\forms\SetorResponsavelForm;
use siap\setor\models\SetorResponsavel;

$app->map(['GET', 'POST'], '/novo', function($request, $response, $args){
  
  if($request->isPost()){
    $postParam = $request->getParams();
    if ($postParam){
      $setor    = tirarAcentos($postParam['setor']);
      $sigla    = tirarAcentos($postParam['sigla']);
      Setor::create($setor, $sigla);
      $mensagem  = "Operação realizada com sucesso!";
    }
  }
  $setores = Setor::getAll();
  return $this->renderer->render($response, 'setor_novo.html' , array('setores' => $setores, 'mensagem'=>$mensagem));
    
  
})->setName('SetorNovo');

$app->map(['GET', 'POST'], '/responsavel', function($request, $response, $args){
  $form = SetorResponsavelForm::create(["formdata" => $_POST]);
  if($request->isPost()){
    if ($form->validate()){
      #Validação da data final que não pode ser menor que a data de início
      $mensagem = $form->valida_data_fim();
      if (!$form->errors){
         #Verifica se ja tem um responsável cadastrado para o período informado
        $mensagem = $form->validaChoqueDePeriodo();
      }
      #Verifico se não tem erros no formulário para inserir no banco
      if (!$form->errors){
        $msg = SetorResponsavel::create($form->getSetor(), $form->getResponsavel(), $form->getDataInicio(), $form->getDataFim());
        if ($msg[2]){
          $form->errors = "danger";
          $mensagem = $msg[2];
        }else{
          $form->errors = "success";
          $mensagem = 'Operacação realizado com sucesso!';
        }
      }
    }
  }else{
    $messages = $this->flash->getMessages();
    #Verificando se tem mensagem de erro
    if($messages){
      foreach ($messages as $_msg){
        $mensagem = $_msg[0];
      }
      $form->errors = 'danger';
    }
  }

  $setores = SetorResponsavel::getAll();
    
  return $this->renderer->render($response, 'setor_responsavel.html' , array('form'=>$form ,'setores'=>$setores,'mensagem'=>$mensagem));
    
  
})->setName('SetorResponsavel');

$app->get('/responsavel/delete/{responsavel_id}/{setor_id}/{data_inicio}', function($request, $response, $args){
  if($request->isGet()){
    $msg = SetorResponsavel::delete($args['setor_id'], $args['responsavel_id'], $args['data_inicio']);
   
    if ($msg[2]){
      $this->flash->addMessage('danger', $msg[2]);
    }else{
      $this->flash->addMessage('success', 'Registro excluido com sucesso');
    }
  }
  return $response->withStatus(301)->withHeader('Location', '../../../../responsavel');
})->setName('SetorResponsavelDelete');
