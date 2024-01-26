
var estadoExecucao="ja";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaSecretaria/novaMatriculaInscricao/";

    selectProvincias("#formularioMatricula #pais", "#formularioMatricula #provincia", "#formularioMatricula #municipio",
     "#formularioMatricula #comuna")

    //Removendo os dados nas outras caixas....
    $("#formularioMatricula #sexoAluno").attr("disabled", "").removeAttr("name")
    $("#formularioMatricula #classeAlunoForm").attr("disabled", "").removeAttr("name")
    $("#formularioMatricula #idPCursoForm").attr("disabled", "").removeAttr("name")
    $("#formularioMatricula #acessoConta").attr("disabled", "").removeAttr("name");
    $("#formularioMatricula #periodoAluno").attr("disabled", "").removeAttr("name");
    $("#curso").val(idCurso)

    fazerPesquisa();
    DataTables("#example1", "sim")
    
    $("#curso").change(function(){
      window.location ='?idCurso='+$(this).val();
    })


    $("#formularioMatricula #nomeAluno").change(function(){
      porValoresDoAluno();
    })
    $("#formularioMatricula #tipoDocumento").change(function(){
      paraDocumentos()
    })

    $("#btnNovaMatriculaInscricao").click(function(){
      limparFormulario("#formularioMatricula");
      action = "salvarMatriculaInsc"
      $("#formularioMatricula input[name=action]").val(action)
      pegarAlunosQueAindaNaoFizeramMatricula();
    })

    $("#formularioMatricula form").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioMatricula form")==true){
          estadoExecucao="espera";
          manipular();
      }
        return false;
    })
    
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      var repet=true
      $("#tabela tr .alteracao a").click(function(){
        if(repet==true){
          idPrincipal = $(this).attr("idPrincipal")
          action = $(this).attr("action")
          $("#formularioMatricula input[name=action]").val(action)
          $("#formularioMatricula #idPEntidade").val(idPrincipal)
          porValoresNoFormulario();
          repet=false;
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

  $("#numTMasculinos").text(0);
  $("#numTAlunos").text(completarNumero(listaAlunos.length));
  var contagem=0;
  var numM=0;
  var tbody=""
  listaAlunos.forEach(function(dado){
    contagem++
    if(dado.sexoAluno=="F"){
      numM++;
    }
    tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
    +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
    +"' class='lead black'>"+dado.numeroInterno
    +"</a></td><td class='lead text-center'>"+dado.abrevCurso+" - "+
    dado.escola.classeActualAluno+"<br/>"+retornarPeriodo(dado.escola.periodoAluno)
    +"</td><td class='text-center'><a href='"+caminhoRecuar+"relatoriosPdf/reciboMatricula.php/?idPMatricula="+dado.idPMatricula
    +"' class='lead text-center'><i class='fa fa-print'></i> Visualizar</a></td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarMatriculaInsc'"+
    " idPrincipal='"+dado.idPMatricula
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirMatriculaInsc'"+
    " idPrincipal='"+dado.idPMatricula
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
  })
  $("#numTMasculinos").text(completarNumero(numM));
  $("#tabela").html(tbody)
}

function porValoresDoAluno(){
    alunosQuePassaram.forEach(function(dado){

      if(dado.idPAluno==$("#formularioMatricula #nomeAluno option:selected").attr("idPAluno")){

            $("#formularioMatricula #idPAluno").val($("#formularioMatricula #nomeAluno option:selected").attr("idPAluno"));
            
            $("#formularioMatricula #sexoAluno, #formularioMatricula #sexoAluno2").val(dado.sexoAluno);
            $("#formularioMatricula #dataNascAluno, #formularioMatricula #dataNascAluno2").val(dado.dataNascAluno);
                      
            $("#formularioMatricula #numBI").val(dado.biAluno);
            $("#formularioMatricula #dataEmissaoBI").val(dado.dataEBIAluno);
            $("#formularioMatricula #nomePai").val(dado.paiAluno);
            $("#formularioMatricula #nomeMae").val(dado.maeAluno);
            $("#formularioMatricula #numTelefone").val(dado.telefoneAluno);
            $("#formularioMatricula #classeAlunoForm, #formularioMatricula #classeAlunoForm2").val(10);    
            $("#formularioMatricula #idPCursoForm, #formularioMatricula #idPCursoForm2").val(dado.inscricao.idInscricaoCurso); 
            
            $("#formularioMatricula #emailAluno").val(dado.emailAluno)
            $("#formularioMatricula #acessoConta, #formularioMatricula #acessoConta2").val("I")
            $("#formularioMatricula #periodoAluno, #formularioMatricula #periodoAluno2").val(dado.inscricao.periodoApuramento)
           
            paraTurno();
            seleccionarTipoDeDeficiencia("");
            $("#tipoDeficiencia").val("");

            passarValoresDaProvincia("#formularioMatricula #pais", "#formularioMatricula #provincia", 
          "#formularioMatricula #municipio", "#formularioMatricula #comuna"
          , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno,
          dado.comunaNascAluno);
      }
  });
}

function porValoresNoFormulario(){
  listaAlunos.forEach(function(dado){
    if(dado.idPMatricula==idPrincipal){
      $("#formularioMatricula #sexoAluno, #formularioMatricula #sexoAluno2").val(dado.sexoAluno);
      $("#formularioMatricula #dataNascAluno, #formularioMatricula #dataNascAluno2").val(dado.dataNascAluno);

      $("#formularioMatricula #nomeAluno").html("<option value='"+dado.nomeAluno+"'>"
      +dado.nomeAluno+"</option>");
      
      $("#formularioMatricula #numBI").val(dado.biAluno);
      $("#formularioMatricula #dataEmissaoBI").val(dado.dataEBIAluno);
      $("#formularioMatricula #nomePai").val(dado.paiAluno);
      $("#formularioMatricula #nomeMae").val(dado.maeAluno);
      $("#formularioMatricula #nomeEncarregado").val(dado.encarregadoEducacao);
      $("#formularioMatricula #numTelefone").val(dado.telefoneAluno);
      listarClasses(dado.escola.idMatCurso, dado.escola.classeActualAluno, "#formularioMatricula #idPCursoForm", "#formularioMatricula #classeAlunoForm")
      listarClasses(dado.escola.idMatCurso, dado.escola.classeActualAluno, "#formularioMatricula #idPCursoForm2", "#formularioMatricula #classeAlunoForm2")
      $("#formularioMatricula #dataCaducidadeBI").val(dado.dataCaducidadeBI) 
      
      $("#formularioMatricula #emailAluno").val(dado.emailAluno)
      $("#formularioMatricula #acessoConta, #formularioMatricula #acessoConta2").val(dado.estadoAcessoAluno)
      $("#formularioMatricula #municipio").val(dado.municNascAluno);
      $("#formularioMatricula #provincia").val(dado.provNascAluno);
      $("#formularioMatricula #pais").val(dado.paisNascAluno);

      idGestLinguaEspecialidade = dado.escola.idGestLinguaEspecialidade
      if(idGestLinguaEspecialidade==22){
        idGestLinguaEspecialidade=20;
      }else if(idGestLinguaEspecialidade==23){
        idGestLinguaEspecialidade=21;
      }

      $("#formularioMatricula #lingEspecialidade").val(idGestLinguaEspecialidade)
      $("#formularioMatricula #discEspecialidade").val(dado.escola.idGestDisEspecialidade)
      $("#formularioMatricula #periodoAluno, #formularioMatricula #periodoAluno2").val(dado.escola.periodoAluno)
      paraTurno(dado.escola.turnoAluno)
      $("#formularioMatricula #numeroProcesso").val(dado.escola.numeroProcesso)
      $("#formularioMatricula #rpm").val(dado.escola.rpm)
      $("#formularioMatricula input[name=idPMatricula]").val(dado.idPMatricula)
      $("#deficiencia").val(dado.deficienciaAluno);
      seleccionarTipoDeDeficiencia(dado.deficienciaAluno);
      $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno)

      passarValoresDaProvincia("#formularioMatricula #pais", "#formularioMatricula #provincia", 
        "#formularioMatricula #municipio", "#formularioMatricula #comuna","provincia", 
        "municipio", "comuna", dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno);

      $("#formularioMatricula #tipoDocumento").val(dado.tipoDocumento)
      $("#formularioMatricula #localEmissao").val(dado.localEmissao)
      paraDocumentos()
      paraTurno("")

      if(action=="editarMatriculaInsc"){
        $("#formularioMatricula").modal("show");
      }else{
        mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta matricula?");
      } 
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
        }else{
          estadoExecucao="ja";
          fecharJanelaEspera();
          if(action=="salvarMatriculaInsc" || action=="editarMatriculaIns"){
            $("#formularioMatricula").modal("show")
          }          
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }
      }
    }
}
function actualizarLista(mensagem){
  enviarComGet("tipoAcesso=listarMatriculadosInsc&idPCurso="+idCurso);
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      estadoExecucao="ja";
      fecharJanelaEspera();
      mensagensRespostas("#mensagemCerta", mensagem);
      listaAlunos = JSON.parse(resultado)
      fazerPesquisa();      
    }
  }
}

function pegarAlunosQueAindaNaoFizeramMatricula(){
  chamarJanelaEspera("")
  enviarComGet("tipoAcesso=pegarAlunosQueAindaNaoFizeramMatricula&idPCurso="+idCurso);
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera()
      resultado = http.responseText.trim()
      $("#formularioMatricula #nomeAluno").empty();
      var html ="<option value=''>Seleccionar Aluno</option>";
      JSON.parse(resultado).forEach(function(dado){
        html +="<option value='"+dado.nomeAluno+"' idPAluno='"+dado.idPAluno+"'>"
        +dado.nomeAluno+" ("+dado.codigoAluno+")</option>";
      })
      $("#formularioMatricula #nomeAluno").html(html) 
      $("#formularioMatricula").modal("show")   
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



