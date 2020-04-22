function TestaCPF(strCPF) {
    
    /*
     * transforma o cpf em somente números
     */
    strCPF = strCPF.replace(/[\.-]/g, "");
  
    var Soma, Resto;
    Soma = 0;

    if (strCPF == "00000000000" || strCPF == "11111111111" || strCPF == "22222222222" ||
            strCPF == "33333333333" || strCPF == "44444444444" || strCPF == "55555555555" ||
            strCPF == "66666666666" || strCPF == "77777777777" || strCPF == "88888888888" ||
            strCPF == "99999999999"){
            document.getElementById("cpf").setCustomValidity('Número de CPF Inválido');
            return false;
    }

    for (i=1; i<=9; i++){
            Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
    }

    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)){
            Resto = 0;
    }

    if (Resto != parseInt(strCPF.substring(9, 10))){
            document.getElementById("cpf").setCustomValidity('Número de CPF Inválido');
            return false;
    }

    Soma = 0;
    for (i = 1; i <= 10; i++){
            Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    }

    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11)){
            Resto = 0;
    }

    if (Resto != parseInt(strCPF.substring(10, 11))){
            document.getElementById("cpf").setCustomValidity('Número de CPF Inválido');
            return false;
    }

    document.getElementById("cpf").setCustomValidity('');
    return true;
}



function formatar(mascara, documento){
  var i = documento.value.length;
  var saida = mascara.substring(0,1);
  var texto = mascara.substring(i)
  
  if (texto.substring(0,1) != saida){
            documento.value += texto.substring(0,1);
  }
  
}


function mascaraData( campo, e )
{
    var kC = (document.all) ? event.keyCode : e.keyCode;
    var data = campo.value;

    if( kC!=8 && kC!=46 )
    {
        if( data.length==2 )
        {
                campo.value = data += '/';
        }
        else if( data.length==5 )
        {
                campo.value = data += '/';
        }
        else
                campo.value = data;
    }
}

/* Máscaras ER */
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mtel(v){
    v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
    v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
    v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
    return v;
}
function id( el ){
	return document.getElementById( el );
}
//window.onload = function(){
//	id('celular').onkeyup = function(){
//		mascara( this, mtel );
//	}
//}

 $(function () {

    if (localStorage.chkbox && localStorage.chkbox != '') {
        $('#chk_remember').attr('checked', 'checked');
        $('#login').val(localStorage.username);
        $('#senha').val(localStorage.pass);
    } else {
        $('#chk_remember').removeAttr('checked');
        $('#login').val('');
        $('#senha').val('');
    }

    $('#chk_remember').click(function () {

        if ($('#chk_remember').is(':checked')) {
            // save username and password
            localStorage.username = $('#login').val();
            localStorage.pass = $('#senha').val();
            localStorage.chkbox = $('#chk_remember').val();
        } else {
            localStorage.username = '';
            localStorage.pass = '';
            localStorage.chkbox = '';
        }
    });
});

$('#instituicao').change(function(){ 
    var value = $(this).val();
    $('#matricula').prop('required',false);
    if (value == 1){
        $('#matricula').prop('required',true);
    }
});

function getMoney( str )
{
        return parseInt( str.replace(/[\D]+/g,'') );
}
function formatReal( int )
{
        var tmp = int+'';
        tmp = tmp.replace(/([0-9]{2})$/g, ",$1");
        if( tmp.length > 6 )
                tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

        return tmp;
}

function moeda(a, e, r, t) {
    let n = ""
      , h = j = 0
      , u = tamanho2 = 0
      , l = ajd2 = ""
      , o = window.Event ? t.which : t.keyCode;
    if (13 == o || 8 == o)
        return !0;
    if (n = String.fromCharCode(o),
    -1 == "0123456789".indexOf(n))
        return !1;
    for (u = a.value.length,
    h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
        ;
    for (l = ""; h < u; h++)
        -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
    if (l += n,
    0 == (u = l.length) && (a.value = ""),
    1 == u && (a.value = "0" + r + "0" + l),
    2 == u && (a.value = "0" + r + l),
    u > 2) {
        for (ajd2 = "",
        j = 0,
        h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
            j = 0),
            ajd2 += l.charAt(h),
            j++;
        for (a.value = "",
        tamanho2 = ajd2.length,
        h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    return !1;
}
