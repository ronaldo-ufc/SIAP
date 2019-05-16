/* global base_url */

$(document).on("click", "#btnAdicionar", function () {
    var info = $(this).attr('data-id');
    var str = info.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#titulo").val(id);
    if(titulo == 'Setor'){
        document.getElementById('sigla').removeAttribute('hidden');
        document.getElementById('sigla_item').setAttribute('type','text');
    }else{
        document.getElementById('sigla').setAttribute('hidden','');
        document.getElementById('sigla_item').setAttribute('type','hidden');
    }
});

$(document).on("click", "#btnEntrada", function () {
    var codigo = $(this).attr('data-id');
    var str = codigo.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#item").val(id);
});

$(document).on("click", "#btnEstorno", function () {
    var codigo = $(this).attr('data-id');
    var str = codigo.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#item").val(id);
});

function aumenta(obj){
    obj.height=obj.height*1000;
    obj.width=obj.width*1000;
}
 
function diminui(obj){
	obj.height=obj.height/2;
	obj.width=obj.width/2;
}

$(document).on("click", "#btnAdicionar", function () {
    var info = $(this).attr('data-id');
    var str = info.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#titulo").val(id);
});

function btnExcluir(url,titulo,mensagem){
    $('#btn_modal_excluir').attr({
       'href': url
    });
    $('#modalLabelExcluir').empty();
    $('#mensagemModal').empty();
    $('#modalLabelExcluir').append(titulo);
    $('#mensagemModal').append(mensagem);
}

function btnCancelar(url,titulo,mensagem){
    $('#btn_modal_cancelar').attr({
       'href': url
    });
    $('#modalLabelCancelar').empty();
    $('#mensagemModalCancel').empty();
    $('#modalLabelCancelar').append(titulo);
    $('#mensagemModalCancel').append(mensagem);
}


function btnReabrir(url){
    $('#btn_modal_reabrir').attr({
       'href': url
    });
}

function btnRemover(patrimonio){
    $('#input_hidden').attr({
       'value': patrimonio
    });
}

function patrimonioRemover(){
    
    var pat = document.getElementById('input_hidden').value;
    document.getElementById(pat).remove();
//    console.log(pat);
}

$("#busca_item").keyup(function () {
    var nomeProduto = $("#busca_item").val();
    if (nomeProduto.length <= 3) return;
    $.ajax({

        url: "http://10.5.5.10/siap/materiais/seach/"+nomeProduto,
        dataType: 'html',
        data: {produto: nomeProduto},
        type: "POST",

        beforeSend: function () {
            $('#carregando').show();
         
        },
        success: function (data) {
            $('#carregando').hide();
            $("#resBusca").html(data);

        },
        error: function (data) {
             $('#carregando').html(data);
            
        }



    });
});

function choiceProduto(id){
    $("#resBusca").html('');
    $.ajax({

        url: "http://10.5.5.10/siap/materiais/seach/itens/"+id,
        dataType: 'html',
        data: {produto: id},
        type: "POST",

        beforeSend: function () {
            $('#carregando').show();
         
        },
        success: function (data) {
            $('#carregando').hide();
            $("#resChoice").html(data);

        },
        error: function (data) {
             $('#carregando').html(data);
            
        }



    });
    
}

function setRecebimento(cod){
    $('#requisicao').val(cod);
}
function editarProduto(id){ 
    var URL = base_url+'/materiais/produto/editar/'+id;
    $(window.document.location).attr('href', URL);
}

function escolherImagem(img){
    $("#perfil").attr('src', base_url+'/uploads/imagem/'+img);
    $("#img_cod").val(img);
    document.getElementById('fileUpload').addEventListener('change', fileChanged(), false);
}


function setEstorno(cod, prod){
    $('#requisicao').val(cod);
    $('#produto').val(prod);
}

   

function fileChanged() {
         var hiddenField = document.getElementById('img_cod');
        function onLoad(e) {
            // Adicionando o arquivo em base64 ao hidden field:
            hiddenField.innerHTML = e.target.result;
        }

        if (this.files && this.files[0]) {
            var fileReader = new FileReader();
            fileReader.onload = onLoad;
            // Isso vai transformar o arquivo em uma string base64:
            fileReader.readAsDataURL(this.files[0]);
        }
}





