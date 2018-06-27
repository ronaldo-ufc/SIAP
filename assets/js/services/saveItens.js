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
    var fab = $("#fabricante").val();
    $.post("http://10.5.5.10/siap/services/salvar/item", "item="+item+"&nome="+valor+"&fabricante="+fab, function( data ) {
        $("#alerta").show();
        $("#mensagem").html(data);
        atualizaSelect(item);
    });
});

