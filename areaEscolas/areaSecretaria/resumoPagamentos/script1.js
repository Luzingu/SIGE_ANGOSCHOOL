    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      entidade ="alunos";
      directorio = "areaSecretaria/resumoPagamentos/";

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
              grupo = $(this).attr("grupo");
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
      chamarJanelaEspera();
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          estadoExecucao="ja";
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
      enviarComGet("tipoAcesso=anularPagamento&idPHistoricoConta="+idPHistoricoConta
        +"&idPMatricula="+idPMatricula
        +"&grupo="+grupo+"&anoCivil="+anoCivil+"&mesPagamento="+mesPagamento);
    }

    function fazerPesquisa(){
      var html="";
      var dinheiroTotal=0;
      $("#totValores").text(0);

        listaPagamentos.forEach(function(dado){
          dinheiroTotal +=new Number(dado.pagamentos.precoPago);

          if(dado.pagamentos.estadoPagamento=="A"){
            estadoDocumento ="<i class='fa fa-check text-success'></i>";
          }else{
            estadoDocumento ="<i class='fa fa-times text-danger'></i>";
          }

          html+= "<tr><td class='toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno+"</td><td class=''>"+dado.pagamentos.designacaoEmolumento
          +"</td><td class='toolTipeImagem'>"+dado.pagamentos.nomeFuncionario
          +"</td><td class='text-center'>"+converterData(dado.pagamentos.dataPagamento)
          +"<br>"+dado.pagamentos.horaPagamento
          +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.pagamentos.precoPago)
          +"</td><td class='text-center'><strong>"+estadoDocumento+"</strong></td><td class='text-center'><a href='"+
          caminhoRecuar+"relatoriosPdf/reciboPagamento/index.php?idPHistoricoConta="+dado.pagamentos.idPHistoricoConta
          +"&idPMatricula="+dado.idPMatricula+"' class='btn' idPMatricula='"
          +dado.idPMatricula+"' idPHistoricoConta='"+dado.pagamentos.idPHistoricoConta
          +"' title='Detalhes'><i class='fa fa-print'></i></a><td><a class='btn ' action='anular' title='Anular' idPHistoricoConta='"+
          dado.pagamentos.idPHistoricoConta
          +"' grupo='"+dado.grupo+"' idPMatricula='"+dado.idPMatricula
          +"'><i class='fa fa-times-circle text-danger'></i></a></td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }