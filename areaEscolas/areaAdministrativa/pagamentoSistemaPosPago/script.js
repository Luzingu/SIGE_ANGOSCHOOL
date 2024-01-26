var mensagemEspera ="Efectuando depósito...";
  var idHistoricoConta="";
  var posicaoArray=-5;
  var accao="";

  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaAdministrativa/pagamentoSistemaPosPago/";

      fazerPesquisa();

      $("#btnAdicionarSaldo").click(function(){
          $("#formularioAdicionarSaldo").modal("show")
      })

      $("#formularioAdicionarSaldo").submit(function(){
        $("#formularioAdicionarSaldo #action").val("processarPagamento")
        processarPagamento();
        return false;
      })
   

      var repet=true;
      $("#movimentos tbody").bind("click mouseenter", function (){
          repet=true;
          $("#movimentos tbody tr td a.cancelar").click(function(){

              if(repet==true){
                  accao="anularMovimento";
                  idPPagamento = $(this).attr("idPPagamento");
                  $("#formularioAdicionarSaldo #idPPagamento").val(idPPagamento)
                  $("#formularioAdicionarSaldo #action").val("anularProcessarPagamento")                 
                  mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular o processamento deste pagamento?");                  
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
          canc="<a href='#' class='text-danger cancelar' idPPagamento='"+dado.pagamentos.idPPagamento
         +"'><i class='fa fa-times'></i></a>";
         }
          html += "<tr><td class='lead'>"+dado.pagamentos.horaReqPagamento+"<br/>"
           +converterData(dado.pagamentos.dataReqPagamento)+"</td><td class='lead'>"+dado.pagamentos.argumentoRequerente
           +"</td><td class='lead text-center'>"+converterNumerosTresEmTres(dado.pagamentos.valorTotalPago)
           +"<td class='lead'>"+vazioNull(dado.pagamentos.horaRespPagamento)+"<br/>"
           +vazioNull(converterData(dado.pagamentos.dataRespPagamento))+"</td><td class='lead'>"+vazioNull(dado.pagamentos.argumentoResposta)
           +"</td><td class='lead'><a title='Comprovativo' href='../../../Ficheiros/Escola_"+
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
  