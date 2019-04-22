<?php
namespace siap\funcoes;

function getMensagemByCodigo($codigo, $txt = NULL){ 
    switch ($codigo){
        case 1:
            return '<div class="alert alert-danger"><p class="text-center">Não encontramos nenhum usuário com o Login: <strong>'.$txt.'</strong>. Favor inserir um Login válido.<p class="text-center"></div>';
            break;
        case 2:
            return '<div class="alert alert-success"><p class="text-center">Operação Realizada com Sucesso.</p></div>';
            break;
        case 3:
            return '<div class="alert alert-danger"><p class="text-center">No período desta movimentação não existe um Agente Setorial responsável pelo setor. Cadastre primeiro o Agente Setorial para o setor.<p class="text-center"></div>';
            break;
        case 4:
            return '<div class="alert alert-info"><p class="text-center"><strong>'.$txt.'</strong></p></div>';
            break;
        case 5:
            return '<div class="alert alert-danger"><p class="text-center"><strong>As senhas digitadas nos campos não conferem.</strong></p></div>';
            break;
        case 6:
            return '<div class="alert alert-success"><p class="text-center"><strong>Senha alterada com sucesso. <a href="/sigce">Clique aqui</a> para ser redirecionado ao Login</strong></p></div>';
            break;
        case 7:
            return '<div class="alert alert-danger"><strong>'.$txt.'</strong></div>';
            break;
        case 8:
            return '<div class="alert alert-warning"><strong>'.$txt.'</strong></div>';
            break;
        case 9:
            return '<div class="alert alert-success"><strong>'.$txt.'</strong></div>';
            break;
        case 10:
            return '<div class="alert alert-success"><p class="text-center"><strong>E-mail cadastrado com sucesso. <a href="/sigce/recuperar/senha">Clique aqui</a> para ser redirecionado para a Redefinição de Senha</strong></p></div>';
            break;
        case 9915:
            return '<div class="alert alert-success"><p class="text-center"><strong>Programação excluida com sucesso.</p></div>';
            break;
        case 9916:
            return '<div class="alert alert-danger"><p class="text-center"><strong>Não foi possível excluir essa programação.</p></div>';
            break;
    }
}



