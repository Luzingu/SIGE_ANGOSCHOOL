var dadosEnviar= new Array()
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/menusParaTodasEscolas/";
    fazerPesquisa()

    $("#pesqMenu").keyup(function(){
      fazerPesquisa()
    })


    $("#tabela").bind("click mouseenter", function(){
      $("#tabela select, #tabela input").bind("keyup change", function(){

        $("#tabela tr#"+$(this).attr("idPMenu")).attr("alterou", "sim")
      })
    })

    $("#btnAlterar").click(function(){
      dadosEnviar = new Array();
      $("#tabela tr[alterou=sim] td select").each(function(){
        dadosEnviar.push({idPMenu:$(this).attr("idPMenu"), idAreaEspecifica:$(this).attr("idAreaEspecifica")
          , idPArea:$(this).val(), ordemMenu:$("#ordem_"+$(this).attr("idPMenu")).val()})
      })
      $("#formularioDados #dadosEnviar").val(JSON.stringify(dadosEnviar))
      luzinguLuame()
    })
}

function fazerPesquisa(){
  var tbody="";
  var i=0;
  listaMenus.filter(condicao).forEach(function(dado){

    ordemMenu = dado.ordemPorDefeito
    idArea = dado.idAreaPorDefeito
    manoPeki=idArea

    i++;
    tbody +="<tr id='"+dado.idPMenu+"'><td class='lead text-center'>"
    +"<input type='number' idPMenu='"+dado.idPMenu+"' class='form-control text-center' value='"+
    ordemMenu+"' step='1' min='0' size='1' id='ordem_"+dado.idPMenu+"'></td><td class='lead'>"+dado.designacaoMenu
    +"</td><td class='lead'>"+dado.areaPorDefeito
    +"</td><td class='lead text-center'>"
    tbody +="<select class='areasSeleccionar form-control' idPMenu='"
    +dado.idPMenu+"' idAreaEspecifica='"+dado.idAreaEspecifica+"'>"
    tbody +="<option value='-1'>Nenhuma Área</option>"
    listaAreas.forEach(function(area){
      var selected="";
      if(area.idPArea==idArea){
        selected="selected"
      }
      tbody +="<option value='"+area.idPArea+"' "+selected+">"+area.designacaoArea+"</option>"
    })
    tbody +="</select></td></tr>";

  })
  $("#tabela").html(tbody)
}

function condicao(elem, ind, obj){
  return elem.designacaoMenu.toLowerCase().indexOf($("#pesqMenu").val().toLowerCase())>=0
}

function luzinguLuame(){
  chamarJanelaEspera("...")
  var form = new FormData(document.getElementById("formularioDados"))
  enviarComPost(form)
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera()
      resultado = http.responseText.trim()
      if(resultado.trim().substring(0, 1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        mensagensRespostas("#mensagemCerta", "alteração efectuada com sucesso.");
        listaMenus = JSON.parse(resultado)
        fazerPesquisa();
      }
    }
  }
}
