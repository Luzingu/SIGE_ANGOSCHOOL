var listaDadosConjuntoAnosLectivos=new Array()
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/camposAvaliacoes00/";
    $("#idPEscola").val(idPEscola)
    $("#idPEscola").change(function(){
      window.location='?idPEscola='+$(this).val()
    })
    fazerPesquisa();

    $("#novaAvaliacao").click(function(){
      $("#markOuDesmarcar").prop("checked")
      $("#formularioAvaliacoesForm #action").val("novaAvaliacao")
      $("#formularioAvaliacoesForm .vazio").val("")
      $("#formularioMatricula").modal("show")
    })

    $("#formularioAvaliacoesForm").submit(function(){
      if(estadoExecucao=="ja"){
        manipular();
      } 
      return false;
    });
    
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idCampoAvaliacao = $(this).attr("idCampoAvaliacao")
          $("#formularioAvaliacoesForm #action").val($(this).attr("action"))
          campos_avaliacao.forEach(function(dado){
            if(dado.idCampoAvaliacao==idCampoAvaliacao){
              $("#formularioAvaliacoesForm #idCampoAvaliacao").val(idCampoAvaliacao)
              $("#formularioAvaliacoesForm #identUnicaDb").val(dado.identUnicaDb)
              $("#formularioAvaliacoesForm #designacao1").val(dado.designacao1)
              $("#formularioAvaliacoesForm #designacao2").val(dado.designacao2)
              $("#formularioAvaliacoesForm #ordenacao").val(dado.ordenacao)
              $("#formularioAvaliacoesForm #tipoCampo").val(dado.tipoCampo)
              $("#formularioAvaliacoesForm #notaMaxima").val(dado.notaMaxima)
              $("#formularioAvaliacoesForm #notaMedia").val(dado.notaMedia)
              $("#formularioAvaliacoesForm #notaMinima").val(dado.notaMinima)
              $("#formularioAvaliacoesForm #numeroCasasDecimais").val(dado.numeroCasasDecimais)
              
              if(dado.seApenasLeitura=="V"){
                $("#formularioAvaliacoesForm #seApenasLeitura").prop("checked", true)  
              }else{
                $("#formularioAvaliacoesForm #seApenasLeitura").prop("checked", false)
              }
            }
          })
          if($(this).attr("action")=="editarAvaliacao"){
              $("#formularioMatricula").modal("show")  
          }else{
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta avaliação?");
          }          
          repet=true
        }
      })
    })

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
      var i=0;
    campos_avaliacao.forEach(function(dado){
      i++;
      tbody +="<tr><td class='lead text-center'>"+dado.ordenacao
      +"</td><td class='lead text-center'>"+dado.identUnicaDb 
      +"</td><td class='lead text-center'>"+dado.designacao1
      +"</td><td class='lead'>"+dado.tipoCampo
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarAvaliacao'"+
      " idCampoAvaliacao='"+dado.idCampoAvaliacao
      +"'><i class='fa fa-pen'></i></a>&nbsp;&nbsp;<a class='btn btn-danger' title='Excluir' href='#as' action='excluirAvaliacao'"+
      " idCampoAvaliacao='"+dado.idCampoAvaliacao
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
  function manipular(){
    chamarJanelaEspera()
    $("#formularioMatricula").modal("hide")
     var form = new FormData(document.getElementById("formularioAvaliacoesForm"));
     enviarComPost(form);
     http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        fecharJanelaEspera()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            campos_avaliacao = JSON.parse(resultado)
            fazerPesquisa();
        }
      }
    }
  }