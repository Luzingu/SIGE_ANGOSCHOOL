var dataSeleccao="";
var idPCursoFormulario =0;
var idMatAno=0;
var estadoExecucao="ja";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaSecretaria/backupNovoRegistro/";
    idPCursoFormulario = $("#formularioMatricula #idPCursoForm").val();

    selectProvincias("#formularioMatricula #pais", "#formularioMatricula #provincia",
      "#formularioMatricula #municipio","#formularioMatricula #comuna")
    accoes("#tabela", "#formularioMatricula", "Registro", "Tens certeza que pretendes eliminar este registro?");
    paraTurno();
    fazerPesquisa();

    $("#periodoAluno").change(function(){
      paraTurno();
    })
    $("#formularioMatricula #tipoDocumento").change(function(){
      paraDocumentos()
    })

    $("#formularioMatricula form").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioMatricula form")==true){
          idEspera = "#formularioMatricula form #Cadastar";
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
              idEspera = "#janelaPergunta #pergSim";
              estadoExecucao="espera";
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

  $("#numTAlunos").text(completarNumero(listaAlunos.filter(fazerPesquisaCondition).length));
        $("#numTMasculinos").text(0);

    var tbody = "";
      if(jaTemPaginacao==false){
        paginacao.baraPaginacao(listaAlunos.filter(fazerPesquisaCondition).length, 100);
      }else{
          jaTemPaginacao=false;
      }

      var contagem=-1;
        var numM=0;
    listaAlunos.filter(fazerPesquisaCondition).forEach(function(dado){
        contagem++;
        if(dado.sexoAluno=="F"){
            numM++;
        }

        $("#numTMasculinos").text(completarNumero(numM));
        if(contagem>=paginacao.comeco && contagem<=paginacao.final){
            
            if(dado.escola.classeActualAluno==90){
              cursoAluno="TÉC. BÁSICO"
            }else if(dado.escola.classeActualAluno==60){
              cursoAluno="TÉC. PRIM";
            }else{
              cursoAluno = vazioNull(dado.abrevCurso)
            }
            tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"
            +completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
            +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
            +"' class='lead black'>"+dado.numeroInterno
            +"</a></td><td class='lead'>"+cursoAluno
            +"</td><td class='lead'>"
            +retornarPeriodo(dado.escola.periodoAluno)+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarRegistro'"+
            " idPrincipal='"+dado.idPMatricula
            +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirRegistro' idPrincipal='"+dado.idPMatricula
            +"'><i class='fa fa-times'></i></a></div></td></tr>";
        }
           
    });
    $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
 listaAlunos.forEach(function(dado){
      if(dado.idPMatricula==idPrincipal){

        $("#formularioMatricula #nomeAluno").val(dado.nomeAluno);
        $("#formularioMatricula #sexoAluno").val(dado.sexoAluno);
        $("#formularioMatricula #dataNascAluno").val(dado.dataNascAluno);
        
        $("#formularioMatricula #dataEmissaoBI").val(dado.dataEBIAluno);
        $("#formularioMatricula #nomePai").val(dado.paiAluno);
        $("#formularioMatricula #nomeMae").val(dado.maeAluno);
        $("#formularioMatricula #nomeEncarregado").val(dado.encarregadoEducacao);
        $("#formularioMatricula #numTelefone").val(dado.telefoneAluno)
        
        $("#formularioMatricula #idPCursoForm").val(dado.escola.idMatCurso)
        $("#formularioMatricula #classeAlunoForm").val("F_"+dado.escola.idMatFAno)

        $("#formularioMatricula #emailAluno").val(dado.emailAluno)
        $("#formularioMatricula #acessoConta").val(dado.estadoAcessoAluno)

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
        $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno);
        $("#formularioMatricula #idMatAnexo").val(dado.escola.idMatAnexo);

        passarValoresDaProvincia("#formularioMatricula #pais", "#formularioMatricula #provincia", 
          "#formularioMatricula #municipio", "#formularioMatricula #comuna"
          , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno);
      }
  });
}

function manipular(){
    $("#formularioMatricula").modal("hide")
    chamarJanelaEspera("")
    var form = new FormData(document.getElementById("formularioMatriculaF"));
    enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        if(resultado.trim().substring(0, 1)=="V"){
            actualizarLista(resultado.substring(1, resultado.length));
        }else{
          $("#formularioMatricula").modal("show")
          fecharJanelaEspera();
          estadoExecucao="ja";
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }
      }
    }
}
function actualizarLista(mensagem){

  enviarComGet("tipoAcesso=listarRegistrados");  
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim();
      listaAlunos = JSON.parse(resultado)
      fecharJanelaEspera()
      mensagensRespostas("#mensagemCerta", mensagem);
      estadoExecucao="ja";
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