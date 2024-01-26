 window.onload=function(){

    if(navigator.appVersion.toLowerCase().indexOf("android".toLowerCase().trim())>=0){
      $(".paraBaixarNoTelefone").show(1200)
     
    }

    var estadoEspera="ja";
    $("form#formLogin").submit(function(){

      if(estadoEspera=="ja"){
        estadoEspera="aindaNao";

          var form = new FormData(document.getElementById("formLogin"));
          var x = new XMLHttpRequest();
          $("form#formLogin button[type=submit]").html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Autenticando...');
            x.onreadystatechange = function(){
            if(x.readyState==4){
              estadoEspera="ja";
              $("form#formLogin button[type=submit]").html('<i class="fa fa-sign-out-alt"></i> Entrar');
              if(x.responseText.trim().substring(0, 1)=="F"){
                $(" #linhaErro").text(x.responseText.trim().substring(1, x.responseText.trim().length)).show("slow");
              }else{
                window.location =x.responseText.trim()+"?login=sim";
              }            
            }
          }
          x.open("POST", caminhoRecuar+"login/entrar/manipulacaoDadosDoAjax.php", true);
          x.send(form);
      }
          return false;
      
    });
}
$("#numeroInterno").keyup(function (argument) {
   $(" #linhaErro").hide("slow")
})
$("#password").keyup(function (argument) {
   $(" #linhaErro").hide("slow")
})