
window.onload = function (){
  
  fecharJanelaEspera();
  seAbrirMenu();
  listarGaleria();

  directorio = "areaDirector/galeriaFotos/";

  $("#novaFoto").click(function(){
    $("#action").val("adicionarFoto");
    $("#legendaFoto").val("");

    $("#formularioNovaFoto").modal("show");
  });

  $("#formularioNovaFoto").submit(function(){
    manipular();
    return false;
  });

  var repet=true;
  $("#fotos").bind("click mouseenter", function(){
      repet=true;
      $("#fotos a").click(function(){
        if(repet==true){
            $("#action").val($(this).attr("id"));
            $("#idPFoto").val($(this).attr("idPFoto"));
            if($(this).attr("id")=="excluirFoto"){
                 mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar esta foto?");
            }
          repet=false;
        }
      });
  });


  var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              idEspera = "#janelaPergunta #pergSim";
              estadoExecucao="espera";
              manipular();
            }
            rep=false;
          }         
      })
    })

}

function listarGaleria(){
  var html="";
  galeriaFotos.forEach(function(dado){
      html +="<div class='col-lg-3 col-md-4 col-sm-12 col-xs-12 divFoto'><img src='../../../Ficheiros/Escola_"+
      dado.idPEscola+"/Galeria/"+dado.fotos.fotoGaleria+"'><p class='lead text-justify; font-size:11pt;'>"
      +fazerParagrafos(dado.fotos.legendaFoto)+"</p>"
      +"<div class='text-center'><a href='#' class='btn btn-danger' idPFoto='"+dado.fotos.idPGaleria+"' id='excluirFoto'><i class='fa fa-times'  title='Excluir a Foto'></i></a></div></div>";
  });
  $("#fotos").html(html);
}

function manipular(){
  $(".modal").modal("hide");
  chamarJanelaEspera();
   var form = new FormData(document.getElementById("formularioNovaFotoForm"));
   enviarComPost(form);
   http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      estadoExecucao="ja";
      resultado = http.responseText.trim()
      if(resultado.substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
          if($("#action").val()=="adicionarFoto"){
              mensagensRespostas("#mensagemCerta", "A Foto foi adicionada com sucesso.");
          }else if($("#action").val()=="excluirFoto"){
            mensagensRespostas("#mensagemCerta", "A foto foi excluida com sucesso.");
          }else if($("#action").val()=="marcarFP"){
            mensagensRespostas("#mensagemCerta", "A foto foi adicionada como a principal.");
          }
          galeriaFotos = JSON.parse(resultado);
          listarGaleria();
      }
    }
  }
}