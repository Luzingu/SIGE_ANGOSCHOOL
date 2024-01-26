  var valorTotalPago=0;
  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaGestaoEscolas/CPainel/pagamentoSistemaPosPago00/";
      fazerPesquisa();
      
      $("#idPEscola").val(idPEscola)

      $("#formularioAdicionarSaldo").submit(function(){
        $("#formularioAdicionarSaldo").modal("hide");
         $("#formularioAdicionarSaldo #action").val("aceitarPagamento");
         processarPagamento();
         return false;
      })
      $("#formularioAdicionarSaldo .submitter").click(function(){
        $("#formularioAdicionarSaldo").modal("hide")
        $("#formularioAdicionarSaldo #action").val("recusarPagamento");
        mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular este pagamento?");
      })
      $("#idPEscola").change(function(){
        window.location="?idPEscola="+$("#idPEscola").val();
      })
   

      var repet=true;
      $("#movimentos tbody").bind("click mouseenter", function (){
          repet=true;
          $("#movimentos tbody tr td a.operacionalizar").click(function(){

              if(repet==true){

                  accao="anularMovimento";
                  idPPagamento = $(this).attr("idPPagamento");
                  $("#formularioAdicionarSaldo #idPPagamento").val(idPPagamento)
                    porValoresNoFormulario();
                  repet=false;
              }

          });
      });



       var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
               fecharJanelaToastPergunta();
               processarPagamento();            
            rep=false;
          }         
      })
    }) 
  }) 

  function porValoresNoFormulario(){
    pagamentos_escola.forEach(function(dado){
        if(dado.pagamentos.idPPagamento==idPPagamento){
          $("#formularioAdicionarSaldo #tempoTotalExtender").val(dado.pagamentos.tempoTotalExtender
            +" dia(s)") 
          $("#formularioAdicionarSaldo #argumentoResposta").val("") 
          $("#formularioAdicionarSaldo #argumentoRequerente").text(dado.pagamentos.argumentoRequerente)        
          $("#formularioAdicionarSaldo #valorTotalPago").val(converterNumerosTresEmTres(dado.pagamentos.valorTotalPago))
          
          valorTotalPago = dado.pagamentos.valorTotalPago;
          $("#formularioAdicionarSaldo #valorDescontado").val(0)
          $("#formularioAdicionarSaldo #valorFinal").val(dado.pagamentos.valorTotalPago)
          $("#formularioAdicionarSaldo #valorDescontado").attr("max", (dado.pagamentos.valorTotalPago/10))
          $("#formularioAdicionarSaldo").modal("show");
        }
    })
  }





  function fazerPesquisa(){
      var html="";
      
        if(jaTemPaginacao==false){
          paginacao.baraPaginacao(pagamentos_escola.length, 50);
        }else{
            jaTemPaginacao=false;
        }

       var contagem=-1;
      pagamentos_escola.forEach(function(dado){
        contagem++;
        if(contagem>=paginacao.comeco && contagem<=paginacao.final){
          
          var estado= dado.pagamentos.estadoPagamento;
          if(estado=="V"){
            estado="<i class='fa fa-check fa-2x text-success' title='Vencido'></i>";
          }else if(estado=="Y"){
            estado="<i class='fa fa-spinner fa-2x text-primary' title='Em Processamento'></i>";
          }else{
            estado="<i class='fa fa-times fa-2x text-danger' title='Recusada'></i>";
          }
          var canc = "";
         if(dado.pagamentos.estadoPagamento=="Y"){
          canc="<a href='#' class='text-primary operacionalizar' idPPagamento='"+dado.pagamentos.idPPagamento
         +"'><i class='fa fa-sign-out-alt'></i></a>";
         }
          html += "<tr><td class='lead'>"+dado.pagamentos.horaReqPagamento+"<br/>"
           +converterData(dado.pagamentos.dataReqPagamento)+"</td><td class='lead'>"+dado.pagamentos.argumentoRequerente+
           "</td><td class='lead text-center'>"+converterNumerosTresEmTres(dado.pagamentos.valorTotalPago)
           +"</td><td class='lead text-center'>"+converterNumerosTresEmTres(dado.pagamentos.valorDescontado)
           +"</td><td class='lead'>"+vazioNull(dado.pagamentos.horaRespPagamento)+"<br/>"
           +vazioNull(converterData(dado.pagamentos.dataRespPagamento))+"</td><td class='lead'>"+vazioNull(dado.pagamentos.argumentoResposta)
           +"</td><td class='lead'><a title='Comprovativo' href='../../../../Ficheiros/Escola_"+
           dado.idPEscola+"/Bolderon_Pag_Sistema/"+dado.pagamentos.imgBolderon+"'><i class='fa fa-print'></i></a></td><td class='lead text-center'>"+estado
           +"</td><td class='lead text-center'>"+canc+"</td></tr>";
        }
      })
      $("#movimentos tbody").html(html); 

      
   }

   function processarPagamento(){
    $(".modal").modal("hide");
    chamarJanelaEspera("");
    http.onreadystatechange = function(){
        if(http.readyState==4){
            resultado = http.responseText.trim()
            fecharJanelaEspera();
            if(resultado.trim().substring(0, 1)=="F"){              
              mensagensRespostas("#mensagemErrada", "Não foi possível processar o seu pedido.")
            }else{ 
              mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.")         
              pagamentos_escola = JSON.parse(resultado);
              fazerPesquisa();
            }            
        }
    }
    enviarComPost(new FormData(document.getElementById("formularioAdicionarSaldoForm")));
  }
  