var dataSeleccao="";

window.onload=function(){
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaSecretaria/novaMatricula/";
    $("#datasMatricula").val(dataMatricula);
    
    selectProvincias("#formularioMatricula #pais", "#formularioMatricula #provincia", "#formularioMatricula #municipio",
     "#formularioMatricula #comuna")
    paraTurno()
    listarClasses("", "", "#formularioMatricula #idPCursoForm", "#formularioMatricula #classeAlunoForm")
    fazerPesquisa();
    DataTables("#example1", "sim")
    
    $("#periodoAluno").change(function(){
      paraTurno();
    })

    $("#formularioMatricula #tipoDocumento").change(function(){
      paraDocumentos()
    })

    $("#datasMatricula").change(function(){
      window.location ="?dataMatricula="+$(this).val();
    }) 

    $(".novoRegistroFormulario").click(function(){
          $("#formularioMatricula #action").val("salvarMatricula")
          limparFormulario("#formularioMatricula")

          $("#formularioMatricula #deficiencia").val("")
          seleccionarTipoDeDeficiencia("")
          $("#formularioMatricula").modal("show")
    })

    var repet=true
    $("#tabela").bind("click mouseenter", function(){
        repet=true
        $("#tabela tr td a.alterar").click(function(){
            if(repet==true){
              $("#formularioMatricula #action").val($(this).attr("action"))
              $("#formularioMatricula #idPMatricula").val($(this).attr("idPrincipal"))
              if($(this).attr("action")=="editarMatricula"){
                  
                  porValoresNoFormulario()
                  $("#formularioMatricula").modal("show")
              }else{
                mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar esta matricula?")
              }
              repet=false
            }
        })
    })

    $("#formularioMatricula form").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioMatricula form")==true){
          estadoExecucao="espera";
          manipular();
      }
        return false;
    })

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              estadoExecucao="espera";
              fecharJanelaToastPergunta()
              manipular();
            }
            rep=false;
          }         
      })
    })    
}

function fazerPesquisa(){

  $("#numTAlunos").text(0);
  $("#numTMasculinos").text(0);

  $("#numTAlunos").text(completarNumero(listaAlunos.length));
  $("#numTMasculinos").text(0);
  var tbody = "";
  var numM=0;
  var contagem=0;
  listaAlunos.forEach(function(dado){
    contagem++;
    if(dado.sexoAluno=="F"){
        numM++;
    }
    $("#numTMasculinos").text(completarNumero(numM));

    tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
    +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
    +"' class='lead black'>"+dado.numeroInterno
    +"</a></td><td class='lead text-center'>"+dado.abrevCurso+" - "+dado.escola.classeActualAluno+"</td><td class='lead'>"
    +retornarPeriodo(dado.escola.periodoAluno)+"</td><td class='text-center'><a class='btn btn-primary' href='"+caminhoRecuar+"relatoriosPdf/reciboMatricula.php?idPMatricula="+dado.idPMatricula+"&idPAno="+dado.reconfirmacoes.idReconfAno+"' class='lead text-center'>"+
          "<i class='fa fa-print'></i></a>&nbsp;&nbsp;&nbsp;<a class='btn btn-success' href='"+caminhoRecuar+"relatoriosPdf/reciboMatricula2.php?idPMatricula="+dado.idPMatricula+"&idPAno="+dado.reconfirmacoes.idReconfAno+"' class='lead text-center'>"+
          "<i class='fa fa-print'></i></a></td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success alterar' title='Editar' href='#as'"+
    " action='editarMatricula' idPrincipal='"+dado.idPMatricula
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger alterar' "+
    "title='Excluir' href='#a' action='excluirMatricula' idPrincipal='"+dado.idPMatricula
    +"'><i class='fa fa-times'></i></a></div></td></tr>";           
  });
  $("#tabela").html(tbody);
}

function porValoresNoFormulario(){
 listaAlunos.forEach(function(dado){
    if(dado.idPMatricula==$("#formularioMatricula #idPMatricula").val()){

      $("#formularioMatricula #nomeAluno").val(dado.nomeAluno);
      $("#formularioMatricula #sexoAluno").val(dado.sexoAluno);
      $("#formularioMatricula #dataNascAluno").val(dado.dataNascAluno);
      
      $("#formularioMatricula #numBI").val(dado.biAluno);
      $("#formularioMatricula #dataEmissaoBI").val(dado.dataEBIAluno);
      $("#formularioMatricula #nomePai").val(dado.paiAluno);
      $("#formularioMatricula #nomeMae").val(dado.maeAluno);
      $("#formularioMatricula #nomeEncarregado").val(dado.encarregadoEducacao);
      $("#formularioMatricula #numTelefone").val(dado.telefoneAluno); 

      $("#formularioMatricula #emailAluno").val(dado.emailAluno)
      $("#formularioMatricula #dataCaducidadeBI").val(dado.dataCaducidadeBI)
      $("#formularioMatricula #estadoDeDesistenciaNaEscola").val(dado.escola.estadoDeDesistenciaNaEscola)
      
      $("#formularioMatricula #acessoConta").val(dado.estadoAcessoAluno)
      $("#formularioMatricula #idMatAnexo").val(dado.escola.idMatAnexo)
      $("#formularioMatricula #rpm").val(dado.escola.rpm)
      $("#formularioMatricula #tipoEntrada").val(dado.reconfirmacoes.tipoEntrada)
      idGestLinguaEspecialidade = dado.escola.idGestLinguaEspecialidade
      if(idGestLinguaEspecialidade==22){
        idGestLinguaEspecialidade=20;
      }else if(idGestLinguaEspecialidade==23){
        idGestLinguaEspecialidade=21;
      }
      $("#formularioMatricula #lingEspecialidade").val(idGestLinguaEspecialidade)
      $("#formularioMatricula #discEspecialidade").val(dado.escola.idGestDisEspecialidade)
      $("#formularioMatricula #periodoAluno").val(dado.escola.periodoAluno)
      paraTurno(dado.escola.turnoAluno)

      $("#formularioMatricula #tipoDocumento").val(dado.tipoDocumento)
      $("#formularioMatricula #localEmissao").val(dado.localEmissao)
      paraDocumentos()
      $("#formularioMatricula #numeroProcesso").val(dado.escola.numeroProcesso)

      $("#deficiencia").val(dado.deficienciaAluno);
      seleccionarTipoDeDeficiencia(dado.deficienciaAluno);
      $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno)

      listarClasses(dado.escola.idMatCurso, dado.escola.classeActualAluno, "#formularioMatricula #idPCursoForm", "#formularioMatricula #classeAlunoForm")

      passarValoresDaProvincia("#formularioMatricula #pais", "#formularioMatricula #provincia", 
    "#formularioMatricula #municipio", "#formularioMatricula #comuna"
      , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno);
    }
  });
}

function manipular(){
  var form = new FormData(document.getElementById("formularioMatriculaF"));
    $("#formularioMatricula").modal("hide") 
  chamarJanelaEspera("")
   enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        if(resultado.trim().substring(0, 1)=="V"){
            actualizarLista(resultado.substring(1, resultado.length));
        }else if(resultado.trim().substring(0, 1)=="F"){
          estadoExecucao="ja"
          fecharJanelaEspera()
          if($("#formularioMatricula #action").val()=="salvarMatricula" || $("#formularioMatricula #action").val()=="editarMatricula"){
            $("#formularioMatricula").modal("show")
          }       
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }
      }
    }
}
function actualizarLista(mensagem){
  enviarComGet("tipoAcesso=listarMatriculados&dataMatricula="+dataMatricula+"&todos=nao");
  http.onreadystatechange = function(){
    if(http.readyState==4){
      listaAlunos = new Array();
      resultado = http.responseText.trim();
      if(resultado.trim()!=""){
        listaAlunos = JSON.parse(resultado);
      }
      if(listaAlunos.length<=0){
         window.location ="";
      }
      fecharJanelaEspera();
      estadoExecucao="ja";
      mensagensRespostas("#mensagemCerta", mensagem );
      fazerPesquisa();
    }
  }
}

function paraTurno(valorTurno=""){
  $("#turnoAluno").empty();
  if(criterioEscolhaTurno=="opcional"){
    if($("#periodoAluno").val()=="reg"){
      $("#turnoAluno").append("<option>Matinal</option>");
      $("#turnoAluno").append("<option>Vespertino</option>");
    }else{
      $("#turnoAluno").append("<option>Noturno</option>");
    }
  }else{
    $("#turnoAluno").append("<option>Automático</option>");
  }
  if(valorTurno!=""){
    $("#turnoAluno").val(valorTurno)
  }
}

function paraDocumentos(){
  if($("#formularioMatricula #tipoDocumento").val()=="BI"){
      $("#formularioMatricula #localEmissao").attr("disabled", "")
      $("#formularioMatricula #localEmissao").val("Luanda");
  }else{
    $("#formularioMatricula #localEmissao").removeAttr("disabled")
  }
}

