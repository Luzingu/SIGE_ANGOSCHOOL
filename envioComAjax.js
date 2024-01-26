var http = new XMLHttpRequest();
var resultado ="aindaNao";
var estadoExecucao="ja";
var idEspera ="";
var directorio ="";

function enviarComPost(valores="", directorioInterno=""){
  //verificarSeASessaoEValida();
    if(directorioInterno!=""){
      directorio=directorioInterno+"/";
    }
    http.open("POST", caminhoRecuar+directorio+"manipulacaoDadosDoAjax.php", true);
    http.send(valores);
}

function enviarComGet(valores=""){
 // verificarSeASessaoEValida();
    http.open("GET", caminhoRecuar+directorio+"manipulacaoDadosDoAjax.php?"+valores, true);
    http.send();
}

function reordernarArray(array){
  var i=0;
  var retorno = array;
  retorno.forEach(function(dado){
      dado.chave=i;
      i++;
  });
  return retorno;
}

function verificarSeASessaoEValida(){
  var verfSessao = new XMLHttpRequest();
  verfSessao.onreadystatechange = function(){
      if(verfSessao.readyState==4){
         if(verfSessao.responseText.trim()=="F"){
            window.location=caminhoRecuar+"index";
         } 
      }
  }
  verfSessao.open("GET", caminhoRecuar+"areaDirector/cursos/manipulacaoDadosDoAjax.php?tipoAcesso=verificarSeASessaoEValida", true);
  verfSessao.send();
}



