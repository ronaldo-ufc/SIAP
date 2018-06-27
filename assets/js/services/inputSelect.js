function menuSelect() {
    
    var e = document.getElementById("privilegio");
    var itemSelecionado = e.options[e.selectedIndex].value;

    var url = 'http://10.5.5.10/siap/services/menu/'+itemSelecionado;
    
    $.getJSON(url, function (data)  {
            
      var option_menu = '<option value="">Selecione um menu... </option>';
     
      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#menu').html(option_menu);
    });
                              
}

function subMenuSelect() {
    var e = document.getElementById("privilegio");
    var privilegio = e.options[e.selectedIndex].value;
    
    var f = document.getElementById("menu");
    var menu = f.options[f.selectedIndex].value;
    
    console.log('Privilegio '+privilegio);
    console.log(menu);
    var url = 'http://10.5.5.10/siap/services/submenu/'+privilegio+'/'+menu;
    
    $.getJSON(url, function (data)  {
            
      var option_menu;
     
      $.each(data, function (i, val) {
        var c = "";
        if (val[2] === 'S'){
            c = "checked";
        }
          option_menu +=  '<tr class="text-center"><td><input '+c+' name="chk[]" value="'+ val[0] +'" type="checkbox"> </td> <td>'+val[1]+'</td></tr>';
      });
      
      
      $('#submenu').html(option_menu);
    });
                              
}

function modeloSelect() {
    
    var e = document.getElementById("fabricante");
    var itemSelecionado = e.options[e.selectedIndex].value;

    var url = 'http://10.5.5.10/siap/services/modelos/'+itemSelecionado;

    $.getJSON(url, function (data)  {

      var option_menu = '<option value="">Selecione um modelo... </option>';

      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#modelo').html(option_menu);
    });
                              
}

function atualizaSelect(item) {
  
    var url = 'http://10.5.5.10/siap/services/receber/item/'+item;

    $.getJSON(url, function (data)  {

      var option_menu;

      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#'+item).html(option_menu);
    });                            
}



