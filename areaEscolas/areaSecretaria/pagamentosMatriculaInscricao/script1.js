    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      entidade ="alunos";
      directorio = "areaSecretaria/pagamentosMatriculaInscricao/";
 
      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)

      $("#referenciaPagamento, #luzingu").change(function(){
        conseguimos()
      })
      fazerPesquisa(); 
      DataTables("#example1", "sim")
      
      $("#novoPagamento").click(function(){

        $("#formularioMatriculaInscricao .paraCamposGerais").show()
        $("#formularioMatriculaInscricao .paraCamposGerais select,"+
          " #formularioMatriculaInscricao .paraCamposGerais #nomeCliente").attr("required", "")

        $("#formularioMatriculaInscricao .paraCampoMotivo").hide()
        $("#formularioMatriculaInscricao .paraCampoMotivo textarea").removeAttr("required")

        $("#formularioMatriculaInscricao #action").val("novoPagamento")
        $("#formularioMatriculaInscricao #nomeCliente, #formularioMatriculaInscricao #nifCliente").val("")

        conseguimos()
        $("#formularioMatriculaInscricao").modal("show")
      })

      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })

      $("#formularioMatriculaInscricaoForm").submit(function(){
        formularioPagamentoInscricao()
        return false;
      })

      var repet1=true;
       $("#tabDados").bind("click mouseenter", function  disparador(){
          repet1 = true;
          $("#tabDados a.anularPagamento").click(function(){
            if(repet1==true){
              idPPagamento = $(this).attr("idPPagamento");
              $("#formularioMatriculaInscricao #idPPagamento").val(idPPagamento)
              $("#formularioMatriculaInscricao #action").val("anularPagamento")
              mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular este pagamento?");
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

              $("#formularioMatriculaInscricao .paraCamposGerais").hide()

              $("#formularioMatriculaInscricao .paraCamposGerais select,"+
          " #formularioMatriculaInscricao .paraCamposGerais input").removeAttr("required")

              $("#formularioMatriculaInscricao .paraCampoMotivo").show()
              $("#formularioMatriculaInscricao .paraCampoMotivo textarea").attr("required", "")
            
              $("#formularioMatriculaInscricao").modal("show")

              rep=false;
            }         
        })
      }) 
    }

    function conseguimos(){
      var luzingu = $("#luzingu").val().split("-")
      var classe = luzingu[1]
      var idCurso = luzingu[2]

      precoEmolumentos.forEach(function(dado){
        if(dado.emolumentos.codigoEmolumento==$("#referenciaPagamento").val() &&
         dado.emolumentos.classe==classe && (classe<=9 || dado.emolumentos.idCurso==idCurso)){
          $("#formularioMatriculaInscricao #valorPago").val(converterNumerosTresEmTres(dado.emolumentos.valor))
        }
      })
    }



    function formularioPagamentoInscricao(){
      $("#formularioMatriculaInscricao").modal("hide")
      chamarJanelaEspera("");
     http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          if(resultado.substring(0, 1)=="F"){
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{               
            mensagensRespostas('#mensagemCerta', "Acção concluída com sucesso.");
            listaPagamentos = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
      var form = new FormData(document.getElementById("formularioMatriculaInscricaoForm"));
      enviarComPost(form);
    }

    function fazerPesquisa(){
      var html="";
      var dinheiroTotal=0;
      $("#totValores").text(0);

      var contagem=0
      listaPagamentos.forEach(function(dado){
        contagem++
        dinheiroTotal +=new Number(dado.valorPago);

        if(dado.estadoPagamento=="A"){
          estadoPagamento ="<i class='fa fa-check text-success'></i>";
        }else{
          estadoPagamento ="<i class='fa fa-times text-danger'></i>";
        }
        if(dado.referenciaPagamento=="matricula"){
          referenciaPagamento="Matricula"
        }else{
          referenciaPagamento="Inscrição"
        }

        html+= "<tr><td class='text-center'>"+completarNumero(contagem)+"</td><td class='text-center'>"
        +dado.dataPagamento+"<br>"+dado.horaPagamento
        +"</td><td class=''>"
        +vazioNull(dado.nomeAluno)
        +"</td><td>"+referenciaPagamento
        +"</td><td class='text-center'>"+dado.contaUsar
        +"</td><td class='text-center'>"+dado.rpm
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.valorPago)
        +"</td><td class='text-center'><strong>"+estadoPagamento+"</strong></td><td class='text-center'><a href='"+
        caminhoRecuar+"relatoriosPdf/reciboPagamento/index.php?idPPagamento="+dado.idPPagamento
        +"' class='btn'><i class='fa fa-print'></i></a></td><td><a class='btn anularPagamento' action='anular' title='Anular' idPPagamento='"+
        dado.idPPagamento
        +"'><i class='fa fa-times-circle text-danger'></i></a></td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }