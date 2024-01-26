var idPAcessoArea="";
var alteracao="";
var visualizacao="";

window.onload = function(){

  seAbrirMenu();
  fecharJanelaEspera();
  seAbrirMenu();

  listarAcessos();
  directorio = "areaGestaoGPE/CPainel/acessoAreas/";
  $("#idPEscola").val(idPEscola)
  $("#idPEscola").change(function(){
    window.location='?idPEscola='+$(this).val()
  })
  
  var repet=true;
  $("#tabela").bind("click mouseenter", function(){
      repet=true;
      $("#tabela tr td a").click(function(){
        if(repet==true){
          alteracao="";
          visualizacao="";
            $("#alterarAcesso").modal("show");
            $("#alterarAcesso #tituloModal").text($(this).attr("nomeArea"));
            idPAcessoArea = $(this).attr("idPAcessoArea");
            acessoAreas.forEach(function(dado){
                if(dado.idPAcessoArea==idPAcessoArea){

                    var acessoVisualizacao = dado.acessoVisualizacao.split(",");
                    for(var i in acessoVisualizacao){
                      $("#formularioAletrarAcesso #vis"+acessoVisualizacao[i].trim()).prop("checked", true)
                    }

                    var acessoAlteracao  = dado.acessoAlteracao  .split(",");
                    for(var i in acessoAlteracao){
                      $("#formularioAletrarAcesso #alt"+acessoAlteracao [i].trim()).prop("checked", true)
                    }
                }
            })
            repet=false;
        }
      })
  })

  $("#formularioAletrarAcesso").submit(function(){
    $("#formularioAletrarAcesso #alteracao input[type=checkbox]").each(function(){
        if($(this).prop("checked")==true){
           if(alteracao!=""){
              alteracao +=", "+$(this).attr("name");
           }else{
            alteracao +=$(this).attr("name");
           }
        }
    })

    $("#formularioAletrarAcesso #visualizacao input[type=checkbox]").each(function(){
        if($(this).prop("checked")==true){
           if(visualizacao!=""){
              visualizacao +=", "+$(this).attr("name");
           }else{
            visualizacao +=$(this).attr("name");
           }
        }
    })
    alterarAcesso();
      return false;
  })
}

function listarAcessos(){
  var html="";
  acessoAreas.forEach(function(dado){
    html +="<tr><td class='lead'>"+dado.nomeArea+"</td><td class='lead'>"+vazioNull(dado.acessoVisualizacao)
    +"</td><td class='lead'>"+vazioNull(dado.acessoAlteracao)
    +"</td><td><div class='btn-group alteracao text-right'>"
    +"<a class='btn btn-success' title='Editar' nomeArea='"+dado.nomeArea+"' idPAcessoArea='"+dado.idPAcessoArea+"' href='#'><i class='fa fa-user-edit'></i></a></div></td></tr>"
  })
  $("#tabela").html(html)
}

function alterarAcesso(){
  $("#alterarAcesso #alterar").focus();
  $(".modal").modal("hide");
  chamarJanelaEspera("")
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim();
      fecharJanelaEspera()
      
      if(resultado.trim().substring(0, 1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        
        mensagensRespostas("#mensagemCerta", "Dados alterados com sucesso.");
        $("#formularioAletrarAcesso input[type=checkbox]").prop("checked", false);
        acessoAreas = JSON.parse(resultado);
        listarAcessos();
      }
        
    }
  }
  enviarComGet("tipoAcesso=alterarAcesso&idPAcessoArea="+idPAcessoArea+"&alteracao="+alteracao
    +"&visualizacao="+visualizacao+"&idPEscola="+idPEscola);
}