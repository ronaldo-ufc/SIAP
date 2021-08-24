<?php

include_once 'public/uteis/funcoes.php';

use siap\setor\models\Bloco;
use siap\setor\models\Setor;
use siap\setor\forms\SetorResponsavelForm;
use siap\setor\models\SetorResponsavel;

$app->get('/novo', function($request, $response, $args) {
    $blocos = Bloco::getAll();
    $setores = Setor::getAll();
    $msg = getMensagem($this->flash->getMessages());
    return $this->renderer->render($response, 'setor_novo.html', array('setores' => $setores, 'blocos' => $blocos, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('SetorNovo');

$app->post('/novo', function($request, $response, $args) {
    $postParam = $request->getParams();
    $setor = $postParam['setor'];
    $sigla = tirarAcentos($postParam['sigla']);
    $bloco_id = tirarAcentos($postParam['bloco']);
    $result = Setor::create($setor, $sigla, $bloco_id);
    if ($result[2]) {
        $this->flash->addMessage('danger', $result[2]);
    } else {
        $this->flash->addMessage('success', 'Operação realizada com sucesso!');
    }

    $rota = $this->get('router')->pathFor('SetorNovo', []);
    return $response->withStatus(301)->withHeader('Location', $rota);
})->setName('SetorNovoPost');

$app->map(['GET', 'POST'], '/responsavel', function($request, $response, $args) {
    $form = SetorResponsavelForm::create(["formdata" => $_POST]);
    if ($request->isPost()) {
        if ($form->validate()) {
            #Validação da data final que não pode ser menor que a data de início
            $mensagem = $form->valida_data_fim();
            if (!$form->errors) {
                #Verifica se ja tem um responsável cadastrado para o período informado
                $mensagem = $form->validaChoqueDePeriodo();
            }
            #Verifico se não tem erros no formulário para inserir no banco
            if (!$form->errors) {
                $msg = SetorResponsavel::create($form->getSetor(), $form->getResponsavel(), $form->getDataInicio(), $form->getDataFim());
                if ($msg[2]) {
                    $form->errors = "danger";
                    $mensagem = $msg[2];
                } else {
                    $form->errors = "success";
                    $mensagem = 'Operacação realizado com sucesso!';
                }
            }
        }
    } else {

        $messages = $this->flash->getMessages();
        #Verificando se tem mensagem de erro
        if ($messages) {
            foreach ($messages as $_msg) {
                $mensagem = $_msg[0];
            }
            $form->errors = 'danger';
        }
    }

    $setores = SetorResponsavel::getAll();

    return $this->renderer->render($response, 'setor_responsavel.html', array('form' => $form, 'setores' => $setores, 'mensagem' => $mensagem));
})->setName('SetorResponsavel');

$app->get('/responsavel/delete/{responsavel_id}/{setor_id}/{data_inicio}', function($request, $response, $args) {
    if ($request->isGet()) {
        $msg = SetorResponsavel::delete($args['setor_id'], $args['responsavel_id'], $args['data_inicio']);

        if ($msg[2]) {
            $this->flash->addMessage('danger', $msg[2]);
        } else {
            $this->flash->addMessage('success', 'Registro excluido com sucesso');
        }
    }
    return $response->withStatus(301)->withHeader('Location', '../../../../responsavel');
})->setName('SetorResponsavelDelete');

$app->get('/editar/{setor_id}', function($request, $response, $args) {
    $setor_id = $args['setor_id'];
    $setor = Setor::getById($setor_id);
    $blocos = Bloco::getAll($setor->getBloco()->getBloco_id());
    $msg = getMensagem($this->flash->getMessages());
    if(!$setor){
        $msg[0] = "warning";
        $msg[1] = "Setor não encontrado !";
    }
    return $this->renderer->render($response, 'setor_editar.html', array('setor' => $setor, 'blocos' => $blocos, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('SetorEditar');

$app->post('/editar/{setor_id}', function($request, $response, $args) {
    $postParam = $request->getParams();
    $setor = $postParam['setor'];
    $sigla = tirarAcentos($postParam['sigla']);
    $bloco_id = tirarAcentos($postParam['bloco']);
    $ativo = tirarAcentos($postParam['ativo']);
    
    $updated = new Setor($args['setor_id']);
    $updated->setAtivo($ativo);
    $updated->setBloco_id($bloco_id);
    $updated->setNome($setor);
    $updated->setSigla($sigla);
    $result = $updated->update();
    if ($result[2]) {
        $this->flash->addMessage('danger', $result[2]);
    } else {
        $this->flash->addMessage('success', 'Operação realizada com sucesso!');
    }

    $rota = $this->get('router')->pathFor('SetorEditar', ['setor_id' => $args['setor_id']]);
    return $response->withStatus(301)->withHeader('Location', $rota);
})->setName('SetorEditarPost');

$app->get('/excluir/{setor_id}', function($request, $response, $args) {
    $setor_id = $args['setor_id'];
    $setor = new Setor($setor_id);
    $result = $setor->delete();
    if ($result[2]) {
        $this->flash->addMessage('danger', $result[2]);
    } else {
        $this->flash->addMessage('success', 'Operação realizada com sucesso!');
    }

    $rota = $this->get('router')->pathFor('SetorNovo', []);
    return $response->withStatus(301)->withHeader('Location', $rota);
})->setName('SetorExcluir');
