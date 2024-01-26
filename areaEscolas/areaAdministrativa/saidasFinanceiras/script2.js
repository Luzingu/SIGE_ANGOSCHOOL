var idPHistoricoConta="";
var pagamentosProcessados="";
var tipoPagamentosProcessados="";
var valoresEfectuados="";
var mesesJaPagos = new Array();
var listaDadosFuncionarios= new Array()
var valorAuferidoNaInstituicao=0;
var pagamentoPorTempo=0
window.onload = function(){

    seAbrirMenu();
    fecharJanelaEspera();
    directorio = "areaAdministrativa/saidasFinanceiras/";

    $("#anoCivil").val(anoCivil)
    $("#mesPagamento").val(mesPagamento)
    fazerPesquisa();

    $("#anoCivil, #mesPagamento").change(function(){
      window.location="?anoCivil="+$("#anoCivil").val()
      +"&mesPagamento="+$("#mesPagamento").val()
    })

    $("#contaUsar").change(function(){
      pegarSaldoDisponivelConta()
    })
    $("#novaSaida").click(function(){

      $("#formularioSaidaValores .paraCamposGerais input[type=number].vazio").val("0")
      $("#formularioSaidaValores .paraCamposGerais input[type=text].vazio").val("")
      $("#formularioSaidaValores .paraCamposGerais #contaUsar, #formularioSaidaValores #idPParceira").val("")
      $("#formularioSaidaValores .paraCamposGerais #idPItem").val("")
      $("#formularioSaidaValores .paraCamposGerais #valorTotal").val("")

      $("#formularioSaidaValores .paraCamposGerais").show()
      $(".paraCamposGerais input, .paraCamposGerais select").attr("required", "")
      $("#formularioSaidaValores .paraCamposAnular").hide()
      $(".paraCamposAnular textarea").removeAttr("required")

      $("#formularioSaidaValores #action").val("manipularSaida");
      $("#formularioSaidaValores #valorDisponivel").text("")
      $("#formularioSaidaValores").modal("show")
    })

    $("#formularioSaidaValores #idPItem").change(function(){
      var valor = $("#formularioSaidaValores #idPItem option:selected").attr("valorItem")
      $("#formularioSaidaValores #valorUnitario").val(valor)
      totalizador = new Number(valor)*new Number($("#formularioSaidaValores #quantidade").val())
      $("#formularioSaidaValores #valorTotal").val(converterNumerosTresEmTres(totalizador))
    })


    $("#formularioSaidaValores input[type=number]").bind("change keyup", function(){
      
      totalizador = new Number($("#formularioSaidaValores #valorUnitario").val())*new Number($("#formularioSaidaValores #quantidade").val())
      $("#formularioSaidaValores #valorTotal").val(converterNumerosTresEmTres(totalizador))
    })

    $("#formularioSaidaValoresForm").submit(function(){
        manipularSaida()
        return false
    })

    var repet1=true;
     $("#tabHistorico").bind("click mouseenter", function  disparador(){
        repet1 = true;
        $("#tabHistorico a.anular").click(function(){
            if(repet1==true){
              $("#formularioSaidaValores #idPFactura").val($(this).attr("idPFactura"))
              $("#formularioSaidaValores #action").val("cancelarSaida");
              
              $("#formularioSaidaValores .paraCamposAnular").show()
              $(".paraCamposAnular textarea").attr("required", "")

              $("#formularioSaidaValores .paraCamposGerais").hide()
              $(".paraCamposGerais input, .paraCamposGerais select").removeAttr("required")
              mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular esta saída?");
              repet1=false;
            }
        });
      });

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            fecharJanelaToastPergunta();
            $("#formularioSaidaValores").modal("show")
            rep=false;
          }         
      })
    })

}

function manipularSaida(){
   var form = new FormData(document.getElementById("formularioSaidaValoresForm"));
   enviarComPost(form);
   $("#formularioSaidaValores").modal("hide");
   chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        estadoExecucao="ja";
        resultado = http.responseText.trim()

        if(resultado.substring(0, 1)=="F"){
          $("#formularioSaidaValores").modal("show");
          mensagensRespostas('#mensagemErrada', resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas('#mensagemCerta', "Acção concluída com sucesso.");
          listaSaidaValores = JSON.parse(resultado)
          fazerPesquisa()          
        } 
      }
    }
}

function pegarSaldoDisponivelConta(){
  $("#formularioSaidaValores #tipoConta").val($("#contaUsar").val())
  $("#valorDisponivel").html("<i class='fa fa-spinner'></i>")
  enviarComGet("tipoAcesso=pegarSaldoDisponivel&tipoConta="+$("#contaUsar").val());
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      $("#valorDisponivel").text(converterNumerosTresEmTres(new Number(resultado)))
     
    }
  }
}



function fazerPesquisa(){
  var contagem=0;
  var html ="";
  var dinheiroTotal=0;

  listaSaidaValores.forEach(function(dado){

    dinheiroTotal +=new Number(dado.valorTotal);
    var btnAnular="";
    var notaLiquidacao="";

    if(dado.estadoFactura=="A"){
      estadoDocumento ="<i class='fa fa-check text-success'></i>";
      var btnAnular="<a class='btn anular' action='anular' title='Anular' idPFactura='"+
      dado.idPFactura
      +"'><i class='fa fa-times-circle text-danger'></i></a>";

      notaLiquidacao="<a href='"+
    caminhoRecuar+"relatoriosPdf/notasDePagamento/notaLiquidacao.php?idPFactura="+dado.idPFactura
    +"' title='Nota de Liquidação'><i class='fa fa-print'></i></a>"
    }else{
      estadoDocumento ="<i class='fa fa-times text-danger'></i>";
    }

    html +="<tr><td class=' text-center lead'>"+anoCivil+"/"+dado.numeroFactura+"</td><td class='lead text-center'>"
    +dado.dataEmissao+"<br/>"+dado.horaEmissao
    +"</td><td class='lead'>"+dado.nomeFuncionario
    +"</td><td class='lead'>"+vazioNull(dado.nomeEmpresa)
    +"</td><td class=' text-center lead'>"+converterNumerosTresEmTres(dado.valorTotal)
    +"</td><td class=' text-center lead'>"+estadoDocumento
    +"</td><td class=' text-center lead'>"+notaLiquidacao+"</td><td class='text-center lead'>"+btnAnular+"</td></tr>";
  });
  $("#tabHistorico").html(html);
}