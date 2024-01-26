    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/movimentosContabilisticos/";

      fazerPesquisa();
      DataTables("#example1", "sim")

      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })

      $("#btnNova").click(function(){
        limparFormulario("#formulario")
        $("#formulario #action").val("novoMovimento")
        $("#formulario").modal("show")
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
          $("#tabDados tr td a").click(function(){
            if(repet==true){
              $("#formulario #action").val("excluirMovimento")
              $("#formulario #idPDocumento").val($(this).attr("idPDocumento"))
              mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este fornecedor?")
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
            listaMovimentos = JSON.parse(resultado)
            fazerPesquisa()
          }
        }
      }
    }

    function fazerPesquisa(){
      
      var html="";
      var i=0;
      listaMovimentos.forEach(function(dado){
        i++

        var btnCancelar ="";
        if(dado.sePagSalario!="A"){
          btnCancelar ="<div class='btn-group alteracao text-right'><a class='btn btn-danger alterar' "+
        "title='Excluir' href='#a' action='excluirMovimento' idPDocumento='"+dado.idPDocumento
        +"'><i class='fa fa-times'></i></a></div>"
        }
        html +="<tr><td class='lead text-center'>"+completarNumero(i)
        +"</td><td class='lead'>"+dado.dataEmissao+"<br>"+dado.horaEmissao
        +"</td><td class='lead'>"+dado.descricaoMovimento
        +"</td><td class='lead text-center'>"+dado.tipoMovimento +" ("+dado.movimento+")"
        +"</td><td class='lead'>"+dado.descricaoContaLiha+
        "</td><td class='lead'>"+converterNumerosTresEmTres(dado.valorLinha)
        +"</td><td class='lead text-center'>"+btnCancelar+"</td></tr>";
      })
      $("#tabDados").html(html)
    }