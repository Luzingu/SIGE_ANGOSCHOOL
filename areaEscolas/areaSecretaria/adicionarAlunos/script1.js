var idPMatricula="";
var action="";
var mensagemEspera ="";
var idMatEscola="";
$(document).ready(function(){
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaSecretaria/adicionarAlunos/";

    selectProvincias("#formularioMatricula #pais", "#formularioMatricula #provincia", "#formularioMatricula #municipio",
     "#formularioMatricula #comuna")
    
    paraTurno();
    $("#periodoAluno").change(function(){
      paraTurno();
    })
    $("#pesqAluno").keyup(function(){
      pesquisarAluno();
    }); 

    $("#tabela2").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#dataReconfirmacao").change(function(){
      listarReconfirmados();
    })

    $("#luzingu").change(function(){
        window.location ="?luzingu="+$("#luzingu").val();
    })
    $("#formularioMatricula #tipoDocumento").change(function(){
    paraDocumentos()
  })

    var repet1 = true;

    $("#dadoTabela").bind("mouseenter click", function(){
        repet1=true;
        $("#dadoTabela a.alteracao").click(function(){
          if(repet1==true){
            idPMatricula = $(this).attr("idPMatricula");
            idMatEscola = $(this).attr("idMatEscola");
            $("#formularioMatricula #action").val("adicionarAluno")
            $("#formularioMatricula #idPMatricula").val(idPMatricula)
            porValoresNoFormulario()
            $("#formularioMatricula").modal("show");
            repet1=false;
          }
        });
    });

    $("#formularioMatriculaF").submit(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="aindaNao";
        adicionarAluno();
      }
      return false;
    }) 

})
 
    function adicionarAluno(){
      chamarJanelaEspera("");
        $("#formularioMatricula").modal("hide");      
        http.onreadystatechange = function(){
          if(http.readyState==4){
            fecharJanelaEspera();
            resultado = http.responseText.trim()
            estadoExecucao="ja";
            if(resultado.substring(0,1)=="V") {
              $("#dadoTabela").html("")
              mensagensRespostas('#mensagemCerta', resultado.substring(1,resultado.length));
            }else{
              $("#formularioMatricula").modal("show")
              mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));          
            }
          }
        }
        var form = new FormData(document.getElementById("formularioMatriculaF"));
         enviarComPost(form);
    }

    function pesquisarAluno(){
      $("#dadoTabela").html("<tr><td colspan='6' class='text-center'>Pesquisando..."+
        "</td></tr>")
      http.onreadystatechange = function(){
        if(http.readyState==4){
            resultado = http.responseText.trim()
            
            var htmlAdicionar="";

            $("#dadoTabela").html("")
            alunosEncontrados = JSON.parse(resultado)
            JSON.parse(resultado).forEach(function(dado){
              varPlamedi="";
              if(dado.escola!=undefined && dado.escola!=null){
                dado.escola.forEach(function(school){
                  if(varPlamedi!=""){
                    varPlamedi +="<br>"
                  }
                  var classe = school.classeActualAluno
                  if(classe==60){
                    classe="TEC_PRIM";
                  }else if(classe==90){
                    classe="TEC_BÁSICO";
                  }else if(classe==120){
                    classe="TEC_MÉDIO";
                  }else{
                    classe +=".ª"
                  }
                  varPlamedi +=retornarNomeEscola(school.idMatEscola)
                  +" ("+retornarNomeCursos(school.idMatCurso)+" - "+classe+")"
                })
              }

              htmlAdicionar +="<tr><td class='lead text-center'><img src='../../../fotoUsuarios/"+dado.fotoAluno+"' class='fotoAluno'>"
              +"</td><td class='lead'>"+dado.nomeAluno
              +"</td><td class='text-center'>"
              +dado.numeroInterno+"<strong>"
              +"</strong></td><td class='text-center'>"
              +dado.sexoAluno+"</td><td class=''>"
              +vazioNull(dado.biAluno)+"</td><td class=''>"
              +varPlamedi+"</td><td class='text-center'><a href='#' title='Adicionar na Escola'"+
              " class='text-success alteracao' idPMatricula='"+dado.idPMatricula
              +"'>"+
              "<i class='fa fa-plus-circle fa-2x'></i></a></td></tr>";
            })
            $("#dadoTabela").html(htmlAdicionar)
        }     
      }
      enviarComGet("tipoAcesso=pesquisarAluno&valorPesq="+$("#pesqAluno").val());
    }

    function retornarNomeEscola(idPEscola){
      var retorno=""
      listaEscolas.forEach(function(dado){
        if(dado.idPEscola==idPEscola){
          retorno=dado.abrevNomeEscola2
        }
      })
      return retorno
    }

    function retornarNomeCursos(idPNomeCurso){
      var retorno=""
      listaNomeCursos.forEach(function(dado){
        if(dado.idPNomeCurso==idPNomeCurso){
          retorno=dado.abrevCurso
        }
      })
      return retorno
    }

    function porValoresNoFormulario(){
     alunosEncontrados.forEach(function(dado){
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
          $("#formularioMatricula #dataCaducidadeBI").val(dado.dataCaducidadeBI)

          $("#formularioMatricula #lingEspecialidade").val("")
          $("#formularioMatricula #discEspecialidade").val("")
          $("#formularioMatricula #periodoAluno").val("")
          paraTurno(dado.turnoAluno)
          $("#formularioMatricula #tipoDocumento").val(dado.tipoDocumento)
          $("#formularioMatricula #localEmissao").val(dado.localEmissao)
          paraDocumentos()
          
          $("#formularioMatricula #numeroProcesso").val("")

          $("#deficiencia").val(dado.deficienciaAluno);
          seleccionarTipoDeDeficiencia(dado.deficienciaAluno);
          $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno);

          listarClasses("", "",
        "#formularioMatricula #idPCursoForm", "#formularioMatricula #classeAlunoForm")


          $("#formularioMatricula #classeAlunoForm").val("")

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


    