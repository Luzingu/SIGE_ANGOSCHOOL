  var idPMatricula="";
  var mesesJaPagos = new Array();
  var listaAlunos = new Array();
  var totalMeses=0
  var valorPropina=0

  var valorAnteriormentePago=0
  var beneficiosDaBolsa = new Array();

  $(document).ready(function(){      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaComissaoPais/efectuarPagamentosPersonalizado77/";

      $("#btnPesquisarAluno").val("")
      $("#btnPesquisarAluno").keyup(function(){
        listar();
      })

      $("#formularioPagamento #mesPagar").change(function(){
        $("#formPagamento #mesInicialContar").val($(this).val())
        valoresNoFormulario()
      })

      $("#formularioPagamento #sePagamentoParcelado").change(function(){
        paulinaTietie()
      })
      
      $("#formularioPagamento #valorPropina, #formularioPagamento #valorMulta").bind("keyup change", function(){

        var total = new Number($("#formularioPagamento #valorPropina").val())+
          new Number($("#formularioPagamento #valorMulta").val())

        $("#formularioPagamento #valorPagar").val(total)
        $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres(total))

        if(new Number($("#formularioPagamento #valorPagar").val())>=valorPropina){
          $("#formularioPagamento #sePagamentoParcelado").prop("checked", false)
        }else{
          $("#formularioPagamento #sePagamentoParcelado").prop("checked", true)
        }
      })
      
      $("#formularioPagamento #valorSomar").bind("keyup change", function(){
        $("#formularioPagamento #valorPropina").val((valorAnteriormentePago+new Number($(this).val())))
        
        var total = new Number($("#formularioPagamento #valorPropina").val())+
          new Number($("#formularioPagamento #valorMulta").val())

        $("#formularioPagamento #valorPagar").val(total)
        $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres(total))

        if(new Number($("#formularioPagamento #valorPagar").val())>=valorPropina){
          $("#formularioPagamento #sePagamentoParcelado").prop("checked", false)
        }else{
          $("#formularioPagamento #sePagamentoParcelado").prop("checked", true)
        }
      })

      $("#formPagamento").submit(function(){
        efectuarPagamento()
        return false;
      })

      var repet1=true;
      $("#tabContas tbody").bind("mouseenter click", function (){
          repet1=true; 

          $("#tabContas tbody tr td a.alteracao").click(function(){
            
            if(repet1==true){
              
              $("#formularioPagamento #fotoAluno").attr("src", "../../../fotoUsuarios/"+$(this).attr("fotoAluno"))
              $("#formularioPagamento #nomeAluno").text($(this).attr("nomeAluno"))
              $("#formularioPagamento #nomeCliente").val($(this).attr("nomeAluno"))
              $("#formularioPagamento #nifCliente").val($(this).attr("biAluno"))
              $("#formularioPagamento #idPMatricula").val($(this).attr("idPMatricula"))
              idPMatricula = $(this).attr("idPMatricula")

              classeAluno = $(this).attr("classe")
              if(classeAluno==120){
                classeAluno=$(this).attr("ultimaClasse")
              }
              idMatCurso = $(this).attr("idPCurso")
              $("#formularioPagamento #classe").val($(this).attr("classe"))
              $("#formularioPagamento #idPCurso").val($(this).attr("idPCurso"))

              $("#formularioPagamento #action").val($(this).attr("action"))

              if(new Number($(this).attr("seJaTemPagamento"))>0){
                $("#formularioPagamento #mesPagar").attr("disabled", "")
              }else{
                $("#formularioPagamento #mesPagar").removeAttr("disabled")
              }

              $("#formularioPagamento #action").val($(this).attr("action"))

              if($(this).attr("action")=="efectuarPagamento"){
                $("#formularioPagamento #action").val($(this).attr("action"))
                
                $("#formularioPagamento #valorMulta").removeAttr("readonly")
                $("#formularioPagamento #valorPropina").removeAttr("readonly")
                $("#formularioPagamento #valorSomar").removeAttr("max")
                $("#formularioPagamento #valorSomar").removeAttr("min")
                $("#formularioPagamento #valorSomar").removeAttr("required")
                $("#formularioPagamento .valSoma").hide()
                
                pegarPagarPagamentosJaEfectuados($(this).attr("idPMatricula"))
              }else{
                arrayMeseses = mesesParaPagar.filter(condicaoClasse)
                if(beneficiosDaBolsa[idPMatricula].length>0){
                  arrayMeseses = beneficiosDaBolsa[idPMatricula].filter(condicaoBolseiro)
                }

                htmlOptions=""
                arrayMeseses.forEach(function(dado){
                  htmlOptions +="<option value='"+new Number(dado.mes)+"' posicao='"+
                  new Number(dado.posicao)+"' valorPreco='"+
                    new Number(dado.valorPreco)+"'>"+retornarMesExtensa(new Number(dado.mes))+"</option>"
                })
                $("#formPagamento select#mesPagar").html(htmlOptions)

                $("#formularioPagamento #sePagamentoParcelado").prop("checked", true)

                $("#formularioPagamento #mesPagar").val($(this).attr("referenciaOperacao"))

                valorPropina = new Number($("#formularioPagamento #mesPagar option:selected").attr("valorPreco"))
                $("#formularioPagamento #divPropina").text(converterNumerosTresEmTres(valorPropina))

                valorAnteriormentePago = new Number($(this).attr("precoPago"))

                $("#formularioPagamento #valorPropina").val($(this).attr("precoPago"))
                $("#formularioPagamento #valorPagar").val($(this).attr("precoPago"))

                $("#formularioPagamento #valorPropina").attr("max", valorPropina)
                $("#formularioPagamento #valorPropina").attr("min", (new Number($(this).attr("precoPago"))+5))

                $("#formularioPagamento #valorMulta").attr("max", valorPropina)
                $("#formularioPagamento #valorMulta").attr("min", $(this).attr("precoMulta"))

                $("#formularioPagamento #valorMulta").val($(this).attr("precoMulta"))

                $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres($(this).attr("precoPago")))
                $("#formularioPagamento #idPHistoricoConta").val($(this).attr("idPHistoricoConta"))
                
                
                $("#formularioPagamento #valorMulta").attr("readonly", "")
                $("#formularioPagamento #valorPropina").attr("readonly", "")
                $("#formularioPagamento #valorSomar").val(0)
                $("#formularioPagamento #valorSomar").attr("max", (valorPropina-valorAnteriormentePago))
                $("#formularioPagamento #valorSomar").attr("min", 0)
                $("#formularioPagamento #valorSomar").attr("required", "")
                $("#formularioPagamento .valSoma").show()

                $("#formularioPagamento").modal("show")
              }

                      
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
              htmlOptions +="<option value='"+new Number(dado.mes)+"' posicao='"+new Number(dado.posicao)+"' valorPreco='"+
              new Number(dado.valorPreco)+"'>"+retornarMesExtensa(new Number(dado.mes))+"</option>";
            }
          })
          $("#formPagamento select#mesPagar").html(htmlOptions)
          valoresNoFormulario();
          $("#formularioPagamento").modal("show")
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

      var i=0
      $("#totContas").text(completarNumero(listaAlunos.length))
      listaAlunos.forEach(function(dado){
          i++;
          var classe = dado.escola.classeActualAluno;
          if(classe==120){
            classe = "Finalista";
          }
          beneficiosDaBolsa[dado.idPMatricula]=new Array()
          if(dado.escola.beneficiosDaBolsa!=null && dado.escola.beneficiosDaBolsa!=undefined){
            beneficiosDaBolsa[dado.idPMatricula]=dado.escola.beneficiosDaBolsa
          }
          var paraBolsaDeEstudo="";
          if(dado.escola.seBolseiro=="V"){
            paraBolsaDeEstudo=" (Bolseiro)"
          }
          var dadosAluno = "duracaoCurso='"+dado.duracao+"' idPMatricula='"+dado.idPMatricula
          +"'  duracao='"+dado.duracao+"' classe='"+dado.escola.classeActualAluno+"' idPCurso='"+dado.idPNomeCurso
          +"' nomeAluno='"+dado.nomeAluno+"' biAluno='"+dado.biAluno+"' fotoAluno='"+dado.fotoAluno+"' numeroInterno='"
          +dado.numeroInterno+"' idAnoMatricula='"+dado.idMatAno+"' seJaTemPagamento='"+
          dado.seJaTemPagamento+"' ultimaClasse='"+dado.ultimaClasse+"'"

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
            +"</td><td class='lead text-center'>"+
            dado.totalMesesJaPagos+"</td><td class='lead text-center'>"
            if(dado.seTemPagamentoPendente=="V"){
              html +=converterNumerosTresEmTres(dado.precoPago)+"&nbsp;&nbsp;&nbsp;<a href='#' "+dadosAluno+" precoPago='"+dado.precoPago
              +"' precoMulta='"+dado.precoMulta+"'  precoPago='"+dado.precoPago
              +"' idPHistoricoConta='"+dado.idPHistoricoConta
              +"' referenciaOperacao='"+dado.referenciaOperacao+"' idPMatricula='"+dado.idPMatricula
              +"' action='pagarPagamentoPendente' title='Pagar' class='lead alteracao text-success'><i class='fa fa-sign-out-alt fa-2x'></i></a>";
            }else{
              html +="<a href='#' "+dadosAluno+" action='efectuarPagamento' idPMatricula='"+dado.idPMatricula
              +"' title='Efectuar novo pagamento' class='lead alteracao text-success'><i class='fa fa-check fa-2x'></i></a>"
            }
          html +="</td><td class='lead text-center'>"
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
    valorPropina = $("#formularioPagamento #mesPagar option:selected").attr("valorPreco")
    $("#formularioPagamento #divPropina").text(converterNumerosTresEmTres(valorPropina))
    $("#formularioPagamento #valorPropina, #formularioPagamento #valorPagar").val(valorPropina) 
    $("#formularioPagamento #valorPropina, #formularioPagamento #valorMulta").attr("max", valorPropina)
    $("#formularioPagamento #valorPropina").attr("max", valorPropina)
    $("#formularioPagamento #valorMulta").attr("min", 0)
    $("#formularioPagamento #valorPropina").attr("min", 100)

    var totalPagar = new Number($("#formularioPagamento #valorPropina").val())+
          new Number($("#formularioPagamento #valorMulta").val())

    $("#formularioPagamento #divPagar").text(converterNumerosTresEmTres(totalPagar))
    $("#formularioPagamento #valorMulta").val(0)        
  }

  function paulinaTietie(){
    if(new Number($("#formularioPagamento #valorPagar").val())>=valorPropina){
      $("#formularioPagamento #sePagamentoParcelado").prop("checked", false)
    }
  }
  function condicaoClasse(elem, ind, obj){
    return (elem.classe==classeAluno && elem.idPCurso==idMatCurso);
  }
  function condicaoBolseiro(elem, ind, obj){
    return (new Number(elem.idPTipoEmolumento)==1);
  }