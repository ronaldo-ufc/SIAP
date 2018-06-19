<?php
header('Content-Type: text/html; charset=UTF-8');

function validaCPF($cpf = null) {
 
    // Verifica se um número foi informado
    if(empty($cpf)) {
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

function formataCPF($nbr_cpf){
  $parte_um     = substr($nbr_cpf, 0, 3);
  $parte_dois   = substr($nbr_cpf, 3, 3);
  $parte_tres   = substr($nbr_cpf, 6, 3);
  $parte_quatro = substr($nbr_cpf, 9, 2);

  return $parte_um.".".$parte_dois.".".$parte_tres."-".$parte_quatro;
}

function desFormataCPF($valor){
  $valor = trim($valor);
  $valor = str_replace(".", "", $valor);
  $valor = str_replace(",", "", $valor);
  $valor = str_replace("-", "", $valor);
  $valor = str_replace("/", "", $valor);
  return $valor;
}

function desFormataCelular($valor){
  $valor = trim($valor);
  $valor = str_replace("(", "", $valor);
  $valor = str_replace(")", "", $valor);
  $valor = str_replace(" ", "", $valor);
  $valor = str_replace("-", "", $valor);
  return $valor;
}

function formatDate($data){
  $_data = split('-', $data);
  return $_data[2]."/".$_data[1]."/".$_data[0];
}

function formatHora($hora){
  $_var = explode(":", $hora);
  return $_var[0].":".$_var[1];
}

function retiraElementoArray($arr, $_var){
  
  $newArr = array();

  foreach($arr as $value)
  {
    if( $value != $_var )
    {
      array_push($newArr, $value);
    }
  }
  return $newArr;
}

function formatCelular ($tipo)
{
    $string  = ereg_replace("[^0-9]", "", $string);
    
    $string = '(' . substr($tipo, 0, 2) . ') ' . substr($tipo, 2, 5) 
             . '-' . substr($tipo, 7);
       
    return $string;
}

function geraRegistroCetificado($evento, $tipo, $modalidade, $inscricao){
  return $evento.'U'.$tipo.'F'.$modalidade.'C'.$inscricao;  
}

function getMensagemByCodigo($codigo, $txt = NULL){ 
    switch ($codigo){
        case 1:
            return '<div class="alert alert-danger">Não encontramos nenhum usuário com o Login: <strong>'.$txt.'</strong>. Favor inserir um Login válido.</div>';
            break;
        case 2:
            return '<div class="alert alert-success"><p class="text-center">Você deverá receber em breve um e-mail permitindo a redefinição de sua senha. Por favor, verifique seus spams e lixo caso não o encontre.</p></div>';
            break;
        case 3:
            return '<div class="alert alert-danger">Não foi possível fazer a recuperação de senha. Por favor, contate o administrador do sistema</div>';
            break;
        case 4:
            return '<div class="alert alert-info"><strong>'.$txt.'</strong></div>';
            break;
        case 5:
            return '<div class="alert alert-danger"><p><strong>As senhas digitadas nos campos não conferem.</strong></p></div>';
            break;
        case 6:
            return '<div class="alert alert-success"><p class="text-center"><strong>Senha alterada com sucesso.</strong></p></div>';
            break;
        ######novos  
        case 7:
            return '<div class="alert alert-danger">'.$txt.'</div>';
            break;
        case 8:
            return '<div class="alert alert-warning">'.$txt.'</div>';
            break;
        case 9:
            return '<div class="alert alert-success">'.$txt.'</div>';
            break;
        case 10:
            return '<div class="alert alert-success"><p class="text-center"><strong>E-mail cadastrado com sucesso. <a href="/sigce/recuperar/senha">Clique aqui</a> para ser redirecionado para a Redefinição de Senha</strong></p></div>';
            break;
    }
}

function enviaEmail($nome, $email, $assunto, $msg_,$remetente) {
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



function criaLinkRecuperacao($login){
    $dia = date('Y-m-d H:m:s');
    return md5($login.$dia);
}
function montaEmailRedefinicao($codigo_autenticacao,$pessoa){
    $url = 'http://www.crateus.ufc.br/sigce/recuperar/redefinicao/' . $codigo_autenticacao;
    $assunto = 'Redefinição de Senha';
    $assunto = '=?UTF-8?B?' . base64_encode($assunto) . '?=';
    $remetente = 'SIGCE - Sistema de Gestão de Certificados e Eventos';
    $remetente = '=?UTF-8?B?' . base64_encode($remetente) . '?=';
    $nome = $pessoa->getNome();
    $mensagem = montaMensagem($nome, $url);
    return enviaEmail($nome, $pessoa->getEmail(), $assunto, $mensagem, $remetente);
   
}

function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI"))
{
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

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(Ç)/", "/(ç)/"),explode(" ","a A e E i I o O u U n N C c"),$string);
}