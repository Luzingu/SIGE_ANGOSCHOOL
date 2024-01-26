  var idPMatricula="";
  var mesesJaPagos = new Array();
  var listaAlunos = new Array();
  var totalMeses=0;
  var valorPropina=0 
  var beneficiosDaBolsa = new Array();

  $(document).ready(function(){      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaComissaoPais/efectuarPagamentos77/";

      $("#btnPesquisarAluno").val("")
      $("#btnPesquisarAluno").keyup(function(){
        listar();
      })
      $("#formPagamento #quantMes").change(function(){
          valoresNoFormulario();
      })
      $("#formularioPagamento #mesPagar").change(function(){

        $("#formPagamento #quantMes").empty()
          $("#formPagamento #quantMes").append("<option value=''>Seleccionar</option>")
        for(var i=1; i<=(totalMeses-
          $("#formularioPagamento #mesPagar option:selected").attr("posicao")); i++){

          $("#formPagamento #quantMes").append("<option>"+i+"</option>")
        }
        $("#formPagamento #mesInicialContar").val($(this).val())
        $("#formularioPagamento #divPropina").text(0)
        $("#formularioPagamento #valorPropina").val(0)
        $("#formularioPagamento #valorPagar").val(0)
        $("#formularioPagamento #divPagar").text(0)
        $("#formularioPagamento #valorMulta").val(0)
      })
      $("#formularioPagamento #valorMulta").bind("change keyup", function(){
        $("#formularioPagamento #valorPagar").val(valorPropina+new Number($(this).val()))
        $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres(new Number(valorPropina)+new Number($(this).val())))
      })

      $("#formPagamento").submit(function(){
        efectuarPagamento()
        return false;
      })

      var repet1=true;
      $("#tabContas tbody").bind("mouseenter click", function (){
          repet1=true; 

          $("#tabContas tbody tr td a.efectuarPagamento").click(function(){
            
            if(repet1==true){
              
              $("#formularioPagamento #fotoAluno").attr("src", "../../../fotoUsuarios/"+$(this).attr("fotoAluno"))
              $("#formularioPagamento #nomeAluno").text($(this).attr("nomeAluno"))

              $("#formularioPagamento #nomeCliente").val($(this).attr("nomeAluno"))
              $("#formularioPagamento #nifCliente").val($(this).attr("biAluno"))
              
              $("#formularioPagamento #idPMatricula").val($(this).attr("idPMatricula"))
              idPMatricula = $(this).attr("idPMatricula")
              seBolseiro = $(this).attr("seBolseiro")
              idMatCurso = $(this).attr("idPCurso")
              classeAluno = $(this).attr("classe")
              if(classeAluno==120){
                classeAluno= $(this).attr("ultimaClasse")
              }

              $("#formularioPagamento #classe").val($(this).attr("classe"))
              $("#formularioPagamento #idPCurso").val($(this).attr("idPCurso"))

              $("#formularioPagamento #action").val("efectuarPagamento")
              $("#formularioPagamento #Cadastrar").html('<i class="fa fa-check"></i> Concluir');

              if(new Number($(this).attr("seJaTemPagamento"))!=""){
                $("#formularioPagamento #mesPagar").attr("disabled", "")
              }else{
                $("#formularioPagamento #mesPagar").removeAttr("disabled")
              }
              pegarPagarPagamentosJaEfectuados($(this).attr("idPMatricula"))

                      
              repet1=false;
            }
          });
      });
 })

  function efectuarPagamento(){
    $("#formularioPagamento").modal("hide");
    chamarJanelaEspera("...")
    http.onreadystatechange = function(){
      if(http.readyState==4){
        var resultado = http.responseText.trim()
        if(resultado.substring(0,1)=="F") {
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));    
        }else{
            listar("OKK");
        } 
      }
    }
    enviarComPost(new FormData(document.getElementById("formPagamento")));
  } 


    function pegarPagarPagamentosJaEfectuados(idPMatricula){
      chamarJanelaEspera("...")
      $("#formularioPagamento").modal("hide");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          var resultado = http.responseText.trim()
          mesesJaPagos = JSON.parse(resultado);

          var htmlOptions="";
          $("#formPagamento #quantMes").empty();
          $("#formPagamento #quantMes").append("<option value=''>Seleccionar</option>")

          var ultimoMesPago=-1;
          totalMeses=0;

          arrayMeseses = mesesParaPagar.filter(condicaoClasse)
          if(beneficiosDaBolsa[idPMatricula].length>0){
            arrayMeseses = beneficiosDaBolsa[idPMatricula].filter(condicaoBolseiro)
          }
          arrayMeseses.forEach(function(dado){
            if(seJaFoiPago(new Number(dado.mes))==true){
              ultimoMesPago=new Number(dado.posicao)
            }
          })

          arrayMeseses.forEach(function(dado){
            if(seJaFoiPago(new Number(dado.mes))==false && new Number(dado.posicao)>ultimoMesPago){
              totalMeses++
              if(totalMeses==1){
                $("#formPagamento #mesInicialContar").val(new Number(dado.mes))
              }
              $("#formPagamento #quantMes").append("<option>"+
                totalMeses+"</option>")
              htmlOptions +="<option value='"+(new Number(dado.mes))+"' posicao='"+new Number(dado.posicao)+"' valorPreco='"+
              new Number(dado.valorPreco)+"'>"+retornarMesExtensa(new Number(dado.mes))+"</option>";
            }
          })

          $("#formPagamento #quantMes").val("")
          $("#formPagamento select#mesPagar").html(htmlOptions)
          valoresNoFormulario();
          $("#formularioPagamento").modal("show");
        }
      }
      enviarComGet("tipoAcesso=pegarPagarPagamentosJaEfectuados&idPMatricula="+
       idPMatricula+"&idPAno="+$("#formPagamento #idPAno").val());
    } 


    
    function listar(msg1=""){
      if(msg1==""){
        $("#tabContas tbody").html("<tr><td colspan='6' class='text-center' lead>Pesquisando...</td></tr>");
      } 
      http.onreadystatechange = function(){
        if(http.readyState==4){                  
          estadoExecucao="ja"
          var resultado = http.responseText.trim()
          $("#tabContas tbody").html("<tr><td colspan='6' class='text-center' lead>Não foi encontrado nenhum aluno.</td></tr>");
          fecharJanelaEspera();
          if(msg1!=""){
            mensagensRespostas("#mensagemCerta", "O pagamento foi efectuado com sucesso.");
          }
          listaAlunos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
      enviarComGet("tipoAcesso=listar&valorPesquisado="+$("#btnPesquisarAluno").val()
        +"&idPAno="+$("#formularioPagamento #idPAno").val());
    }   

   function  fazerPesquisa(){
      var html ="";
      $(".quantidadeTotal").text(completarNumero(listaAlunos.length))
      var i=0
      listaAlunos.forEach(function(dado){
          i++;

          var classe = dado.escola.classeActualAluno;
          if(classe==120){
            classe = "Finalista";
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
            +"</td><td class='lead text-center'>"
              +dado.totalPagamentos
            +"</td><td class='text-center'><a href='#' class='lead efectuarPagamento text-success' idPMatricula='"+dado.idPMatricula
            +"' action='pagamentos' title='Efectuar um Pagamento' classe='"+dado.escola.classeActualAluno+"' idPCurso='"+dado.idPNomeCurso
            +"' nomeAluno='"
            +dado.nomeAluno+"'  ultimaClasse='"+dado.ultimaClasse+"' fotoAluno='"+dado.fotoAluno
            +"' biAluno='"+dado.biAluno+"' numeroInterno='"+dado.numeroInterno
            +"'  seJaTemPagamento='"+dado.seJaTemPagamento
            +"' seBolseiro='"+dado.escola.seBolseiro+"'><i class='fa fa-check fa-2x'></i></a></td>"+
            "<td class='lead text-center'>";
            if(dado.seJaTemPagamento!=""){
              html +="<a href='"+caminhoRecuar+
           "/relatoriosPdf/reciboPagamento/index.php?idPHistoricoConta="+dado.seJaTemPagamento
              +"&idPMatricula="+dado.idPMatricula+"' class='lead'><i class='fa fa-print'></i><br/> Comprov.</a>";
            }
          html +="</td></tr>";
      })
      $("#tabContas tbody").html(html)
   }

   function seJaFoiPago(mes){
    retorno=false;
    mesesJaPagos.forEach(function(d){
        if(d.pagamentos.referenciaPagamento==mes){
            retorno =true;
        }
    })
    return retorno;
   }


  function valoresNoFormulario(){
    var posicaoInicial = new Number($("#formularioPagamento #mesPagar option:selected").attr("posicao"))
    valorPropina=0;

    for(var i=posicaoInicial; i<(posicaoInicial+new Number($("#formularioPagamento #quantMes").val())); i++){
        valorPropina += new Number($("#formularioPagamento #mesPagar option[posicao="+i+"]").attr("valorPreco"))
    }

    $("#formularioPagamento #divPropina").text(converterNumerosTresEmTres(valorPropina))
    $("#formularioPagamento #valorPropina").val(valorPropina)

     $("#formularioPagamento #valorPagar").val(valorPropina+new Number($("#formularioPagamento #valorMulta").val()))
     $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres(new Number(valorPropina)+new Number($("#formularioPagamento #valorMulta").val())))
  }

  
function condicaoClasse(elem, ind, obj){
  return (elem.classe==classeAluno && elem.idPCurso==idMatCurso);
}

function condicaoBolseiro(elem, ind, obj){
  return (new Number(elem.idPTipoEmolumento)==1);
}