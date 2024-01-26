window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/subMenus66/";

    $("#btnNovoMenu").click(function(){
      limparFormulario("#formularioSubMenus")
      $("#formularioSubMenus #action").val("novoSubMenu")
      $("#formularioSubMenus").modal("show")
    })

    var repet=true;
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          $("#formularioSubMenus #action").val($(this).attr("action"))
          idPSubMenu = $(this).attr("idPSubMenu");
          $("#formularioSubMenus #idPSubMenu").val(idPSubMenu)

          if($(this).attr("action")=="editarMenu"){
            subMenusEscolas.forEach(function(dado){
              if(dado.subMenus.idPSubMenu==idPSubMenu){
                $("#formularioSubMenus #somenteOnline").val(dado.subMenus.somenteOnline)
                $("#formularioSubMenus #designacaoSubMenu").val(dado.subMenus.designacaoSubMenu)
                $("#formularioSubMenus #linkSubMenu").val(dado.subMenus.linkSubMenu)
                $("#formularioSubMenus #identificadorSubMenu").val(dado.subMenus.identificadorSubMenu)
              }
            })
            $("#formularioSubMenus").modal("show")
          }else{
            mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este sub menu?");
          }
          repet=false
        }
      })
    })
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioSubMenusForm").submit(function(){
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
  $("#numTCursos").text(completarNumero(subMenusEscolas.length));
  var i=0;
  subMenusEscolas.forEach(function(dado){
    i++;
    tbody +="<tr><td class='lead text-center'>"
    +completarNumero(i)+"</td><td class='lead'>"+dado.subMenus.designacaoSubMenu
    +"</td><td class='lead'>"+dado.subMenus.identificadorSubMenu
    +"</td><td class='lead'>"+dado.subMenus.linkSubMenu
    +"</td><td class='text-center'>"+
    "<div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarMenu' idPSubMenu='"+dado.subMenus.idPSubMenu
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirMenu' idPSubMenu='"+dado.subMenus.idPSubMenu
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
  })
  $("#tabela").html(tbody)
}

  function manipular(){
    $("#formularioSubMenus").modal("hide")
     var form = new FormData(document.getElementById("formularioSubMenusForm"));
     enviarComPost(form);
     chamarJanelaEspera("")
     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            subMenusEscolas = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
  }
