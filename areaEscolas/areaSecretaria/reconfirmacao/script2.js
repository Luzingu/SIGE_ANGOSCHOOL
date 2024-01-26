var tabelaAListar="#aindaNaoReconfirmados";
var classeEnviar="";
var idAnoMatriculadoAluno =0;

var idPMatricula="";
var action="";
var mensagemEspera ="";

$(document).ready(function(){
  fecharJanelaEspera();
  seAbrirMenu();
  entidade ="alunos";
  directorio = "areaSecretaria/reconfirmacao/";
  $("#luzingu").val(luzingu);

  selectProvincias("#formularioMatricula #pais", "#formularioMatricula #provincia", "#formularioMatricula #municipio",
  "#formularioMatricula #comuna")

  paraTurno(); 
  listarNaoReconfirmados();
  listarReconfirmados();       
  
  $("#pesqAluno").keyup(function(){
    listarNaoReconfirmados();
  })
  $("#tabela2").DataTable({
    "responsive": true, "lengthChange": true, "autoWidth": false,
    "buttons": ["copy", "excel", "pdf", "print"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  $("#luzingu").change(function(){
      window.location ="?luzingu="+$("#luzingu").val();
  })
  $("#formularioMatricula #tipoDocumento").change(function(){
    paraDocumentos()
  })

  $("#periodoAluno").change(function(){
    paraTurno();
  })

  var repet1 = true;
  $("table").bind("mouseenter click", function(){
      repet1=true;
      $("table .alteracao").click(function(){
          if(repet1==true){
            idPMatricula = $(this).attr("idPMatricula");
            action = $(this).attr("action");
            idAnoMatriculadoAluno = $(this).attr("idAnoMatriculadoAluno");

            $("#formularioMatricula #action").val(action)
            $("#formularioMatricula #idPMatricula").val(idPMatricula)
            $("#formularioMatricula #idAnoMatriculadoAluno").val(idAnoMatriculadoAluno)
            
            if(action=="anularReconfirmacao"){
                mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular a reconfirmação deste(a) aluno(a)?");
            }else if(action=="suspenderMatricula"){
                mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes suspender a matricula do aluno?");
            }else{
              porValoresNoFormulario();
              $("#formularioMatricula").modal("show")
            }
            repet1=false;
          }
      });
  });

  var rep=true;
  $("body").bind("mouseenter click", function(){
        rep=true;
      $("#janelaPergunta #pergSim").click(function(){
        if(rep==true){
          if(estadoExecucao=="ja"){
             estadoExecucao="aindaNao";
             fecharJanelaToastPergunta();
              manipularReconfirmacao();
          }
          rep=false;
        }         
    })
  })

  $("#formularioMatriculaF").submit(function(){
    if(estadoExecucao=="ja"){
      estadoExecucao="aindaNao";
      manipularReconfirmacao();
    }
    return false;
  }) 

})

  function listarNaoReconfirmados(){
    var html ="";
    var i=0;
    var masculino=0;
    $(".numTMasculinos").text(completarNumero(masculino));
    var contagem=-1;
    if(jaTemPaginacao==false){
      paginacao.baraPaginacao(alunosNaoReconfirmados.filter(conditionNaoReconfirmado).length, 100);
    }else{
      jaTemPaginacao=false;
    }                
    $(".numTAlunos").text(completarNumero(alunosNaoReconfirmados.filter(conditionNaoReconfirmado).length));
    
    contagem=0;
   alunosNaoReconfirmados.filter(conditionNaoReconfirmado).forEach(function(dado){
     contagem++;
     
      if(dado.sexoAluno=="F"){
        masculino++;
      }
      var desabilitacao ="title='Reconfirmar.' ";
      if(dado.escola.estadoDeDesistenciaNaEscola!="A"){
        desabilitacao ="disabled title='Este(a) aluno(a) já desistiu.' ";
      }
      $(".numTMasculinos").text(completarNumero(masculino));

      html += "<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"
      +dado.fotoAluno+"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
      +"' class='lead black'>"+dado.numeroInterno+"</a></td><td class='lead text-center'>"+dado.sexoAluno
      +"</td><td class='lead text-center'>"+calcularIdade(dado.dataNascAluno)+" Anos</td><td class='text-center'>";

      html +="<button "+desabilitacao+" class='text-primary alteracao' action='reconfirmar' idPMatricula='"
        +dado.idPMatricula+"' classe='"+dado.escola.classeActualAluno+"' href='#' idAnoMatriculadoAluno='"+dado.idMatAno
        +"'><i class='fa fa-sign-out-alt fa-2x'></i></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='text-danger alteracao' href='#' action='suspenderMatricula' idPMatricula='"
      +dado.idPMatricula+"' classe='"+dado.escola.classeActualAluno+"' title='Suspender' idAnoMatriculadoAluno='"+dado.idMatAno
      +"'><i class='fa fa-minus-circle fa-2x'></i></a></td></tr>";
                
   });
   $("#tabelaNaoReconfirmados").html(html);
  }
  function listarReconfirmados(){
    var html ="";
  
    $(".numTotal").text(completarNumero(alunosReconfirmados.length));
    contagem=0;
    masculino=0;
    alunosReconfirmados.forEach(function(dado){
       contagem++;

        if(dado.sexoAluno=="F"){
          masculino++;
        }
        $(".numTMasculinos").text(completarNumero(masculino)); 

        html += "<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
        +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno+"</a></td><td class='lead text-center'>"
        +dado.abrevCurso+" - "+dado.reconfirmacoes.classeReconfirmacao+"</td><td class='text-center lead'>"+dado.reconfirmacoes.horaReconf
        +"</td><td class='text-center'><a href='"+caminhoRecuar+"relatoriosPdf/reciboMatricula.php?idPMatricula="+dado.idPMatricula+"&idPAno="
        +dado.idReconfAno+"' class='lead text-center'><i class='fa fa-print fa-2x'></i></a></td><td class='text-center'><a class='btn text-danger alteracao' title='Anular' action='anularReconfirmacao' idPMatricula='"
        +dado.idPMatricula+"' classe='"+dado.escola.classeActualAluno
        +"' idAnoMatriculadoAluno='"+dado.escola.idMatAno+"'><i class='fa fa-user-times'></i></a></td></tr>";
             
     });
     $("#tabJaReconfirmados").html(html)
  }

  function conditionNaoReconfirmado(elem, ind, obj){
    return (elem.nomeAluno.toLowerCase().indexOf($("#pesqAluno").val().toLowerCase())>=0 );
  }

  function manipularReconfirmacao(){
    chamarJanelaEspera("");
      $("#formularioMatricula").modal("hide");      
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          estadoExecucao="ja";
          if(resultado.substring(0,1)=="V") {
            pegarAlunosNaoReconfirmados(resultado.substring(1,resultado.length));                          
          }else{
            estadoExecucao="ja";
            if(action=="reconfirmar"){
              $("#formularioMatricula").modal("show")
            }
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }
        }
      }
      var form = new FormData(document.getElementById("formularioMatriculaF"));
       enviarComPost(form);
  }

  function pegarAlunosNaoReconfirmados(mensagem){
    fecharJanelaEspera()
    chamarJanelaEspera("...")
    http.onreadystatechange = function(){
      if(http.readyState==4){
          estadoExecucao="ja";
          fecharJanelaEspera();
          var resultado = http.responseText.trim()
          alunosNaoReconfirmados = JSON.parse(resultado);
          pegarAlunosReconfirmados(mensagem)
      }     
    }
    enviarComGet("tipoAcesso=pegarAlunosNaoReconfirmados&classe="+classeP+"&idPCurso="+idCursoP);
  }

  function pegarAlunosReconfirmados(mensagem){
    fecharJanelaEspera();
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){
          estadoExecucao="ja";
          fecharJanelaEspera();
          var resultado = http.responseText.trim()
          alunosReconfirmados = JSON.parse(resultado)
          listarReconfirmados();
          listarNaoReconfirmados(); 
          mensagensRespostas('#mensagemCerta', mensagem);

      }     
    }
    enviarComGet("tipoAcesso=pegarAlunosReconfirmados&classe="+classeP+"&idPCurso="+idCursoP);
  }



  function porValoresNoFormulario(){
   alunosNaoReconfirmados.forEach(function(dado){
        if(dado.idPMatricula==idPMatricula){
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
            $("#formularioMatricula #acessoConta").val(dado.estadoAcessoAluno)
            $("#formularioMatricula #idMatAnexo").val(dado.escola.idMatAnexo);
            $("#formularioMatricula #dataCaducidadeBI").val(dado.dataCaducidadeBI);

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
            
            $("#formularioMatricula #numeroProcesso").val(dado.numeroProcesso)

            $("#deficiencia").val(dado.deficienciaAluno);
            seleccionarTipoDeDeficiencia(dado.deficienciaAluno);
            $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno); 

            listarClasses(dado.escola.idMatCurso, dado.escola.classeActualAluno,
          "#formularioMatricula #idPCursoForm", "#formularioMatricula #classeAlunoForm")

          passarValoresDaProvincia("#formularioMatricula #pais", "#formularioMatricula #provincia", 
          "#formularioMatricula #municipio", "#formularioMatricula #comuna"
          , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno);
        }
    });
  }

  function paraTurno(valorTurno=""){
    if(criterioEscolhaTurno=="opcional"){
      $("#turnoAluno").empty();
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