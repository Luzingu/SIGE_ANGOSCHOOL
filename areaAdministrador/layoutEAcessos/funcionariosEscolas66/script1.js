var listaDados= new Array()
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/funcionariosEscolas66/";
    fazerPesquisa();

    $("#idPEscola").val(idPEscola)
    $("#idPEscola").change(function(){
      window.location='?idPEscola='+$(this).val()
    })

    $("#btnAlterar").click(function(){
      listaDados = new Array();
      $("#tabela tr").each(function(){

        idPEntidade = $(this).attr("idPEntidade");
        listaDados.push({idPEntidade:$(this).attr("idPEntidade"),
          luzl:$("#linha_"+idPEntidade+" .luzl").prop("checked"),
          backup:$("#linha_"+idPEntidade+" .backup").prop("checked")})

      })
      luzinguLuame()
    })
}

function fazerPesquisa(){
  var tbody="";
  var i=0;
  listaEntidades.forEach(function(dado){
    i++;
    var luzl="";
    if(dado.escola.LUZL=="V"){
      luzl="checked"
    }

    var backup="";
    if(dado.escola.BACKUP=="V"){
      backup="checked"
    }

    tbody +="<tr idPEntidade='"+dado.idPEntidade+"' id='linha_"+dado.idPEntidade+"'><td class='lead text-center'>"
    +completarNumero(i)+"</td><td class='lead'>"+dado.nomeEntidade
    +"</td><td class='lead'>"+dado.tituloNomeEntidade
    +"</td><td class='lead'>"+dado.escola.nivelSistemaEntidade
    +"</td><td class='lead text-center'><input type='checkbox' class='luzl' idPEntidade='"+dado.idPEntidade
    +"' "+luzl+"></td><td class='lead text-center'><input type='checkbox' class='backup' idPEntidade='"+dado.idPEntidade
    +"' "+backup+"></td></tr>";
  })
  $("#tabela").html(tbody)
}

function luzinguLuame(){
  chamarJanelaEspera("...")
  enviarComGet("tipoAcesso=luzinguLuame&listaDados="
    +JSON.stringify(listaDados)+"&idPEscola="+idPEscola);
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera()
      resultado = http.responseText.trim();
      if(resultado.trim().substring(0, 1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        mensagensRespostas("#mensagemCerta", "alteração efectuada com sucesso.");
        listaEntidades = JSON.parse(resultado)
        fazerPesquisa();
      }
    }
  }
}
