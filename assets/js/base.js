function mudando(num){
    
    var classe, chk;
    classe = document.getElementById(num).className; 
    chk = document.getElementById('id'+num); 
    
    
    if(chk.checked){
       document.getElementById(num).className = 'text-success';
       chk.checked=true;
   }else{
       document.getElementById(num).className = 'text-danger';
       chk.checked = false;
   }
   
   document.forms['chekin'].submit();
    
}

function loadFast(url,div,script) {
    if (window.XMLHttpRequest) {
            eval(div+"_rq = new XMLHttpRequest()");
    } else if (window.ActiveXObject) {
            eval(div+"_rq = new ActiveXObject(\"Microsoft.XMLHTTP\")");
    }
    eval(div+"_rq.open(\"GET\", '"+url+"', true)");
    eval(div+"_rq.onreadystatechange = function () { if ("+div+"_rq.readyState == 4) { if ("+div+"_rq.status == 200) { document.getElementById('"+div+"').innerHTML = "+div+"_rq.responseText; " + script + " };};}");
    eval(div+"_rq.send(null)");
}

function printCertificate(id, form){
    $('#programa'+form).val(id);
    $('#'+form).submit();
}

function selecionando(num){
 
    var chk;
    
    chk = document.getElementById('chk-'+num); 
        
    if(!chk.checked){
       document.getElementById('linha-'+num).className = 'text-success';
       chk.checked=true;
   }else{
       document.getElementById('linha-'+num).className = 'text-info';
       chk.checked = false;
   } 
}