<?php

header('Content-Type: text/html; charset=UTF-8');
function getMensagem($messages){
  
  #Verificando se tem mensagem de erro
  return array(key($messages), $messages[key($messages)][0]);
  
}

function validaCPF($cpf = null) {

    // Verifica se um número foi informado
    if (empty($cpf)) {
        return false;
    }

    // Elimina possivel mascara
    //$cpf = ereg_replace('[^0-9]', '', $cpf);
    //$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
        return false;
        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
    } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }

        return true;
    }
}

function getClientIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function formataCPF($nbr_cpf) {
    $parte_um = substr($nbr_cpf, 0, 3);
    $parte_dois = substr($nbr_cpf, 3, 3);
    $parte_tres = substr($nbr_cpf, 6, 3);
    $parte_quatro = substr($nbr_cpf, 9, 2);

    return $parte_um . "." . $parte_dois . "." . $parte_tres . "-" . $parte_quatro;
}

function desFormataCPF($valor) {
    $valor = trim($valor);
    $valor = str_replace(".", "", $valor);
    $valor = str_replace(",", "", $valor);
    $valor = str_replace("-", "", $valor);
    $valor = str_replace("/", "", $valor);
    return $valor;
}

function desFormataCelular($valor) {
    $valor = trim($valor);
    $valor = str_replace("(", "", $valor);
    $valor = str_replace(")", "", $valor);
    $valor = str_replace(" ", "", $valor);
    $valor = str_replace("-", "", $valor);
    return $valor;
}

function formatDate($data) {
    $_data = split('-', $data);
    return $_data[2] . "/" . $_data[1] . "/" . $_data[0];
}

function formatHora($hora) {
    $_var = explode(":", $hora);
    return $_var[0] . ":" . $_var[1];
}

function moeda($valor){
    return number_format($valor,2,",",".");
}

function moedaBanco($get_valor) {

    $source = array('.', ',');
    $replace = array('', '.');
    $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
    return $valor? $valor: 0; //retorna o valor formatado para gravar no banco
}

function retiraElementoArray($arr, $_var) {

    $newArr = array();

    foreach ($arr as $value) {
        if ($value != $_var) {
            array_push($newArr, $value);
        }
    }
    return $newArr;
}

function formatCelular($tipo) {
    $string = ereg_replace("[^0-9]", "", $string);

    $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 5)
            . '-' . substr($tipo, 7);

    return $string;
}

function enviaEmail($nome, $email, $assunto, $msg_, $remetente) {
    //$msg = "*** Email enviado pelo site *** <br/>" . "Email: " . $email . "<br />" . "Mensagem: " . $msg_;


    $Mailer = new PHPMailer();

//Define que será usado SMTP
    $Mailer->IsSMTP();

//Enviar e-mail em HTML
    $Mailer->isHTML(true);

//Aceitar carasteres especiais
    $Mailer->Charset = 'UTF-8';

//Configurações
    $Mailer->SMTPAuth = true;
    $Mailer->SMTPSecure = 'ssl';

//nome do servidor
    $Mailer->Host = 'smtp.gmail.com';
//Porta de saida de e-mail 
    $Mailer->Port = 465;

//Dados do e-mail de saida - autenticação
    $Mailer->Username = 'encontrosuniversitarioscrateus@gmail.com';
    $Mailer->Password = 'cc@ufc!_';
//      $Mailer->Username = 'sigcenaoresponda@gmail.com';
//      $Mailer->Password = 'cc@ufc!_';
//E-mail remetente (deve ser o mesmo de quem fez a autenticação)
    $Mailer->From = 'encontrosuniversitarioscrateus@gmail.com';

//Nome do Remetente
    $Mailer->FromName = $remetente;

//Assunto da mensagem
    $Mailer->Subject = $assunto;

//Corpo da Mensagem
    $Mailer->Body = $msg_;

//Corpo da mensagem em texto
    $Mailer->AltBody = $msg_;

//Destinatario 
    $Mailer->AddAddress($email);

    return $Mailer->Send();
}

function criaLinkRecuperacao($login) {
    $dia = date('Y-m-d H:m:s');
    return md5($login . $dia);
}

function montaEmailRedefinicao($codigo_autenticacao, $pessoa) {
    $url = 'http://www.crateus.ufc.br/sigce/recuperar/redefinicao/' . $codigo_autenticacao;
    $assunto = 'Redefinição de Senha';
    $assunto = '=?UTF-8?B?' . base64_encode($assunto) . '?=';
    $remetente = 'SIGCE - Sistema de Gestão de Certificados e Eventos';
    $remetente = '=?UTF-8?B?' . base64_encode($remetente) . '?=';
    $nome = $pessoa->getNome();
    $mensagem = montaMensagem($nome, $url);
    return enviaEmail($nome, $pessoa->getEmail(), $assunto, $mensagem, $remetente);
}

function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI")) {
    /*
     * Exceptions in lower case are words you don't want converted
     * Exceptions all in upper case are any words you don't want converted to title case
     *   but should be converted to upper case, e.g.:
     *   king henry viii or king henry Viii should be King Henry VIII
     */
    $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) {
        $words = explode($delimiter, $string);
        $newwords = array();
        foreach ($words as $wordnr => $word) {
            if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtoupper($word, "UTF-8");
            } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtolower($word, "UTF-8");
            } elseif (!in_array($word, $exceptions)) {
                // convert to uppercase (non-utf8 only)
                $word = ucfirst($word);
            }
            array_push($newwords, $word);
        }
        $string = join($delimiter, $newwords);
    }//foreach
    return $string;
}

function tirarAcentos($string) {
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(Ç)/", "/(ç)/"), explode(" ", "a A e E i I o O u U n N C c"), $string);
}

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @return string filename of moved file
 */
function moveUploadedFile($directory, $uploadedFile) {
    $imagem = array("pdf", "jpeg", "jpg", "png");
    $extension = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
    if (in_array($extension, $imagem)) {
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
    return null;
}

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded Image.
 *
 * @param string $directory directory to which the file is moved
 * @return string filename of moved file
 */
function moveUploadedImage($directory, $uploadedFile) {
    $imagem = array("jpg", "jpeg", "png");
    $extension = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
    if (in_array($extension, $imagem)) {
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
    return null;
}

function getMensagemByCodigo($codigo, $txt = NULL) {
    switch ($codigo) {
        case 1:
            return '<div class="alert alert-danger"><p class="text-center">Não encontramos nenhum usuário com o Login: <strong>' . $txt . '</strong>. Favor inserir um Login válido.<p class="text-center"></div>';
            break;
        case 2:
            return '<div class="alert alert-success"><p class="text-center">Operação Realizada com Sucesso.</p></div>';
            break;
        case 3:
            return '<div class="alert alert-danger"><p class="text-center">No período desta movimentação não existe um Agente Setorial responsável pelo setor. Cadastre primeiro o Agente Setorial para o setor.<p class="text-center"></div>';
            break;
        case 4:
            return '<div class="alert alert-info"><p class="text-center"><strong>' . $txt . '</strong></p></div>';
            break;
        case 5:
            return '<div class="alert alert-danger"><p class="text-center"><strong>As senhas digitadas nos campos não conferem.</strong></p></div>';
            break;
        case 6:
            return '<div class="alert alert-success"><p class="text-center"><strong>Senha alterada com sucesso. <a href="/sigce">Clique aqui</a> para ser redirecionado ao Login</strong></p></div>';
            break;
        case 7:
            return '<div class="alert alert-danger"><strong>' . $txt . '</strong></div>';
            break;
        case 8:
            return '<div class="alert alert-warning"><strong>' . $txt . '</strong></div>';
            break;
        case 9:
            return '<div class="alert alert-success"><strong>' . $txt . '</strong></div>';
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

//Retorna a quantidade de elementos na lista passada como parâmetro
function retornaTamanhoLista($lista) {
    return sizeof($lista);
}

//Retorna uma lista com patrimonios que pertencem a dada categoria
function categoriaFiltro($ativos, $categoria_id) {
    $result = array();
    foreach ($ativos as $ativo) {
        if ($ativo->getCategoria_id() == $categoria_id) {
            array_push($result, $ativo);
        }
    }
    return $result;
}


//Retorna uma lista com patrimonios que pertencem a dado setor
function setorFiltro($ativos, $setor_id) {
    $result = array();
    foreach ($ativos as $ativo) {
        if ($ativo->getSetor_id() == $setor_id) {
            array_push($result, $ativo);
        }
    }
    return $result;
}

//Retorna uma lista com movimentações que pertencem a dado setor
function movFiltro($movimentacoes, $setor_id) {
    $result = array();
    foreach ($movimentacoes as $mov) {
        if ($mov->getSetor_id() == $setor_id) {
            array_push($result, $mov);
        }
    }
    return $result;
}


//Retorna uma lista com patrimonios que pertencem a dado setor e categoria
function categoriaSetorFiltro($ativos, $categoria_id, $setor_id){
    $result = array();
    foreach ($ativos as $ativo){
        if($ativo->getCategoria_id() == $categoria_id and $ativo->getSetor_id() == $setor_id){
            array_push($result, $ativo);
        }
    }
    return $result;
}

//Retorna uma lista com todas as movimentações de entrada
function movEntradaFiltro($movimentacoes){
    $result = array();
    foreach ($movimentacoes as $mov){
        if($mov->getMov_entrada() == TRUE){
            array_push($result, $mov);
        }
    }
    return $result;
}

//Retorna uma lista com todas as movimentações de saída
function movSaidaFiltro($movimentacoes){
    $result = array();
    foreach ($movimentacoes as $mov){
        if($mov->getMov_entrada() == FALSE){
            array_push($result, $mov);
        }
    }
    return $result;
}

//Retorna uma lista com todos os bens com um determinado número de empenho
function bensPorEmpenho($bens,$empenho){
    $result = array();
    foreach ($bens as $bem){
        if($bem->getEmpenho() == $empenho){
            array_push($result, $bem);
        }
    }
    return $result;
}

//Retorna uma data no formato DD-MM-AAAA
function formData($data){
// Quebra o texto nos "-" e transforma cada pedaço numa matriz
    $divisor = explode("-", $data);

// Inverte os pedaços
    $reverso = array_reverse($divisor);

// Junta novamente a matriz em texto
    $result = implode("-", $reverso); // Junta com -
    return $result;
}


//Retorna o responsável pelo setor dentre uma lista de responsáveis
function responsavelPorSetor($setor_id,$responsaveis){
    foreach ($responsaveis as $responsavel){
        if($responsavel->getSetor_id() == $setor_id){
            return $responsavel;
        }
    }
    return NULL;
}

function mesAbreviado($num_mes){
    if($num_mes == 1) return  'JAN';
    if($num_mes == 2) return  'FEV';
    if($num_mes == 3) return  'MAR';
    if($num_mes == 4) return  'ABR';
    if($num_mes == 5) return  'MAI';
    if($num_mes == 6) return  'JUN';
    if($num_mes == 7) return  'JUL';
    if($num_mes == 8) return  'AGO';
    if($num_mes == 9) return  'SET';
    if($num_mes == 10) return  'OUT';
    if($num_mes == 11) return  'NOV';
    if($num_mes == 12) return  'DEZ';
    return  'NOT_FOUND';
}

function mesCompleto($num_mes){
    if($num_mes == 1) return  'JANEIRO';
    if($num_mes == 2) return  'FEVEREIRO';
    if($num_mes == 3) return  'MARÇO';
    if($num_mes == 4) return  'ABRIL';
    if($num_mes == 5) return  'MAIO';
    if($num_mes == 6) return  'JUNHO';
    if($num_mes == 7) return  'JULHO';
    if($num_mes == 8) return  'AGOSTO';
    if($num_mes == 9) return  'SETEMBRO';
    if($num_mes == 10) return  'OUTUBRO';
    if($num_mes == 11) return  'NOVEMBRO';
    if($num_mes == 12) return  'DEZEMBRO';
    return  'NOT_FOUND';
}

function capitalize($str){
    if(!$str) return '';
    $lower = strtolower($str);
    return ucwords($lower);
}

function upperInitial($str){
    if(!$str) return '';
    $lower = strtolower($str);
    return ucfirst($lower);
}