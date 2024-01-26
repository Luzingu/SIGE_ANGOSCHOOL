    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/contasFinanceiras/";

      fazerPesquisa();
      DataTables("#example1", "sim")

      $("#btnNovaConta").click(function(){
        limparFormulario("#formularioContaBancaria")
        $("#formularioContaBancaria #action").val("novaConta")
        $("#formularioContaBancaria").modal("show")
      })

      $("#formularioContaBancariaForm").submit(function(){
        manipularContasBancarias();
        return false
      })
      $("#formularioTransacaoForm").submit(function(){
        manipularTransacao();
        return false
      })

      var repet=true
      $("#tabDados").bind("mouseenter click", function(){
          repet=true
          $("#tabDados tr td a").click(function(){
            if(repet==true){

              $("#formularioContaBancaria #action").val($(this).attr("action"))
              idPContaFinanceira = $(this).attr("idPContaFinanceira")
              $("#formularioContaBancaria #idPContaFinanceira").val(idPContaFinanceira)
              if($(this).attr("action")=="editarContaBancaria"){

                listaContaBancaria.forEach(function(dado){
                  if(dado.idPContaFinanceira==idPContaFinanceira){

                    $("#formularioContaBancaria #descricaoConta").val(dado.descricaoConta)
                    $("#formularioContaBancaria #bancoConta").val(dado.bancoConta)
                    $("#formularioContaBancaria #numeroConta").val(dado.numeroConta)
                    $("#formularioContaBancaria #ibanConta").val(dado.ibanConta)
                    $("#formularioContaBancaria #categoriaTipoConta").val(dado.categoriaTipoConta)
                    $("#formularioContaBancaria #hierarquia").val(dado.hierarquia)
                    $("#formularioContaBancaria").modal("show")
                  }
                })
              }else{
                mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar esta matricula?")
              }
              repet=false
            }
          })
          var rep=true;
          $("body").bind("mouseenter click", function(){
                rep=true;
              $("#janelaPergunta #pergSim").click(function(){
                if(rep==true){
                    fecharJanelaToastPergunta()
                    manipularContasBancarias();
                  rep=false;
                }         
            })
          })
      })
    }

    function manipularContasBancarias(){
      chamarJanelaEspera("")
      $("#formularioContaBancaria").modal("hide");
      var form = new FormData(document.getElementById("formularioContaBancariaForm"));
      enviarComPost(form);
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          var resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));    
          }else{
            mensagensRespostas("#mensagemCerta", "A transação foi concluída com sucesso.");
            listaContaBancaria = JSON.parse(resultado)
            fazerPesquisa()
          }
        }
      }
    }

    function fazerPesquisa(){
      
      var html="";
      var i=0;
      listaContaBancaria.forEach(function(dado){
        i++
        html +="<tr><td class='lead text-center'>"+completarNumero(i)
        +"</td><td class='lead'>"+dado.descricaoConta
        +"</td><td class='lead text-center'>"+dado.bancoConta
        +"</td><td class='lead text-center'>"+dado.categoriaTipoConta
        +"</td><td class='lead text-center'>"+dado.hierarquia
        +"</td><td class='lead text-center'><strong>"+converterNumerosTresEmTres(Number(dado.C)-Number(dado.D))
        +"</strong></td><td class='lead text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success alterar' title='Editar' href='#as'"+
        " action='editarContaBancaria' idPContaFinanceira='"+dado.idPContaFinanceira
        +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger alterar' "+
        "title='Excluir' href='#a' action='excluirContaBancaria' idPContaFinanceira='"+dado.idPContaFinanceira
        +"'><i class='fa fa-times'></i></a></div></td></tr>";
      })
      $("#tabDados").html(html)
    }