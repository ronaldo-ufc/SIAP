<?php
include_once 'public/uteis/funcoes.php';

use siap\produto\forms\TemplateProdutoForm;
use siap\produto\models\TemplateProduto;

$app->map(['GET', 'POST'], '/modelo', function($request, $response, $args){
  $form = TemplateProdutoForm::create(["formdata" => $_POST]);
  
  if($request->isPost()){
    $form->validate();
    $directory = $this->get('upload_directory_imagem');
    $uploadedFiles = $request->getUploadedFiles();

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedImage($directory, $uploadedFile);
        if ($filename){
          $msg = TemplateProduto::create($form->getNome(),  
                                        $form->getData_atesto(),  
                                        $form->getNota_fiscal(),  
                                        $form->getFornecedor(),  
                                        $form->getDescricao(),  
                                        $form->getObservacao(),  
                                        $filename,  
                                        $form->getFabricante(),  
                                        $form->getModelo(),  
                                        $form->getAquisicao(),  
                                        $form->getStatus(),  
                                        $form->getSetor(),  
                                        $form->getConservacao());
          if($msg[2]){
            $mensagem = $msg[2];
            $form->errors='danger';
          }else{
            $mensagem = 'Operação realizada com sucesso.';
            $form->errors='success';
          }
        }else{
          $mensagem = 'Tipo de arquivo não suportado para foto do produto. Insira a foto no formato (jpg / png / jpeg)';
          $form->errors='danger';
        }
    }
  }
  return $this->renderer->render($response, 'produto_template.html' , array('form'=>$form , 'mensagem'=>$mensagem));
    
  
})->setName('AtivoModelo');

$app->map(['GET', 'POST'], '/novo', function($request, $response, $args){
  $template = TemplateProduto::getAll();
  
  return $this->renderer->render($response, 'produto_novo.html' , array('templates'=>$template , 'mensagem'=>$mensagem));
      
})->setName('AtivoProdutoNovo');

$app->map(['GET', 'POST'], '/novo/modelo/{modelo_id}', function($request, $response, $args){
  $template = TemplateProduto::getById($args['modelo_id']);
  var_dump($template);
  //return $this->renderer->render($response, 'produto_cadastro.html' , array('templates'=>$template , 'mensagem'=>$mensagem));
      
})->setName('AtivoProdutoNovo');
