<?php
function dataext($data_tipo, $data_valor) { // $valor deve ser uma data completa no formato dd/mm/aaaa
				  // $tipo deve ser 1,2,3,4 ou 5
	$data_valor=explode("/",$data_valor);			
	$data_dianum=intval($data_valor[0]);
	$data_mesnum=intval($data_valor[1]);
	$data_anonum=intval($data_valor[2]);
	//$dianum=substr($valor,0,2);
	//$mesnum=substr($valor,3,2);
	//$anonum=substr($valor,6,4);
	$data_diasemnum=date("w",mktime(0,0,1,$data_mesnum,$data_dianum,$data_anonum));
	if ($data_diasemnum==0):
		$data_diaext="DOMINGO";
	elseif ($data_diasemnum==1):
		$data_diaext="SEGUNDA-FEIRA";
	elseif ($data_diasemnum==2):
		$data_diaext="TERÇA-FEIRA";
	elseif ($data_diasemnum==3):
		$data_diaext="QUARTA-FEIRA";
	elseif ($data_diasemnum==4):
		$data_diaext="QUINTA-FEIRA";
	elseif ($data_diasemnum==5):
		$data_diaext="SEXTA-FEIRA";
	elseif ($data_diasemnum==6):
		$data_diaext="SÁBADO";
	endif;
	if ($data_diasemnum==0):
		$data_diaextL="domingo";
	elseif ($data_diasemnum==1):
		$data_diaextL="segunda-feira";
	elseif ($data_diasemnum==2):
		$data_diaextL="terça-feira";
	elseif ($data_diasemnum==3):
		$data_diaextL="quarta-feira";
	elseif ($data_diasemnum==4):
		$data_diaextL="quinta-feira";
	elseif ($data_diasemnum==5):
		$data_diaextL="sexta-feira";
	elseif ($data_diasemnum==6):
		$data_diaextL="sábado";
	endif;
	if ($data_mesnum==1):
		$data_mesext="janeiro";
	elseif ($data_mesnum==2):
		$data_mesext="fevereiro";
	elseif ($data_mesnum==3):
		$data_mesext="março";
	elseif ($data_mesnum==4):
		$data_mesext="abril";
	elseif ($data_mesnum==5):
		$data_mesext="maio";
	elseif ($data_mesnum==6):
		$data_mesext="junho";
	elseif ($data_mesnum==7):
		$data_mesext="julho";
	elseif ($data_mesnum==8):
		$data_mesext="agosto";
	elseif ($data_mesnum==9):
		$data_mesext="setembro";
	elseif ($data_mesnum==10):
		$data_mesext="outubro";
	elseif ($data_mesnum==11):
		$data_mesext="novembro";
	elseif ($data_mesnum=="12"):
		$data_mesext="dezembro";
	endif;
	if ($data_tipo==1):	// Dia da semana UPPER + dia do m�s + nome do m�s + ano
		$data_retorno= "$data_diaext, $data_dianum de $data_mesext de $data_anonum";
	elseif ($data_tipo==2):	// Dia do m�s + nome do m�s + ano
		$data_retorno= "$data_dianum de $data_mesext de $data_anonum";
	elseif ($data_tipo==3):	// Nome do m�s + ano
		$data_retorno= ucfirst($data_mesext) . " de $data_anonum";
	elseif ($data_tipo==4):// Nome do m�s
		$data_retorno= ucfirst($data_mesext);
	elseif ($data_tipo==5):	// Dia da semana LOWER + dia do m�s + nome do m�s + ano
		$data_retorno= "$data_diaextL, $data_dianum de $data_mesext de $data_anonum";
	endif;

	return $data_retorno;
}

function formatoDMY($data_data) { // Data recebida no formato aaaa-mm-dd
	if (!empty($data_data)){
		$data_data=explode("-",$data_data);
		$data_data=$data_data[2]."/".$data_data[1]."/".$data_data[0];
		return $data_data;
	}else{
		return "";
	}
}
function formatoYMD_SH($data_data) { // Data recebida no formato aaaa-mm-dd ou aaaa/mm/dd
	if (!empty($data_data)){
		$data_data=explode(" ",$data_data);
		$data_dat=$data_data[0];
		return $data_dat;
	}else{
		return "";
	}
}

function formatoYMD($data_data) { // Data recebida no formato dd/mm/aaaa
	if (!empty($data_data)){
		$data_data=explode("/",$data_data);
		$data_data=$data_data[2]."-".$data_data[1]."-".$data_data[0];
		return $data_data;
	}else{
		return "";
	}
}

function formatoYMD_($data_data) { // Data recebida no formato dd-mm-aaaa
	if (!empty($data_data)){
		$data_data=explode("-",$data_data);
		$data_data=$data_data[2]."/".$data_data[1]."/".$data_data[0];
		return $data_data;
	}else{
		return "";
	}
}

function formatoDateToDataHora($data_data, $data_forma) {
//	Parametro $data_forma
//	DMY NNN -	data recebida no formato aaaa-mm-dd com ou sem hora e retorna somente a data dd/mm/aaaa
//	YDM NNN -	data recebida no formato dd/mm/aaaa com ou sem hora e retorna somente a data aaaa-mm-dd
//	DMY HMN	-	data recebida no formato aaaa-mm-dd com hora e retorna a data dd/mm/aaaa e a hora hh:mm
//	DMY HMS -	data recebida no formato aaaa-mm-dd com hora e retorna a data dd/mm/aaaa e a hora hh:mm:ss
//	DMY AAA -	data recebida no formato aaaa-mm-dd SEM hora e retorna a data dd/mm/aaaa e a hora atual do servidor
//	YMD AAA -	data recebida no formato dd/mm/aaaa SEM hora e retorna a data dd/mm/aaaa e a hora atual do servidor
	$data_data=explode(" ",$data_data);
	$data_hora=array_key_exists("1",$data_data)?$data_data[1]:"";
	$data_sep=" ";
	$data_forma=explode(" ",$data_forma);
	if ($data_forma[0]=="DMY") $data_data=formatoDMY($data_data[0])." ";
	if ($data_forma[0]=="YMD") $data_data=formatoYMD($data_data[0])." ";
	if ($data_forma[0]=="NNN") {$data_data=""; $data_sep="";}
	if ($data_forma[1]=="NNN") $data_hora="";
	if ($data_forma[1]=="HMN") $data_hora= substr($data_hora,0,5);
	if ($data_forma[1]=="HMS") $data_hora= $data_hora;
	if ($data_forma[1]=="AAA") $data_hora= date("H:i:s");
	
	return $data_data.$data_sep.$data_hora;
}

// Data recebida no formato aaaa-mm-dd
function getUltimoDiaUtil($data) {
        $con = new Conexao();
        $sql = "select * from get_ult_dia('$data')";
        $result=@ibase_query($sql);
        $campo=@ibase_fetch_object($result);
        $ult_dia=$campo->ULT_DIA;

        @ibase_free_result($result); 
        
        $ult_dia_= explode("-",$ult_dia);
       
        $diasemnum=date("w",mktime(0,0,0,$ult_dia_[1],$ult_dia_[2],$ult_dia_[0]));
                  
        if ($diasemnum==0)
                $dia=$ult_dia_[2]-2;
        elseif($diasemnum==6)
                $dia=$ult_dia_[2]-1;
        else
                $dia=$ult_dia_[2];
        return $ult_dia_[0]."/".$ult_dia_[1]."/".$dia;
        
}

// Data recebida no formato aaaa/mm/dd
function getQtdDiaUtil($data) {
    $cont=0;
    $hoje=date("Y/m/d");
    while(strtotime($data)<strtotime($hoje)){
        $data=date('Y/m/d', strtotime($data. ' + 1 days'));
        $ult_dia_= explode("/",$data);
        $diasemnum=date("w",mktime(0,0,0,$ult_dia_[1],$ult_dia_[2],$ult_dia_[0]));
        if ($diasemnum>0 && $diasemnum<6)
            $cont++;
    }
    return $cont;
}

// Data recebida no formato aaaa-mm-dd
function getUltimoDia($data) {
        $con = new Conexao();
        $sql = "select * from get_ult_dia('$data')";
        $result=@ibase_query($sql);
        $campo=@ibase_fetch_object($result);
        $ult_dia=$campo->ULT_DIA;
        
        @ibase_free_result($result); 
        
        $ult_dia_=explode("/",$ult_dia);
        return $ult_dia_[2].$ult_dia_[1].$ult_dia_[0];					
}

function moedaToFloat($valor, $decimal){
    $valor = trim($valor);
    if(preg_match("~^([0-9]+|(?:(?:[0-9]{1,3}([.,' ]))+[0-9]{3})+)(([.,])[0-9]{0,$decimal})?$~", $valor, $r)){
        if(!empty($r['2'])){
            $pre = preg_replace("~[".$r['2']."]~", "", $r['1']);
        }else{
            $pre = $r['1'];
        }
        if(!empty($r['4'])){
            $post = ".".preg_replace("~[".$r['4']."]~", "", $r['3']);
        }else{
            $post = false;
        }
        $form_valor = $pre.$post;
        return $form_valor;
    }
    return false;
}
?>
