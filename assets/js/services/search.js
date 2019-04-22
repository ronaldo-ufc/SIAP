$(document).ready(function () {
    var tabela = $('#tabela').DataTable({
        "lengthMenu": [[50, 25, 20, 15, 10, 5, -1], [50, 25, 20, 15, 10, 5, "TUDO"]],
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(Filtrado de _MAX_ registros no total)",
            "search": "Pesquisa",
            "paginate": {
                first: "Primeira página",
                previous: "Anterior",
                next: "Próximo",
                last: "Última página"
            }
        }
        });

//    $('#tabelaAtivos tfoot th').each( function () {
//        var title = $(this).text();
//        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
//    } );
    
    var tabelaAtivos = $('#tabelaAtivos').DataTable({
        "lengthMenu": [[50, 25, 20, 15, 10, 5, -1], [50, 25, 20, 15, 10, 5, "TUDO"]],
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(Filtrado de _MAX_ registros no total)",
            "search": "Pesquisa",
            "paginate": {
                first: "Primeira página",
                previous: "Anterior",
                next: "Próximo",
                last: "Última página"
            }
        }
    });
    var tabelaAtivos = $('#tabelaItens').DataTable({
        "lengthMenu": [[50, 25, 20, 15, 10, 5, -1], [50, 25, 20, 15, 10, 5, "TUDO"]],
//        "bSort": false,
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Total _MAX_ registros - Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro disponível",
            "infoFiltered": "(Filtrado de _MAX_ registros no total)",
            "search": "Pesquisa",
            "paginate": {
                first: "Primeira página",
                previous: "Anterior",
                next: "Próximo",
                last: "Última página"
            }
        }
    });
});

function notify(icon,title,mensagem){
    
    
    //Verificando se o Browser tem o Notification
    if(!Notification){
        alert('O navegador que você está utilizando não possui o Notification. Tente o Chrome.');
        return;
    }
    
    //Verificando se a permissão foi concedida
    if(Notification.permission !== "granted"){
        Notification.requestPermission();
    }else{
        var notification = new Notification(title,{
            icon: icon,
            body: mensagem
        });
        notification.onclick = function(){
            ;
        }
    }
}



function verificarCheckBox() {
    var checkbox = $('input:checkbox[name^=patrimonios]:checked');
    if(checkbox.length == 1) {
        window.location.href = "http://"+location.hostname+"/siap/ativo/movimentacao/"+checkbox.val();
    }
    else if(checkbox.length > 1){
        let url = "http://"+location.hostname+"/siap/ativo/mov/grupo/";
        for (var i=0;i<checkbox.length;i++){
            if(i == checkbox.length-1){
                url = url+checkbox[i].value;
            }
            else{
                url = url+checkbox[i].value+"/";
            }
    }
//        document.getElementById("myform").submit();
    
//    console.log(url);
        window.location.href = url;
    }
    else{
        //window.location.href = "http://"+location.hostname+"/siap/ativo/show";
        alert('Para realizar uma movimentação em lote, é necessário selecionar 2 ou mais bens.');
    }
}

function verificarCheckBoxRelatorio() {
    var checkbox = $('input:checkbox[name^=patrimonios]:checked');
    if(checkbox.length == 1) {
        window.location.href = "http://"+location.hostname+"/siap/relatorios/setor/movimentacao/"+checkbox.val();
    }
    else if(checkbox.length > 1){
        let url = "http://"+location.hostname+"/siap/relatorios/setor/mov/grupo/";
        for (var i=0;i<checkbox.length;i++){
            if(i == checkbox.length-1){
                url = url+checkbox[i].value;
            }
            else{
                url = url+checkbox[i].value+"/";
            }
    }
        window.location.href = url;
    }
    else{
        alert('Para gerar um relatório em conjunto é necessário selecionar 2 ou mais bens.');
    }
}