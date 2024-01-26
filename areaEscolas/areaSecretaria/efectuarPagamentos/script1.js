  var idPMatricula="";
  var referenciasJaPagas = new Array();
  var totalValor=0;
  var classeActualAluno="";
  var idPCurso=""
  var periodo=""
  var beneficiosDaBolsa = new Array()
  $(document).ready(function(){      
      fecharJanelaEspera();
      seAbrirMenu();
      $("#idPTipoEmolumento").val(idPTipoEmolumento)
      directorio = "areaSecretaria/efectuarPagamentos/";
      DataTables("#example1", "sim")

      $("#btnPesquisarAluno").val("")
      $("#btnPesquisarAluno").keyup(function(){
        listar();
      })

      $("#idPTipoEmolumento").change(function(){ 
        window.location ="?idPTipoEmolumento="+$("#idPTipoEmolumento").val();
      })

      $("#formPagamento #referenciaPagamento").change(function(){
        valoresNoFormulario();
      })

      $("#formPagamento").submit(function(){
        efectuarPagamento()
        return false;
      })

      var repet1=true;
      $("#tabDados").bind("mouseenter click", function (){
          repet1=true; 

          $("#tabDados tr td a.efectuarPagamento").click(function(){

            if(repet1==true){
              
              
              idPMatricula = $(this).attr("idPMatricula")
              $("#formularioPagamento #idPMatricula").val(idPMatricula);

              $("#formularioPagamento #referenciaPagamento").empty()
              listaAlunos.forEach(function(dado){
                if(dado.idPMatricula==idPMatricula){
                  $("#formularioPagamento #fotoAluno").attr("src", "../../../fotoUsuarios/"+dado.fotoAluno)
                  $("#formularioPagamento #nomeAluno").text(dado.nomeAluno)
                  $("#formularioPagamento #grupo").val(dado.grupo);
                  $("#formularioPagamento #nomeCliente").val(dado.nomeAluno);
                  $("#formularioPagamento #nifCliente").val(dado.biAluno);
                  $("#formularioPagamento #classe").val(dado.escola.classeActualAluno)
                  $("#formularioPagamento #periodo").val(dado.escola.periodoAluno)
                  $("#formularioPagamento #idPCurso").val(dado.escola.idMatCurso)
                  classeActualAluno=dado.escola.classeActualAluno
                  if(classeActualAluno==120){
                    classeActualAluno=dado.ultimaClasse
                  }

                  periodo=dado.escola.periodoAluno
                  idPCurso=dado.escola.idMatCurso

                  arrayBolseiros = new Array()
                  if(beneficiosDaBolsa[idPMatricula].length>0){
                    arrayBolseiros = beneficiosDaBolsa[idPMatricula].filter(condicaoBolseiro)
                  }
                  var valorPagar=0
                  arrayBolseiros.forEach(function(tabela){
                    valorPagar=tabela.valorPreco
                  })

                  tabelaprecos.forEach(function(tabela){
                    if(tipoPagamento=="pagAberto"){

                      if(tabela.classe==classeActualAluno && tabela.idCurso==idPCurso && tabela.valor>0){

                        if(valorPagar<=0){
                          valorPagar=tabela.valor
                        }
                        $("#formularioPagamento #referenciaPagamento").append("<option value='"+idAnoActual
                          +"' valor='"+valorPagar+"'>Pagamento</option>")
                      }
                    }else if(tipoPagamento=="pagPorTrimestre"){

                      if(tabela.classe==classeActualAluno &&
                        tabela.idCurso==idPCurso && tabela.valor>0){

                        if(valorPagar<=0){
                          valorPagar=tabela.valor
                        }
                        $("#formularioPagamento #referenciaPagamento").append("<option value='I' valor='"+valorPagar+"'>I Trimestre</option>")
                        $("#formularioPagamento #referenciaPagamento").append("<option value='II' valor='"+valorPagar+"'>II Trimestre</option>")
                        $("#formularioPagamento #referenciaPagamento").append("<option value='III' valor='"+valorPagar+"'>III Trimestre</option>")
                        $("#formularioPagamento #referenciaPagamento").append("<option value='IV' valor='"+valorPagar+"'>Final</option>")
                      }
                    }else if(tipoPagamento=="pagPorClasse"){
                      if(tabela.idCurso==idPCurso && tabela.valor>0){

                        if(valorPagar<=0){
                          valorPagar=tabela.valor
                        }
                        $("#formularioPagamento #referenciaPagamento").append("<option value='"+tabela.classe
                          +"' valor='"+valorPagar+"'>"+retornarNomeDocumento(tabela.classe)+"</option>")
                      }

                    }else if(tipoPagamento=="pagMensal"){

                      mesesAnoLectivo.forEach(function(mes){
                        if(tabela.classe==classeActualAluno && tabela.mes==mes &&
                          tabela.idCurso==idPCurso && tabela.valor>0){

                          if(valorPagar<=0){
                            valorPagar=tabela.valor
                          }
                          $("#formularioPagamento #referenciaPagamento").append("<option value='"+mes
                            +"' valor='"+valorPagar+"'>Pagamento</option>")
                        }
                      })
                    }else if(tipoPagamento=="pagPorAno"){
                      if(tabela.classe==classeActualAluno && tabela.idCurso==idPCurso && tabela.valor>0){

                        if(valorPagar<=0){
                          valorPagar=tabela.valor
                        }
                        $("#formularioPagamento #referenciaPagamento").append("<option value='"+idAnoActual
                          +"' valor='"+valorPagar+"'>Pagamento</option>")
                      }
                    }
                  })

                }
              })
              valoresNoFormulario()
              $("#formularioPagamento").modal("show")
              repet1=false;
            }
          });
      });
 })

  function efectuarPagamento(){
    $("#formularioPagamento").modal("hide");
    var form = new FormData(document.getElementById("formPagamento"));
    chamarJanelaEspera("")
    enviarComPost(form);
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera()
        var resultado = http.responseText.trim()
        if(resultado.substring(0,1)=="F") {
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));    
        }else{
            listar("OKK");
        }
      }
    }
  } 
    function listar(msg1=""){
      http.onreadystatechange = function(){
        if(http.readyState==4){                  
          estadoExecucao="ja"
          var resultado = http.responseText.trim()
          fecharJanelaEspera();
          if(msg1!=""){
            mensagensRespostas("#mensagemCerta", "O pagamento foi efectuado com sucesso.");
          }
          listaAlunos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
      enviarComGet("tipoAcesso=listar&idPTipoEmolumento="+idPTipoEmolumento+"&valorPesquisado="
      +$("#btnPesquisarAluno").val());
    }   


   function  fazerPesquisa(){
      var html ="";

      listaAlunos.forEach(function(dado){
          var classe = dado.escola.classeActualAluno;
          if(classe==120){
            classe = "Finalista";
          }else{
            classe =dado.escola.classeActualAluno;
          }
          var paraBolsaDeEstudo="";
          if(dado.escola.seBolseiro=="V"){
            paraBolsaDeEstudo=" (Bolseiro)"
          }
          beneficiosDaBolsa[dado.idPMatricula]=new Array()
          if(dado.escola.beneficiosDaBolsa!=null && dado.escola.beneficiosDaBolsa!=undefined){
            beneficiosDaBolsa[dado.idPMatricula]=dado.escola.beneficiosDaBolsa
          }

           html += "<tr><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
           +"'>"+dado.nomeAluno+paraBolsaDeEstudo+
           "</td><td class='lead text-center'><a href='"+caminhoRecuar+
           "areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
              +"' class='lead black'>"
              +dado.numeroInterno
            +"</a></td><td class='lead text-center'>"
              +vazioNull(dado.abrevCurso)
            +"</td><td class='lead text-center'>"
            +classe
            +"</td><td class='text-center'><a href='#' class='lead efectuarPagamento text-success' idPMatricula='"
            +dado.idPMatricula
            +"' action='pagamentos' title='Efectuar um Pagamento'><i class='fa fa-check'></i></a></td>"+
            "<td class='lead text-center'>";
            if(dado.seJaTemPagamento!=""){
              html +="<a href='"+caminhoRecuar+
           "relatoriosPdf/reciboPagamento/index.php?idPHistoricoConta="+dado.seJaTemPagamento
              +"&idPMatricula="+dado.idPMatricula+"' class='lead'><i class='fa fa-print'></i><br/> Comprov.</a>";
          }
          html +="</td></tr>";
      })
      $("#tabDados").html(html)
   }


  function valoresNoFormulario(){
    var valorSeleccionado = new Number($("#referenciaPagamento option:selected").attr("valor"))
    $("#formularioPagamento #valorPago").val(valorSeleccionado)    
  }

  function condicaoBolseiro(elem, ind, obj){
    return (new Number(elem.idPTipoEmolumento)==idPTipoEmolumento);
  }
