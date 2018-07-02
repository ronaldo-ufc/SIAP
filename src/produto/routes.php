<?php
include_once 'public/uteis/funcoes.php';

use siap\produto\forms\TemplateProdutoForm;
use siap\produto\models\TemplateProduto;
use siap\produto\forms\AtivosForm;
use siap\produto\models\Ativos;

$app->map(['GET', 'POST'], '/main', function($request, $response, $args){
  $template = TemplateProduto::getAll();
  
  return $this->renderer->render($response, 'ativos_main.html' , array('templates'=>$template , 'mensagem'=>$mensagem));
      
})->setName('AtivosMain');

$app->map(['GET', 'POST'], '/show', function($request, $response, $args){
  $ativos = Ativos::getAll();
  
  return $this->renderer->render($response, 'ativos_show.html' , array('ativos'=>$ativos , 'mensagem'=>$mensagem));
      
})->setName('AtivosShow');

$app->map(['GET', 'POST'], '/modelo', function($request, $response, $args){
  $form = TemplateProdutoForm::create(["formdata" => $_POST]);
  
  if($request->isPost()){
    $form->validate();
    $uploadedFiles = $request->getUploadedFiles();

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        //$filename = moveUploadedImage($this->get('upload_directory_imagem'), $uploadedFile);
       // Associamos a classe à variável $upload
      $upload = new siap\models\UploadImagem(); 
      // Determinamos nossa largura máxima permitida para a imagem
      $upload->width = 450; 
      // Determinamos nossa altura máxima permitida para a imagem
      $upload->height = 350; 
      // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
      //Se for sucesso, a mensagem também é um link para a imagem enviada.
       $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
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

$app->map(['GET', 'POST'],'/novo/modelo/{modelo_id}', function($request, $response, $args){
  if($request->isGet()){
    $template = TemplateProduto::getById($args['modelo_id']);
    $foto = $template->getFoto();
    $form = AtivosForm::create(["formdata" => $_GET, "data"=>[
                            "nome"=>$template->getNome(),
                            "foto"=>$template->getFoto(),
                            "data_atesto"=>$template->getData_atesto(),
                            "nota_fiscal"=>$template->getNota_fiscal(),
                            "fornecedor"=>$template->getFornecedor(),
                            "descricao"=>$template->getDescricao(),
                            "observacao"=>$template->getObservacao(),
                            "fabricante"=>$template->getFabricante_id(),
                            "modelo"=>$template->getModelo_id(),
                            "tipo_de_aquisicao" => $template->getAquisicao_id(),
                            "status"=>$template->getStatus_id(),
                            "setor"=>$template->getSetor_id(),
                            "estado_de_conservacao"=>$template->getConservacao_id(),
                            "template_id"=>$args['modelo_id']
    ]]);
    
   }else{
    #Caso seja POst ou seja o usuário está salvando o bem 
    $aut = \siap\auth\models\Autenticador::instanciar();
    $form = AtivosForm::create(["formdata" => $_POST]);
    
    #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
    #pois o form estava perdendo o valor quando fazia o post.
    $postParam = $request->getParams();
    $filename = null;
    $uploadedFiles = $request->getUploadedFiles();
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
       //$filename = moveUploadedImage($this->get('upload_directory_imagem'), $uploadedFile);
       // Associamos a classe à variável $upload
      $upload = new siap\models\UploadImagem(); 
      // Determinamos nossa largura máxima permitida para a imagem
      $upload->width = 450; 
      // Determinamos nossa altura máxima permitida para a imagem
      $upload->height = 350; 
      // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
      //Se for sucesso, a mensagem também é um link para a imagem enviada.
       $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
    }
    $foto = $filename? $filename:$postParam['foto_bem'];
    
    if ($form->validate()){
      $msg = Ativos::create($form->getPatrimonio(),
                          $form->getNome(),  
                          $form->getData_atesto(),  
                          $form->getNota_fiscal(),  
                          $form->getFornecedor(),  
                          $form->getDescricao(),  
                          $form->getObservacao(),  
                          $foto,  
                          $form->getFabricante(),  
                          $form->getModelo(),  
                          $form->getAquisicao(),  
                          $form->getStatus(),  
                          $form->getSetor(),  
                          $form->getConservacao(),
                          $args['modelo_id'],
                          $aut->getUsuario()

      );
      if($msg[2]){
        $mensagem = $msg[2];
        $form->errors='danger';
      }else{
        $mensagem = 'Operação realizada com sucesso.';
        $form->errors='success';
      }
      
    }//Fim do if validate form
    
   }
  $form->setNome($args['modelo_id']);
  return $this->renderer->render($response, 'ativo_novo.html' , array('form'=>$form , 'mensagem'=>$mensagem, 'foto'=>$foto));
      
})->setName('AtivoProdutoNovo');

$app->map(['GET', 'POST'], '/novo/branco', function($request, $response, $args){
   if($request->isPost()){
    #Caso seja POst ou seja o usuário está salvando o bem 
    $aut = \siap\auth\models\Autenticador::instanciar();
    $form = AtivosForm::create(["formdata" => $_POST]);
    
    #Recebe o nome da foto do bem que está numa variável tipo hiddem para pode mostrar, 
    #pois o form estava perdendo o valor quando fazia o post.
    $postParam = $request->getParams();
    $filename = null;
    $uploadedFiles = $request->getUploadedFiles();
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
      // Associamos a classe à variável $upload
      $upload = new siap\models\UploadImagem(); 
      // Determinamos nossa largura máxima permitida para a imagem
      $upload->width = 450; 
      // Determinamos nossa altura máxima permitida para a imagem
      $upload->height = 350; 
      // Exibimos a mensagem com sucesso ou erro retornada pela função salvar.
      //Se for sucesso, a mensagem também é um link para a imagem enviada.
       $filename = $upload->salvar($this->get('upload_directory_imagem'), $_FILES['foto']);
    }
    $foto = $filename? $filename:$postParam['foto_bem'];

    if ($form->validate()){
      $msg = Ativos::create($form->getPatrimonio(),
                          $form->getNome(),  
                          $form->getData_atesto(),  
                          $form->getNota_fiscal(),  
                          $form->getFornecedor(),  
                          $form->getDescricao(),  
                          $form->getObservacao(),  
                          $foto,  
                          $form->getFabricante(),  
                          $form->getModelo(),  
                          $form->getAquisicao(),  
                          $form->getStatus(),  
                          $form->getSetor(),  
                          $form->getConservacao(),
                          $args['modelo_id'],
                          $aut->getUsuario()

      );
      if($msg[2]){
        $mensagem = $msg[2];
        $form->errors='danger';
      }else{
        $mensagem = 'Operação realizada com sucesso.';
        $form->errors='success';
      }
      
    }//Fim do if validate form
   }else{
     $form = AtivosForm::create(["formdata" => $_GET]);
     $foto = 'sem_imagem.jpg';
   }
  return $this->renderer->render($response, 'ativo_novo.html' , array('form'=>$form , 'mensagem'=>$mensagem, 'foto'=>$foto));
      
})->setName('AtivoEmBranco');

$app->map(['GET', 'POST'], '/movimentacao', function($request, $response, $args){
  $template = TemplateProduto::getAll();
  
  return $this->renderer->render($response, 'produto_novo.html' , array('templates'=>$template , 'mensagem'=>$mensagem));
      
})->setName('AtivoMovimentacao');