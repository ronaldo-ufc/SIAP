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

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @return string filename of moved file
 */
function moveUploadedFile($directory, $uploadedFile)
{
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
function moveUploadedImage($directory, $uploadedFile)
{
  $imagem = array("jpg", "jpeg", "png"); 
  $extension = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
  if (in_array($extension, $imagem)) {
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    reduzir($directory.$filename);
    
    return $filename;
  }
  return null;
}

//// Função que irá redimensionar nossa imagem
//function redimensionar($caminho, $nomearquivo){
//    $tipos = array("jpeg", "jpg", "png");
//    // Determina as novas dimensões
//    $width = 100;
//    $height = 100;
//    
//    $uploadfile = $caminho.$nomearquivo;
//    
//    // Pegamos sua largura e altura originais
//    list($width_orig, $height_orig) = getimagesize($uploadfile);   
//    
//    // Pegamos a largura e altura originais, além do tipo de imagem
//    list($width_orig, $height_orig, $tipo, $atributo) = getimagesize($caminho.$nomearquivo);
//
//    // Se largura é maior que altura, dividimos a largura determinada pela original e multiplicamos a altura pelo resultado, para manter a proporção da imagem
//    if($width_orig > $height_orig){
//    $height = ($width/$width_orig)*$height_orig;
//    // Se altura é maior que largura, dividimos a altura determinada pela original e multiplicamos a largura pelo resultado, para manter a proporção da imagem
//    } elseif($width_orig < $height_orig) {
//    $width = ($height/$height_orig)*$width_orig;
//    } // -> fim if
//    // Criando a imagem com o novo tamanho
//    $novaimagem = imagecreatetruecolor($width, $height);
//    switch($tipo){
//
//      // Se o tipo da imagem for gif
//      case 0:
//      // Obtém a imagem gif original
//      $origem = imagecreatefromgif($caminho.$nomearquivo);
//      // Copia a imagem original para a imagem com novo tamanho
//      imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
//      // Envia a nova imagem gif para o lugar da antiga
//      imagegif($novaimagem, $caminho.$nomearquivo);
//      break;
//
//      // Se o tipo da imagem for jpg
//      case 1:
//      // Obtém a imagem jpg original
//      $origem = imagecreatefromjpeg($caminho.$nomearquivo);
//      // Copia a imagem original para a imagem com novo tamanho
//      imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
//      // Envia a nova imagem jpg para o lugar da antiga
//      imagejpeg($novaimagem, $caminho.$nomearquivo);
//      break;
//
//      // Se o tipo da imagem for png
//      case 2:
//      // Obtém a imagem png original
//      $origem = imagecreatefrompng($caminho.$nomearquivo);
//      // Copia a imagem original para a imagem com novo tamanho
//      imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
//      // Envia a nova imagem png para o lugar da antiga
//      imagepng($novaimagem, $caminho.$nomearquivo);
//      break;
//    } // -> fim switch
//
//    // Destrói a imagem nova criada e já salva no lugar da original
//    imagedestroy($novaimagem);
//    // Destrói a cópia de nossa imagem original
//    imagedestroy($origem);
//  } // -> fim function redimensionar()
  
  function reduzir($filename){
     // O arquivo. Dependendo da configuração do PHP pode ser uma URL.
   //$filename = 'original.jpg';
   //$filename = 'http://exemplo.com/original.jpg';

   // Largura e altura máximos (máximo, pois como é proporcional, o resultado varia)
   // No caso da pergunta, basta usar $_GET['width'] e $_GET['height'], ou só
   // $_GET['width'] e adaptar a fórmula de proporção abaixo.
   $width = 200;
   $height = 200;

   // Obtendo o tamanho original
   list($width_orig, $height_orig) = getimagesize($filename);

   // Calculando a proporção
   $ratio_orig = $width_orig/$height_orig;

   if ($width/$height > $ratio_orig) {
      $width = $height*$ratio_orig;
   } else {
      $height = $width/$ratio_orig;
   }

   // O resize propriamente dito. Na verdade, estamos gerando uma nova imagem.
   $image_p = imagecreatetruecolor($width, $height);
   $image = imagecreatefromjpeg($filename);
   imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

   // Ou, se preferir, Salvando a imagem em arquivo:
   imagejpeg($image_p, 'nova.jpg', 75);
  }