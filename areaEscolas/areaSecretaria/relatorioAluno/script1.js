  var tipoPesquisa ="nome";
  var valorPesquisado="";
  var documentoTratar =0;
  var tipoDeDocumento="";
  var seCertificado="";
  var valorMaximo=20

$(document).ready(function(){
  directorio = "areaSecretaria/relatorioAluno/";
    fecharJanelaEspera();
    seAbrirMenu();

    $("#idCursoMaster").val(idCursoMaster)
    $("#formularioDadosAlunos #pais").val("")
    selectProvincias("#formularioDadosAlunos #pais", "#formularioDadosAlunos #provincia", "#formularioDadosAlunos #municipio",
     "#formularioDadosAlunos #comuna", false)

      paraTurno();
      porValoresFormulario();
      $("#formularioDadosAlunos").submit(function(){
        var labelDados=new Array();

        if($("#seBolseiro").prop("checked")==true){
          $("#dadosSobreBolsa input[type=number]").each(function(){
            labelDados.push({idPTipoEmolumento:$(this).attr("idPTipoEmolumento"), 
            mes:$(this).attr("mes"), codigoEmolumento:$(this).attr("codigoEmolumento"), posicao:$(this).attr("posicao"), valorPreco:$(this).val()})
          })  
        }
        $("#beneficiosDaBolsa").val(JSON.stringify(labelDados))
        alterarDadosAlunos();
        return false;
      })

      $("#formularioDadosAlunos #seBolseiro").change(function(){
        if($(this).prop("checked")==true){
          $("#formularioDadosAlunos #dadosSobreBolsa").show(500)
        }else{
          $("#formularioDadosAlunos #dadosSobreBolsa").hide(500)
        }
      })

      $("#pesquisarAluno").submit(function(){
        window.location ="?valorPesquisado="+$("#valorPesquisado").val();
        return false;
      })
      $("#idCursoMaster").change(function(){
        window.location="?idCursoMaster="+$(this).val()+"&idPMatricula="+idPMatricula
      })

      $("#valorPesquisado").keyup(function(){
        pesquisarAluno()
      })

      $("#periodoAluno").change(function(){
        paraTurno();
      })
      $("#formularioDadosAlunos #tipoDocumento").change(function(){
        paraDocumentos()
      })

      $("#documentos #listaDocumentos a.declaracao").click(function(){
        documentoTratar = $(this).attr("documento");
        tipoDeDocumento = "declaracao";
        seCertificado = $(this).attr("certificado");
        if(idPMatricula=="" || idPMatricula==null){
          mensagensRespostas2("#mensagemErrada", "Deves pesquisar um(a) aluno(a).");
        }else{
          verificarSeEPossivelTratarODocumento();
        }
      });

      $("#boletimDocumento a.boletim").click(function(){
        if(idPMatricula=="" || idPMatricula==null){ 
          mensagensRespostas2("#mensagemErrada", "Deves pesquisar um(a) aluno(a).");
        }else{
          window.location=caminhoRecuar+'relatoriosPdf/boletins/?idPMatricula='+idPMatricula
          +'&idPCurso='+idCursoP+'&trimestreApartir='+$(this).attr("id")+"&idPAno="+$("#anosLectivos").val();
        }
      });

      $("#documentos a.termoAproveitamento").click(function(){
        if(idPMatricula=="" || idPMatricula==null){          
          mensagensRespostas2("#mensagemErrada", "Deves pesquisar um(a) aluno(a).");
        }else{
          documentoTratar="termoAproveitamento";
          window.location =caminhoRecuar+"relatoriosPdf/termoAproveitamento/?idPMatricula="
          +idPMatricula+"&documento="+$(this).attr("documento")+"&idPCurso="+idCursoP; 
        }
      }); 
      
      $("#documentos #listaDocumentos a.declaracaoSemNotas").click(function(){
        tipoDeDocumento="declaracaoSemNotas";
        documentoTratar="declaracaoSemNotas";
        if(idPMatricula=="" || idPMatricula==null){
          mensagensRespostas2("#mensagemErrada", "Deves pesquisar um(a) aluno(a).");
        }else{ 
          verificarSeEPossivelTratarODocumento(tipoDeDocumento);
        }
      }); 

      $("#formularioDadosForm").submit(function(){

        var comAssinDirectProv ="nao";
        if($("#comAssinDirectProv").prop("checked")==true){
          comAssinDirectProv="sim";
        }

        var comAssinDirectMunicipal ="nao";
        if($("#comAssinDirectMunicipal").prop("checked")==true){
          comAssinDirectMunicipal="sim";
        }
        var comQRCode = "nao"
        if($("#comQRCode").prop("checked")==true){
          comQRCode="sim";
        }
        
        window.location =caminhoRecuar+"relatoriosPdf/declaracoes/?documentoTratar="+documentoTratar
         +"&idPMatricula="+idPMatricula+"&idPCurso="+idCursoP+"&numeroDeclaracao="+$("#numeroDeclaracao").val()
         +"&efeitoDeclaracao="+$("#efeitoDeclaracao").val()+"&comAssinDirectProv="
         +comAssinDirectProv+"&comAssinDirectMunicipal="
         +comAssinDirectMunicipal+"&nomeDirectorMunicipal="+$("#nomeDirectorMunicipal").val()
         +"&nomeDirectorProvincial="+$("#nomeDirectorProvincial").val()
         +"&viaDocumento="+$("#viaDocumento").val()+"&comQRCode="+comQRCode;
        return false;
      })
  });

  function  pesquisarAluno (){
    directorio = "areaSecretaria/relatorioAluno/";
    http.onreadystatechange = function(){
      if(http.readyState==4){
        $("#sugestoesNomesAlunos").empty()
        JSON.parse(http.responseText.trim()).forEach(function(dado){
          $("#sugestoesNomesAlunos").append("<option value='"+
          dado.numeroInterno+"'>"+dado.nomeAluno+" - "+dado.biAluno+"</option>")
        })      
      }
    }
    enviarComGet("tipoAcesso=pesquisarAluno&valorPesquisado="+$("#valorPesquisado").val());
  }

    function  verificarSeEPossivelTratarODocumento (){
        directorio = "areaSecretaria/relatorioAluno/";
        chamarJanelaEspera("...");
        http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          if(http.responseText.trim()=="podeTratar"){
            if(tipoDeDocumento=="cartEstudante"){
              window.location =caminhoRecuar+"relatoriosPdf/cartoesAlunos/?idPMatricula="+idPMatricula;
            }else{              
                if(seCertificado=="definitivo"){
                  $("#formularioDados #idEfeitoDeclaracao").hide();
                }else{
                  $("#formularioDados #idEfeitoDeclaracao").val("efeitos legais")
                  $("#formularioDados #idEfeitoDeclaracao").show();
                }
                $("#viaDocumento").val(1) 
                $("#formularioDados #comQRCode").prop("checked", true)             
                $("#formularioDados").modal("show");
            }                          
          }else{
           mensagensRespostas2("#mensagemErrada", http.responseText.trim());
          }
        }
      }
      enviarComGet("tipoAcesso=verificarPagamento&idPMatricula="+idPMatricula
        +"&documentoTratar="+documentoTratar+"&classe="+classeAlunoP+"&idPCurso="+idCursoP+"&tipoDeDocumento="+tipoDeDocumento);
    }

    function alterarDadosAlunos(){
        directorio = "areaSecretaria/novaMatricula/";
       chamarJanelaEspera("");
       var form = new FormData(document.getElementById("formularioDadosAlunos"));
       enviarComPost(form);

       http.onreadystatechange = function(){
          if(http.readyState==4){
            resultado = http.responseText.trim()
            if(resultado.trim().substring(0, 1)=="V"){
              mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");                
            }else{
              estadoExecucao="ja";
                mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
            }
          }
        }
    }

    

  function porValoresFormulario(){ 
    dadosAluno.forEach(function(dado){
        $("#formularioDadosAlunos #nomeAluno").val(dado.nomeAluno);
        $("#formularioDadosAlunos #sexoAluno").val(dado.sexoAluno);
        $("#formularioDadosAlunos #dataNascAluno").val(dado.dataNascAluno);
        
        $("#formularioDadosAlunos #numBI").val(dado.biAluno);
        $("#formularioDadosAlunos #dataCaducidadeBI").val(dado.dataCaducidadeBI);
        
        $("#formularioDadosAlunos #dataEmissaoBI").val(dado.dataEBIAluno);
        $("#formularioDadosAlunos #nomePai").val(dado.paiAluno);
        $("#formularioDadosAlunos #nomeMae").val(dado.maeAluno);
        $("#formularioDadosAlunos #nomeEncarregado").val(dado.encarregadoEducacao);
        $("#formularioDadosAlunos #numTelefone").val(dado.telefoneAluno)

       listarClasses(dado.escola.idMatCurso, dado.escola.classeActualAluno,
          "#formularioDadosAlunos #idPCursoForm", "#formularioDadosAlunos #classeAlunoForm")

        if(dado.escola.classeActualAluno==120){
          $("#formularioDadosAlunos #classeAlunoForm").val("FIN_"+dado.escola.idMatFAno);             
        }else{
          $("#formularioDadosAlunos #classeAlunoForm").val(dado.escola.classeActualAluno)
        }
        $("#formularioDadosAlunos #seBolseiro").prop("checked", false)
        $("#formularioDadosAlunos #dadosSobreBolsa").hide()
        if(dado.escola.seBolseiro=="V"){
          $("#formularioDadosAlunos #seBolseiro").prop("checked", true)
          $("#formularioDadosAlunos #dadosSobreBolsa").show(500)
        }
        if(dado.escola.beneficiosDaBolsa!=undefined && dado.escola.beneficiosDaBolsa!=""
         && dado.escola.beneficiosDaBolsa!=null){

          dado.escola.beneficiosDaBolsa.forEach(function(ben){

            if(ben.mes!=""){
              $("#formularioDadosAlunos #dadosSobreBolsa input[idPTipoEmolumento="+
              ben.idPTipoEmolumento+"][mes="+ben.mes+"]").val(new Number(ben.valorPreco))
            }else{
              $("#formularioDadosAlunos #dadosSobreBolsa input[idPTipoEmolumento="+
              ben.idPTipoEmolumento+"]").val(new Number(ben.valorPreco))
            }
          })
        }

        $("#formularioDadosAlunos #emailAluno").val(dado.emailAluno)
        $("#formularioDadosAlunos #acessoConta").val(dado.estadoAcessoAluno)
        $("#formularioDadosAlunos #idMatAnexo").val(dado.escola.idMatAnexo)
        var idGestLinguaEspecialidade = dado.escola.idGestLinguaEspecialidade
        if(idGestLinguaEspecialidade==22){
          idGestLinguaEspecialidade=20;
        }else if(idGestLinguaEspecialidade==23){
          idGestLinguaEspecialidade=21;
        }
        $("#formularioDadosAlunos #lingEspecialidade").val(idGestLinguaEspecialidade)
        $("#formularioDadosAlunos #discEspecialidade").val(dado.escola.idGestDisEspecialidade)
        $("#formularioDadosAlunos #periodoAluno").val(dado.escola.periodoAluno)
        paraTurno(dado.turnoAluno)

        $("#formularioDadosAlunos #tipoDocumento").val(dado.tipoDocumento)
        $("#formularioDadosAlunos #localEmissao").val(dado.localEmissao)
        paraDocumentos()

        $("#formularioDadosAlunos #numeroProcesso").val(dado.escola.numeroProcesso)
        $("#formularioDadosAlunos #estadoDeDesistenciaNaEscola").val(dado.escola.estadoDeDesistenciaNaEscola)
        
        $("#deficiencia").val(dado.deficienciaAluno);
        seleccionarTipoDeDeficiencia(dado.deficienciaAluno);
        $("#tipoDeficiencia").val(dado.tipoDeficienciaAluno);
                   
        passarValoresDaProvincia("#formularioDadosAlunos #pais", "#formularioDadosAlunos #provincia", 
          "#formularioDadosAlunos #municipio", "#formularioDadosAlunos #comuna"
          , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno); 
    })
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
      if($("#formularioDadosAlunos #tipoDocumento").val()=="BI"){
          $("#formularioDadosAlunos #localEmissao").attr("disabled", "")
          $("#formularioDadosAlunos #localEmissao").val("Luanda");
      }else{
        $("#formularioDadosAlunos #localEmissao").removeAttr("disabled")
      }
    }