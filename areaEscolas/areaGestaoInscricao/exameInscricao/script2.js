  var numeroInterno="";
  var posicaoArray=0;
  var idPMatricula="";

  window.onload = function (){

    fecharJanelaEspera()
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaGestaoInscricao/exameInscricao/";

    fazerPesquisa();
      $("#curso").val(idPCurso);

      $("#curso").change(function(){
          window.location ="?idCurso="+$(this).val();
      }); 

      $(".pesquisaAluno").keyup(function(){
        fazerPesquisa();
      })

      var rep=true;
      $("#listaAlunos").bind("click mouseenter", function(){
        rep=true;
        $("#listaAlunos form").submit(function(){
          var id = $(this).attr("id");
          $("#"+id+" button").html('<i class="fa fa-spinner fa-spin"></i>')
          manipularNota(this);
            return false;
        })
      })

      $("#actualizar").click(function(){
          gravarExameInscricao();
      });
  }

  function manipularNota(dados){
   http.onreadystatechange = function(){
      if(http.readyState==4){
        $("form button").html('<i class="fa fa-check"></i>')
        resultado = http.responseText.trim();
        if(resultado.trim().substring(0, 1)=="F"){
           mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
            mensagensRespostas("#mensagemCerta", "A nota foi alterada com sucesso");
          alunos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(dados);
   enviarComPost(form);
}


  function fazerPesquisa(){
      $("#numTAlunos").text(0);
      $("#numTMasculinos").text(0);

      $("#numTAlunos").text(completarNumero(alunos.filter(condition).length));
            $("#numTMasculinos").text(0);

        var tbody = "";
          if(jaTemPaginacao==false){
            paginacao.baraPaginacao(alunos.filter(condition).length, 300);
          }else{
              jaTemPaginacao=false;
          }
          var contagem=-1;
            var numM=0;

        alunos.filter(condition).forEach(function(dado){
            contagem++;
            if(dado.sexoAluno=="F"){
                numM++;
            }
            $("#numTMasculinos").text(completarNumero(numM));

            if(contagem>=paginacao.comeco && contagem<=paginacao.final){ 
              var dadoExibir = dado.codigoAluno
              if(tipoAutenticacao=="nome"){
                dadoExibir=dado.nomeAluno
              }


              var htmlProva="";
              for(var i=1; i<=numeroProvas; i++){
                if(i==1){
                  nota = dado.inscricao.notaExame1;
                }else if(i==2){
                  nota = dado.inscricao.notaExame2;
                }else if(i==3){
                  nota = dado.inscricao.notaExame3;
                }
                htmlProva +="<div class='col-md-2 col-sm-4 col-xs-4 text-center'><label for='nota"+i+"'>"+nomeProvas[i]
                +"</label><input type='number' min='0' max='20' step='0.01'"
                +" name='nota"+i+"' id='nota"+i+"' required value='"+nota
                +"' class='form-control lead text-center inputVal'></div>"
              }
              tbody +="<form method='post' class='col-lg-12' id='"+dado.inscricao.idPInscrito
              +"' style='border-bottom:solid rgba(0,0,0,0.3) 3px; padding-bottom:20px;'>"+
              
              "<div class='col-md-2 col-sm-6 col-xs-6 text-center lead' style='font-size:12pt;'><br/><i><del>"+nomeDaEntidade(dado.inscricao.idExameLancProfessor)
                +"</del></i></div>"+
              "<div class='col-md-4 col-sm-6 col-xs-6 lead text-primary' style='font-size:16pt; padding:0px;'><br/><strong>"+dadoExibir
                +"</strong></div>"

              +htmlProva+"<div class='col-md-1 col-sm-4 col-xs-4 text-center'><strong>M. F.</strong>"+
              "<input type='text' disabled step='0.01'"
              +" name='mediaFinal' value='"+vazioNull(dado.inscricao.mediaExames)
              +"' class='form-control lead text-center inputVal' style='padding:0px;'></div><div class='col-md-1 col-sm-4'><br/>"
              +"<button type='submit' name='' class='form-control lead btn-primary'><i class='fa fa-check'></i></button>" 
              +"<input type='hidden' name='action' value='alterarNotaExame'>"+
              "<input type='hidden' name='numeroProvas' value='"+numeroProvas+"'>"+
              "<input type='hidden' name='idPInscrito' value='"+
              dado.inscricao.idPInscrito+"'><input type='hidden' name='idPAluno' value='"+
              dado.idPAluno+"'><input type='hidden' name='idPCurso' value='"+idPCurso+"'></div></form>";
            }                   
        });
        $("#listaAlunos").html(tbody)
        corCelula();
    }

  function condition(elem, ind, obj){
    if(tipoAutenticacao=="nome"){
      return (elem.nomeAluno.toLowerCase().indexOf($(".pesquisaAluno").val().toLowerCase().trim())>=0);
    }else{
      return (elem.codigoAluno.toLowerCase().indexOf($(".pesquisaAluno").val().toLowerCase().trim())>=0);
    }
  }

  function corCelula(){
      $("#listaAlunos form .inputVal").each(function(i){
          if($(this).val()<10 && $(this).val().trim()!=""){
              $(this).css("color", "red");
          }else{
            $(this).css("color", "darkblue");
          }
      });
  }