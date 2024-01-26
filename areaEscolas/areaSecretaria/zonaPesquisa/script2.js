  var classe="T";
  var turma = "A";
  var idAnoLectivo="";

  var campoPesquisado="idade";
  var operador="=";
  var valorPesquisado="";

  var listaAlunos = new Array();

  window.onload=function(){
  
  fecharJanelaEspera();
  seAbrirMenu();
    directorio = "areaSecretaria/zonaPesquisa/";

    $("#operador option").hide();

    $("#operador .paraIdade").show();
    $("#campoPesquisado").val("idade");
    $("#operador").val("=");

    listarClasses("", "", "#curso", "#classe")

    $(".abrirRelatorio").click(function(){
      window.location =caminhoRecuar+"relatoriosPdf/mapasEstaticosDosAlunosReconfirmados/"
      +$(this).attr("caminho")+"?idPAno="+
      $("#anoLectino").val()+"&idPCurso="+$("#curso").val(); 
    })

    $("#campoPesquisado").change(function(){
      campoPesquisado = $(this).val();

      var classeDaPesq = $("#campoPesquisado option:selected").attr("title");
      $("#operador option").hide();
      $("."+classeDaPesq).show();

      operador = $("."+classeDaPesq+":nth-child(1)").val();

       if(classeDaPesq=="paraGenero" || classeDaPesq=="paraDataDeNascimento" || classeDaPesq=="paraIdade"
        || classeDaPesq=="paraNumero"){
          operador = $("."+classeDaPesq).val();
       }
       $("#operador").val(operador);
    })

     $("#formularioPesquisa").submit(function (){
      classe = $("#classe").val();
      turma = $("#turma").val();
      idAnoLectivo =$("#anoLectino").val();

      operador = $("#operador option:selected").val();     
      valorPesquisado = $("#valorPesquisado").val();

      if(campoPesquisado=="sexoAluno"){
          valorPesquisado = valorPesquisado.substring(0,1);
      }
      pesquisaDb();
        return false;
    });
  }

  function fazerPesquisa(){
    var html ="";
    var numM=0;

    $("#numTotAlunos").text(completarNumero(listaAlunos.length));
    $("#numTotAlunosM").text(0);

    var contagem=0;
    listaAlunos.forEach(function(dado){
      contagem++;
      if(dado.sexoAluno=="F"){
          numM++;
        }
        $("#numTotAlunosM").text(completarNumero(numM));

        var campoAutarca="";
        if(campoPesquisado=="nomeAluno"){
            campoAutarca = dado.nomeAluno;
            $("#celulaPesquisada").text("Nome do Aluno");
        }else if(campoPesquisado=="paiAluno"){
            campoAutarca = dado.paiAluno;
            if(dado.paiAluno==null){
              campoAutarca=""
            }
            $("#celulaPesquisada").text("Nome do Pai");
        }else if(campoPesquisado=="maeAluno"){
            campoAutarca = dado.maeAluno;
            if(campoAutarca==null){
              campoAutarca="";
            }
            $("#celulaPesquisada").text("Nome da Mãe");
        }else if(campoPesquisado=="sexoAluno"){
            $("#celulaPesquisada").text("Género");
            campoAutarca = dado.sexoAluno;
            if(campoAutarca=="M"){
                campoAutarca="Masculino";
            }else{
                campoAutarca="Feminino";
            }
        }else if(campoPesquisado=="idade"){
            $("#celulaPesquisada").text("Idade");
            campoAutarca = calcularIdade(dado.dataNascAluno)+" Anos";
        }else if(campoPesquisado=="dataNascAluno"){
            campoAutarca = converterData(dado.dataNascAluno);
            $("#celulaPesquisada").text("Data de Nascimento");
        }else if(campoPesquisado=="municNascAluno"){
            campoAutarca = dado.municNascAluno;
            $("#celulaPesquisada").text("Municipio de Nascimento");
        }else if(campoPesquisado=="provNascAluno"){
            campoAutarca = dado.provNascAluno;
            $("#celulaPesquisada").text("Província de Nascimento");
        }else if(campoPesquisado=="biAluno"){
            campoAutarca = dado.biAluno;
            $("#celulaPesquisada").text("Número de BI");
        }else if(campoPesquisado=="numeroInterno"){
            $("#celulaPesquisada").text("Número Interno");
             campoAutarca = dado.numeroInterno;
        }

        html +="<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+
          dado.fotoAluno+"'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.nomeAluno+"</a></td><td class='lead'>"+campoAutarca
          +"</td><td class='lead'>"+dado.sexoAluno+"</td><td>"+vazioNull(dado.abrevCurso)+" - "+dado.reconfirmacoes.classeReconfirmacao+"</td></tr>";
        
      });
      $("#listaAlunosBody").html(html)
  }


  function pesquisaDb(){
    chamarJanelaEspera("")
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera()
        listaAlunos = JSON.parse(http.responseText.trim())
        fazerPesquisa()
      }     
    }
    enviarComGet("tipoAcesso=fazerPesquisaAlunos&idPCurso="
        +$("#curso").val()+"&classe="+$("#classe").val()+"&turma="+turma+"&idAnoLectivo="+idAnoLectivo+"&campoPesquisado="+campoPesquisado+
        "&operador="+operador+"&valorPesquisado="+valorPesquisado);
  }





