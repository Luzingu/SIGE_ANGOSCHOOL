    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/fornecedores/";

      fazerPesquisa();
      DataTables("#example1", "sim")

      $("#btnNova").click(function(){
        limparFormulario("#formulario")
        $("#formulario #action").val("novoFornecedor")
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

              $("#formulario #action").val($(this).attr("action"))
              idPFornecedor = $(this).attr("idPFornecedor")
              $("#formulario #idPFornecedor").val(idPFornecedor)
              if($(this).attr("action")=="editarFornecedor"){

                listaFornecedores.forEach(function(dado){
                  if(dado.fornecedores.idPFornecedor==idPFornecedor){
                    $("#formulario #NIF").val(dado.fornecedores.NIF)
                    $("#formulario #nomeEmpresa").val(dado.fornecedores.nomeEmpresa)
                    $("#formulario #enderecoEmpresa").val(dado.fornecedores.enderecoEmpresa)
                    $("#formulario #cidadeEmpresa").val(dado.fornecedores.cidadeEmpresa)
                    $("#formulario").modal("show")
                  }
                })
              }else{
                mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este fornecedor?")
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
            listaFornecedores = JSON.parse(resultado)
            fazerPesquisa()
          }
        }
      }
    }

    function fazerPesquisa(){
      
      var html="";
      var i=0;
      listaFornecedores.forEach(function(dado){
        i++
        html +="<tr><td class='lead text-center'>"+completarNumero(i)
        +"</td><td class='lead'>"+dado.fornecedores.NIF
        +"</td><td class='lead'>"+dado.fornecedores.nomeEmpresa
        +"</td><td class='lead'>"+dado.fornecedores.enderecoEmpresa
        +"</td><td class='lead text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success alterar' title='Editar' href='#as'"+
        " action='editarFornecedor' idPFornecedor='"+dado.fornecedores.idPFornecedor
        +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger alterar' "+
        "title='Excluir' href='#a' action='excluirFornecedor' idPFornecedor='"+dado.fornecedores.idPFornecedor
        +"'><i class='fa fa-times'></i></a></div></td></tr>";
      })
      $("#tabDados").html(html)
    }