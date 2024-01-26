var dadosEnviar= new Array()
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/menusPorInstituicao66/";
    fazerPesquisa()

    $("#idPEscola").val(idPEscola)
    $("#idPArea").val(idPArea)
    $("#idPEscola, #idPArea").change(function(){
      window.location='?idPEscola='+$("#idPEscola").val()+"&idPArea="+$("#idPArea").val()
    })

    $("#pesqMenu").keyup(function(){
      fazerPesquisa()
    })


    $("#tabela").bind("click mouseenter", function(){
      $("#tabela select").bind("keyup change", function(){
        $(this).attr("alterou", "sim")
      })
    })

    $("#btnAlterar").click(function(){
      dadosEnviar = new Array();
      $("#tabela tr td select").each(function(){
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
    var idArea="";
    var ordemMenu="";
    if(dado.instituicoes!=undefined){
      dado.instituicoes.forEach(function(areaS){
        if(areaS.idEscola==idPEscola){
          idArea = areaS.idArea
          ordemMenu = areaS.ordemMenu
        }
      })
    }

    if(ordemMenu=="" || ordemMenu==null || ordemMenu==undefined){
      ordemMenu = dado.ordemPorDefeito
    }
    alterou=""
    var manoPeki=idArea
    if(idArea=="" || idArea==null || idArea==undefined){
      idArea = dado.idAreaPorDefeito
      alterou="sim";
    }


    if(idPArea==0 || manoPeki==idPArea
    || (idPArea=="" && (manoPeki==null || manoPeki==undefined || manoPeki=="" || manoPeki=="-1"))){

      i++;
      tbody +="<tr><td class='lead text-center'>"
      +"<input type='number' class='form-control text-center' value='"+
      ordemMenu+"' step='1' min='0' size='1' id='ordem_"+dado.idPMenu+"'></td><td class='lead'>"+dado.designacaoMenu
      +"</td><td class='lead'>"+dado.areaPorDefeito
      +"</td><td class='lead text-center'>"
      tbody +="<select alterou='"+alterou+"' class='areasSeleccionar form-control' idPMenu='"
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
    }
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
