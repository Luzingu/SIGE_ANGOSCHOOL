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
    directorio = "areaAdministrativa/processamentoDeSalarios/";

    $("#anoCivil").val(anoCivil)
    $("#mesPagamento").val(mesPagamento)
    fazerPesquisa();

    $("#anoCivil, #mesPagamento").change(function(){
      window.location="?anoCivil="+$("#anoCivil").val()
      +"&mesPagamento="+$("#mesPagamento").val()
    })

    $("#novoProcessamento").click(function(){
      $("#formularioProcessamentoSalario input[type=number]").val("0")
      $("#formularioProcessamentoSalario input[type=text]").val("")
      $("#formularioProcessamentoSalario #action").val("processarSalario");
      pegarProfessores();
    })

    $("#formularioProcessamentoSalario input").bind("change keyup", function(){
      calculador()
    })

    $("#funcionario").change(function(){
      listaDadosFuncionarios.forEach(function(dado){
        if(dado.idPEntidade==$("#funcionario").val()){
          valorAuferidoNaInstituicao=new Number(dado.escola.valorAuferidoNaInstituicao);
          pagamentoPorTempo=new Number(dado.pagamentoPorTempo)

          $("#formularioProcessamentoSalario input[type=number]").val("0")
          $("#formularioProcessamentoSalario input[type=text]").val("")

          $("#formularioProcessamentoSalario #salarioBase").val(converterNumerosTresEmTres(dado.escola.valorAuferidoNaInstituicao))
          $("#formularioProcessamentoSalario #pagamentoPorTempo").val(converterNumerosTresEmTres(dado.pagamentoPorTempo))
          $("#formularioProcessamentoSalario #cargaHoraria").val(dado.cargaHoraria)
          $("#formularioProcessamentoSalario #tempoTotLeccionado").val(dado.tempoTotLeccionado)
          $("#formularioProcessamentoSalario #tempoTotNaoLeccionado").val(dado.tempoTotNaoLeccionado)

          calculador()
        }
      })
    })

    $("#formularioProcessamentoSalarioForm").submit(function(){
      manipularFormulario()
      return false
    })

    $("#formularioAnularFacturaForm").submit(function(){
      //manipularFormulario("formularioAnularFactura")
      return false
    })

    

    var repet1=true;
     $("#tabHistorico").bind("click mouseenter", function  disparador(){
        repet1 = true;
        $("#tabHistorico a.cancelar").click(function(){
            if(repet1==true){
              $("#formularioAnularFactura #idPFuncionario").val($(this).attr("idPFuncionario"))
              $("#formularioAnularFactura #idPSalario").val($(this).attr("idPSalario"))
              $("#formularioAnularFactura #action").val("excluirProcessamento");
              mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular este processamento?");               
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
            manipularFormulario("formularioAnularFactura")
            rep=false;
          }         
      })
    })

}

function manipularFormulario(formulario="formularioProcessamentoSalario"){
   var form = new FormData(document.getElementById(formulario+"Form"));
   enviarComPost(form);
   $("#"+formulario).modal("hide");
   chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        estadoExecucao="ja";
        resultado = http.responseText.trim()
        if(resultado.substring(0, 1)=="F"){
          mensagensRespostas('#mensagemErrada', resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas('#mensagemCerta', "Acção concluída com sucesso.");
          listaFuncionarios = JSON.parse(resultado)
          fazerPesquisa()          
        }   
      }
    }
}
function pegarProfessores(){
  enviarComGet("tipoAcesso=pegarProfessores&anoCivil="+anoCivil
    +"&mesPagamento="+mesPagamento);
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      listaDadosFuncionarios = JSON.parse(resultado)
      $("#formularioProcessamentoSalario #funcionario").empty()
      $("#formularioProcessamentoSalario #funcionario").append("<option value=''>Seleccionar</option>")
      listaDadosFuncionarios.forEach(function(dado){
        $("#formularioProcessamentoSalario #funcionario").append("<option value='"+
          dado.idPEntidade+"'>"+dado.nomeEntidade+"</option>")
      })
      $("#formularioProcessamentoSalario").modal("show")
    }
  }
}

function calculador(){
  var salarioTotal = new Number(valorAuferidoNaInstituicao)+
  new Number($("#tempoTotLeccionado").val())*new Number(pagamentoPorTempo)+
  new Number($("#totalSubsidios").val())

  var salarioLiquido = new Number(salarioTotal)-new Number($("#outrosDescontos").val())-new Number($("#IRT").val())
    -new Number($("#segurancaSocial").val())
  $("#salariorLiquido").val(converterNumerosTresEmTres(salarioLiquido))
}



function fazerPesquisa(){
  var contagem=0;
  var html ="";
  listaFuncionarios.forEach(function(dado){
    contagem++;
    html +="<tr><td class='text-center'>"+completarNumero(contagem)+"</td><td class=''>"+dado.nomeEntidade
    +"</td><td class='text-center'>"+dado.salarios.dataPagamento+" &nbsp;&nbsp;"+dado.salarios.horaPagamento
    +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.salarios.salarioLiquido)
    +"</td><td class='text-center'>"+vazioNull(dado.salarios.nomeFuncProc)
    +"</td><td class='text-center'><a href='"+
    caminhoRecuar+"relatoriosPdf/notasDePagamento/notaDePagamentoDeSubsidio.php?idPSalario="+dado.salarios.idPSalario
    +"&idPEntidade="+dado.idPEntidade+"' title='Nota de Pagamento'><i class='fa fa-print'></i></a></td><td class='text-center'><a href='#' class='cancelar text-danger' idPFuncionario='"
    +dado.idPEntidade
    +"' idPSalario='"+dado.salarios.idPSalario+"' class='text-danger'><i class='fa fa-times-circle'></i></a></td></tr>";
  });
  $("#tabHistorico").html(html);
}