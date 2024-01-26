    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/documentosComercFornecedores/";

      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)
      fazerPesquisa(); 
      DataTables("#example1", "sim")
      
      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })
      


      $("#btnNova").click(function(){
        $("#paraIsencaoIVA input").removeAttr("required")
        $("#paraIsencaoIVA").hide(200)
        limparFormulario("#formulario")
        $("#formulario #action").val("novoDocumento")
        $("#formulario").modal("show")
      })

      $("#IVA, #valorLiquidado, #montanteImpostoRetido").bind("keyup change", function(){
        if(new Number($("#IVA").val())==0){
          $("#paraIsencaoIVA input").attr("required", "")
          $("#paraIsencaoIVA").show(200)
          $("#totalLiquidado").val(new Number($("#montanteImpostoRetido").val())+new Number($("#valorLiquidado").val()))
        }else{
          $("#paraIsencaoIVA input").removeAttr("required")
          $("#paraIsencaoIVA").hide(200)
          $("#totalLiquidado").val(new Number($("#IVA").val())+new Number($("#valorLiquidado").val()))
        }
      })

      $("#formularioForm").submit(function(){
        manipular();
        return false
      })
      $("#formularioTransacaoForm").submit(function(){
        manipularTransacao();
        return false
      })

      var repet=true
      $("#tabDados").bind("mouseenter click", function(){
          repet=true
          $("#tabDados tr td a.alterar").click(function(){
            if(repet==true){
              $("#formulario #action").val("excluirDocumento")
              $("#formulario #idPDocumento").val($(this).attr("idPDocumento"))
              mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este documento?")
              repet=false
            }
          })

          var rep=true;
          $("body").bind("mouseenter click", function(){
                rep=true;
              $("#janelaPergunta #pergSim").click(function(){
                if(rep==true){
                    fecharJanelaToastPergunta()
                    manipular();
                  rep=false;
                }         
            })
          })
      })
    }

    function manipular(){
      chamarJanelaEspera("")
      $("#formulario").modal("hide");
      var form = new FormData(document.getElementById("formularioForm"));
      enviarComPost(form);
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          var resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));    
          }else{
            mensagensRespostas("#mensagemCerta", "A transação foi concluída com sucesso.");
            listaPagamentos = JSON.parse(resultado)
            fazerPesquisa()
          }
        }
      }
    }

    function fazerPesquisa(){
      
      var html="";
      var i=0;
      listaPagamentos.forEach(function(dado){
        i++
        html +="<tr><td class='text-center'>"+completarNumero(i)
        +"</td><td class='text-center'>"+dado.dataEmissao+"<br>"+dado.horaEmissao
        +"</td><td class=''>"+dado.tipoDocumento+"<br>"+ dado.referenciaDocumento
        +"</td><td class=''>"+dado.nomeEmpresa
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.valorLiquidado)
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.IVA)
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.montanteImpostoRetido)
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.totalLiquidado)
        +"</td><td class='text-center'><a href='"+
        caminhoRecuar+"relatoriosPdf/notasDePagamento/notaLiquidacao.php?idPDocumento="+dado.idPDocumento
        +"' title='Nota de Liquidação'><i class='fa fa-print'></i></a></td><td class='text-center'><div class='btn-group text-right'><a class='btn btn-danger alterar' "+
        "title='Excluir' href='#a' action='excluirDocumento' idPDocumento='"+dado.idPDocumento
        +"'><i class='fa fa-times'></i></a></div></td></tr>";
      })
      $("#tabDados").html(html)
    }