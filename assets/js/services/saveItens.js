$(document).on("click", "#btnAdicionar", function () {
    var info = $(this).attr('data-id');
    var str = info.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#item").val(id);
});

$(document).on("click", "#btnSairModalItens", function () {
    $("#titulo").html();
    $("#nome_item").val('');
    $("#alerta").hide();
});

$(document).on("click", "#btnSalvarItens", function () {
    var item = $("#item").val();
    var valor = $("#nome_item").val();
    var fab = $("#marca").val();
    var sigla = $('#sigla_item').val();
    document.getElementById('nome_item').value='';  // Limpa o campo
    document.getElementById('sigla_item').value=''; // Limpa o campo
    console.log(sigla);
    $.post("http://"+location.hostname+"/siap/services/salvar/item", "item="+item+"&nome="+valor+"&marca="+fab+"&sigla="+sigla, function( data ) {
        $("#alerta").show();
        $("#mensagem").html(data);
        atualizaSelect(item);
    });
});

function atualizaSelect(item) {
  
    var url = 'http://'+location.hostname+'/siap/services/receber/item/'+item;

    $.getJSON(url, function (data)  {

      var option_menu;

      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#'+item).html(option_menu);
    });                            
}


