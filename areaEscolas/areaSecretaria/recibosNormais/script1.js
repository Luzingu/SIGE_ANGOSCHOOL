    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaSecretaria/recibosNormais/";

      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)
      fazerPesquisa(); 
      DataTables("#example1", "sim")
      
      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })
      $("#formularioAnularFacturaForm").submit(function(){
        anularFactura();
        return false;
      })

      var repet1=true;
       $("#tabDados").bind("click mouseenter", function  disparador(){
          repet1 = true;
          $("#tabDados a.anular").click(function(){
            if(repet1==true){
              idPDocumento = $(this).attr("idPDocumento");
              $("#formularioAnularFactura #idPDocumento").val(idPDocumento)
              if($(this).attr("action")=="anular"){
                mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular esta factura?");                                                   
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
              fecharJanelaToastPergunta();
              $("#formularioAnularFactura #motivoCancelamento").val("")
              $("#formularioAnularFactura").modal("show")
              rep=false;
            }         
        })
      }) 
    }

    function anularFactura(){
      $("#formularioAnularFactura").modal("hide");
      chamarJanelaEspera("")
      var form = new FormData(document.getElementById("formularioAnularFacturaForm"));
      enviarComPost(form);
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          var resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));    
          }else{
            mensagensRespostas("#mensagemCerta", "A factura foi anulada com sucesso.");
            listaFacturas = JSON.parse(resultado)
            fazerPesquisa()
          }
        }
      }
    }

    function fazerPesquisa(){

      var html="";
      var dinheiroTotal=0;
      $("#totValores").text(0);

      listaFacturas.forEach(function(dado){
        dinheiroTotal +=new Number(dado.valorTotComImposto);
        html+= "<tr><td class='text-center'>"+dado.identificacaoUnica
        +"</td><td class='text-center'>"+converterData(dado.dataEmissao)
        +"<br>"+dado.horaEmissao
        +"</td><td>"+dado.nomeFuncionario
        +"</td><td>"+dado.nomeCliente
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.valorTotComImposto)
        +"</td><td class='text-center'><a href='"+
        caminhoRecuar+"relatoriosPdf/reciboPagamento/index.php?idPDocumento="+dado.idPDocumento
        +"' title='Facturas'><i class='fa fa-print'></i></a><td><a class='btn anular' action='anular' title='Anular' idPDocumento='"+
          dado.idPDocumento
          +"'><i class='fa fa-times-circle text-danger'></i></a></td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }