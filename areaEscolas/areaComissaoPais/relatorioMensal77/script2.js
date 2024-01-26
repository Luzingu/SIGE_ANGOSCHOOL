    var idPHistoricoConta="";
    var action="";

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaComissaoPais/relatorioMensal77/";

      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)

      fazerPesquisa();
      DataTables("#example1", "sim")

      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })

      var repet1=true;
       $("#tabDados").bind("click mouseenter", function  disparador(){
            repet1 = true;
            $("#tabDados a").click(function(){
              if(repet1==true){
                idPHistoricoConta = $(this).attr("idPHistoricoConta");
                idPMatricula = $(this).attr("idPMatricula");
                action = $(this).attr("action");
                if(action=="anular"){
                  mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular este pagamento?");                     
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
                anularPagamento();
                rep=false;
              }         
          })
        })
    } 

    function anularPagamento(){
      chamarJanelaEspera("...");
      $(".modal").modal("hide");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{
            mensagensRespostas('#mensagemCerta', "O pagamento foi anulado com sucesso.");
            listaPagamentos = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
      enviarComGet("tipoAcesso=anularPagamentoMensalidade&idPHistoricoConta="+idPHistoricoConta
        +"&idPMatricula="+idPMatricula+"&anoCivil="+anoCivil+"&mesPagamento="+mesPagamento);
    }

    function fazerPesquisa(){
      var html="";
      var dinheiroTotal=0;

        listaPagamentos.forEach(function(dado){

          dinheiroTotal +=new Number(dado.pagamentos.precoPago);
          html+= "<tr><td class='toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
          +"</td><td>"+converterData(dado.pagamentos.dataPagamento)
          +"<br>"+dado.pagamentos.horaPagamento+"</td><td>"+dado.pagamentos.nomeFuncionario+"</td><td class=''>"+
          retornarMesExtensa(dado.pagamentos.referenciaPagamento)
          +"</td><td class='text-center'>"+
          converterNumerosTresEmTres(dado.pagamentos.precoPago.toFixed(2))
          +"</td><td class='text-center'><a href='"+caminhoRecuar
          +"relatoriosPdf/reciboPagamento/index.php?idPHistoricoConta="+dado.pagamentos.idPHistoricoConta
          +"&idPMatricula="+dado.idPMatricula+"' class='btn' ><i class='fa fa-print'></i></a><td class='text-center'><a class='btn  text-danger' action='anular' idPHistoricoConta='"+dado.pagamentos.idPHistoricoConta
          +"' idPMatricula='"+dado.idPMatricula+"'><i class='fa fa-times-circle'></i></a></td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }
