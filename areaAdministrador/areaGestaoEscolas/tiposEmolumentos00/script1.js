window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="cursos";
    directorio = "areaGestaoEscolas/tiposEmolumentos00/";
    fazerPesquisa();

    $("#novoEmolumento").click(function(){
      $("#formularioTipoEmolumentos #action").val("novoTipoEmolumento")
      $("#formularioTipoEmolumentos .vazio").val("")
      $("#formularioTipoEmolumentos").modal("show")
    })
    DataTables("#example1", "sim")

    $("#formularioTipoEmolumentos form").submit(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        manipular();
      } 
      return false;
    });
    
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPTipoEmolumento = $(this).attr("idPTipoEmolumento")
              $("#formularioTipoEmolumentos #action").val("editarCodigoEmolumento")
          tipos_emolumentos.forEach(function(dado){
            if(dado.idPTipoEmolumento==idPTipoEmolumento){
              $("#formularioTipoEmolumentos #idPTipoEmolumento").val(idPTipoEmolumento)
              $("#formularioTipoEmolumentos #codigo").val(dado.codigo)
              $("#formularioTipoEmolumentos #tipoPagamento").val(dado.tipoPagamento)
              $("#formularioTipoEmolumentos #designacaoEmolumento").val(dado.designacaoEmolumento)
            }
          })
          $("#formularioTipoEmolumentos").modal("show")
          repet=true
        }
      })
    })
}

function fazerPesquisa(){
  var tbody="";
      var i=0;
    tipos_emolumentos.forEach(function(dado){
      i++;
      
      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(i)+"</td><td class='lead'>"+dado.codigo
      +"</td><td class='lead'>"+dado.designacaoEmolumento 
      +"</td><td class='lead'>"+dado.tipoPagamento
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarEmolumento'"+
      " idPTipoEmolumento='"+dado.idPTipoEmolumento
      +"'><i class='fa fa-pen'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
  function manipular(){
    chamarJanelaEspera()
      $("#formularioTipoEmolumentos").modal("hide")
     var form = new FormData(document.getElementById("formularioTipoEmolumentosForm"));
     enviarComPost(form);
     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim();
          fecharJanelaEspera()
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
              mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
              tipos_emolumentos = JSON.parse(resultado)
              fazerPesquisa();
          }
        }
      }
  }