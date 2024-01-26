window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/menus66/";

    $("#idPArea").val(idPArea)

    $("#idPArea").change(function(){
      window.location="?idPArea="+$(this).val()
    })

    $("#btnNovoMenu").click(function(){
      limparFormulario("#formularioMenus")
      $("#formularioMenus #action").val("novoMenu")
      $("#formularioMenus").modal("show")
    })

    var repet=true;
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a.alterar").click(function(){
        if(repet==true){
          $("#formularioMenus #action").val($(this).attr("action"))
          idPMenu = $(this).attr("idPMenu");
          $("#formularioMenus #idPMenu").val(idPMenu)

          if($(this).attr("action")=="editarMenu"){
            menusEscolas.forEach(function(dado){
              if(dado.idPMenu==idPMenu){
                $("#formularioMenus #designacaoMenu").val(dado.designacaoMenu)
                $("#formularioMenus #linkMenu").val(dado.linkMenu)
                $("#formularioMenus #instituicao").val(dado.instituicao)
                $("#formularioMenus #icone").val(dado.icone)
                $("#formularioMenus #eGratuito").val(dado.eGratuito)
                $("#formularioMenus #idAreaEspecifica").val(dado.idAreaEspecifica)
                $("#formularioMenus #idAreaPorDefeito").val(dado.idAreaPorDefeito)
                $("#formularioMenus #identificadorMenu").val(dado.identificadorMenu)
                $("#formularioMenus #ordemPorDefeito").val(dado.ordemPorDefeito)
                $("#formularioMenus #somenteOnline").val(dado.somenteOnline)
              }
            })
            $("#formularioMenus").modal("show")
          }else{
            mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este menu?");
          }
          repet=false
        }
      })
    })
    $("#pesqMenu").keyup(function(){
      fazerPesquisa()
    })
    fazerPesquisa();

    $("#formularioMenusForm").submit(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        manipular();
      }
      return false;
    });

     var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
              if(estadoExecucao=="ja"){
                estadoExecucao="espera";
                manipular();
              }
            rep=false;
          }
      })
    })
}

function fazerPesquisa(){
  var tbody="";
  $("#numTCursos").text(completarNumero(menusEscolas.length));
  var i=0;
  menusEscolas.filter(condicao).forEach(function(dado){
    i++;
    numSubMenus=0;
    if(dado.subMenus!=undefined && dado.subMenus!=null){
      numSubMenus=dado.subMenus.length;
    }
    tbody +="<tr><td class=''>"+"("+dado.ordemPorDefeito+")<br>"+dado.areaEspecifica
    +" <br> "+dado.areaPorDefeito+"</td><td class='lead text-center'><i class='"+dado.icone
      +"'></i></td><td class=''>"+dado.designacaoMenu
    +"</td><td class=''>"+dado.identificadorMenu
    +"</td><td class=''>"+dado.instituicao
    +"</td><td class=' text-center'>("+numSubMenus
    +") &nbsp;&nbsp;&nbsp;<a href='../subMenus66/index.php?idPMenu="+dado.idPMenu
    +"'><i class='fa fa-chain'></i></a></td><td class='text-center'><div class='btn-group alteracao text-right'>"+
    "<a class='btn btn-success alterar' title='Editar' href='#as' action='editarMenu' idPMenu='"+dado.idPMenu
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger alterar' n='' title='Excluir' href='#a' action='excluirMenu' idPMenu='"+dado.idPMenu
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
  })
  $("#tabela").html(tbody)
}

  function manipular(){
    $("#formularioMenus").modal("hide")
     var form = new FormData(document.getElementById("formularioMenusForm"));
     enviarComPost(form);
     chamarJanelaEspera("")
     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim();
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            menusEscolas = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
  }

  function condicao(elem, ind, obj){
  return elem.designacaoMenu.toLowerCase().indexOf($("#pesqMenu").val().toLowerCase())>=0
}
