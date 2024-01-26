var dataSeleccao="";
var idPCursoFormulario =0;
var idMatAno=0;
window.onload=function(){
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaGestaoInscricao/inscricaoNovaInscricao/";

    $("#idPCurso").val(idPCurso)
    selectProvincias("#formularioCadastro #pais", "#formularioCadastro #provincia", "#formularioCadastro #municipio",
     "#formularioCadastro #comuna")

    accoes("#tabela", "#formularioCadastro", "Cadastro", "Tens certeza que pretendes eliminar esta inscrição?");
    
    $("#dataNascAluno, #sexoAluno").bind("change, keyup", function(){
      seleccaoPeriodoDoAluno();
    })

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#idPCurso").change(function(){
      window.location ="?idPCurso="+$(this).val();
    })
    $("#formularioCadastro form").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioCadastro form")==true){  
        manipular();
      }
      return false;
    })
    $("#formularioInscTrocCursoForm").submit(function(){
      trocarCurso()
      return false
    })

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              idEspera = "#janelaPergunta #pergSim";
              estadoExecucao="espera";
              fecharJanelaToastPergunta();
              manipular();
            }
            rep=false;
          }         
      })
    }) 
    var repet1=true;
    $("#tabela").bind("click mouseenter", function(){
      repet1=true
      $("#tabela tr td a[id=trocarCurso]").click(function(){
        if(repet1==true){
          $("#formularioInscTrocCurso #nomeAluno").text($(this).attr("nomeAluno"))
          $("#formularioInscTrocCurso #idNovoCurso").val(idPCurso)
          $("#formularioInscTrocCurso #idPAluno").val($(this).attr("idPAluno"))

          $("#formularioInscTrocCurso").modal("show")
          repet1=false;
        }
      })
    })   
}

function fazerPesquisa(){

  $("#numTAlunos").text(0);
  $("#numTMasculinos").text(0);

  $("#numTAlunos").text(completarNumero(alunosInscritos.length));
  $("#numTMasculinos").text(0);

    var tbody = "";

      var contagem=0;
      var numM=0;
    alunosInscritos.forEach(function(dado){
        contagem++;
        if(dado.sexoAluno=="F"){
            numM++;
        }
        $("#numTMasculinos").text(completarNumero(numM));
        var estado = dado.inscricao.estadoInscricao;
        if(estado=="A"){
          estado="Abilitado";
        }else{
          estado="Desabilitado";
        }
        tbody +="<tr><td class='lead text-center'>"
        +completarNumero(contagem)+"</td><td class='lead'><a class='black' href='"+caminhoRecuar+"areaGestaoInscricao/relatorioAluno?idPAluno="
          +dado.idPAluno+"'>"+dado.nomeAluno
        +"</a></td><td class='lead text-center'>"+vazioNull(dado.sexoAluno)
        +"</td><td class='lead text-center'>"+converterData(dado.dataNascAluno)
        +"</td><td class='lead text-center'>"+vazioNull(dado.biAluno)
        +"</td><td class='lead text-center'>"+vazioNull(dado.telefoneAluno)
        +"</td><td class='lead text-center'><a href='#' title='Trocar o curso' id='trocarCurso' idPAluno='"+
        dado.idPAluno+"' nomeAluno='"+dado.nomeAluno+"'>"+
      "<i class='fa fa-sign-out-alt'></i></a></td><td class='lead text-center'><a href='../../relatoriosPdf/relatoriosI"+
        "nscricao/reciboInscricao.php?idPAluno="+dado.idPAluno+"'>"+
      "<i class='fa fa-print'></i></a></td><td class='text-center'>"+
        "<div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarCadastro'"+
        " idPrincipal='"+dado.idPAluno
        +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirCadastro'  idPrincipal='"
        +dado.idPAluno+"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}




function porValoresNoFormulario(){
 alunosInscritos.forEach(function(dado){

      if(dado.idPAluno==idPrincipal){
        $("#formularioCadastro #idPAluno").val(dado.idPAluno);
        $("#formularioCadastro #nomeAluno").val(dado.nomeAluno);
        $("#formularioCadastro #sexoAluno").val(dado.sexoAluno);
        $("#formularioCadastro #dataNascAluno").val(dado.dataNascAluno);
        
        $("#formularioCadastro #numBI").val(dado.biAluno);
        $("#formularioCadastro #dataEmissaoBI").val(dado.dataEBIAluno);
        $("#formularioCadastro #nomePai").val(dado.paiAluno);
        $("#formularioCadastro #nomeMae").val(dado.maeAluno);
        $("#formularioCadastro #numTelefone").val(dado.telefoneAluno);

        $("#formularioCadastro #emailAluno").val(dado.emailAluno)
        $("#formularioCadastro #estadoInscricao").val(dado.inscricao.estadoInscricao);

        $("#formularioCadastro #mediaDiscNuclear").val(dado.inscricao.mediaDiscNuclear);

        seleccaoPeriodoDoAluno();
        $("#formularioCadastro #periodoInscricao").val(dado.inscricao.periodoInscricao);
        passarValoresDaProvincia("#formularioCadastro #pais", "#formularioCadastro #provincia", 
      "#formularioCadastro #municipio", "#formularioCadastro #comuna"
      , dado.paisNascAluno,dado.provNascAluno, dado.municNascAluno, dado.comunaNascAluno);
      }
  });
}

function seleccaoPeriodoDoAluno(){
  $("#formularioCadastro #periodoInscricao").empty();
  if(criterioEscolhaPeriodo=="auto"){
    $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "auto'>Automatico</option>")
  }else if(criterioEscolhaPeriodo=="opcional"){
    
    if(periodosCurso=="regPos"){
      $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "reg'>Regular</option>")
      $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "pos'>Pós-Laboral</option>")
    }else if(periodosCurso=="reg"){
      $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "reg'>Regular</option>")
    }else if(periodosCurso=="pos"){
      $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "pos'>Pós-Laboral</option>")
    }
  }else{
      var anoNascimento = $("#formularioCadastro #dataNascAluno").val().split("-")
      if((ano-anoNascimento[0])>criterioEscolhaPeriodo){
        $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "pos'>Pós-Laboral</option>")
      }else{
        $("#formularioCadastro #periodoInscricao").append("<option value='"+
      "reg'>Regular</option>")
      }
  }
}

function manipular(){
  var form = new FormData(document.getElementById("formularioCadastroF"));
  chamarJanelaEspera("");
  $("#formularioCadastro").modal("hide");
   enviarComPost(form);

   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        resultado = http.responseText.trim()
        estadoExecucao="ja";        
        if(resultado.trim().substring(0, 1)=="F"){
            $("#formularioCadastro").modal("show");
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          alunosInscritos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
    }
}

function trocarCurso(){
  var form = new FormData(document.getElementById("formularioInscTrocCursoForm"));
  $("#formularioInscTrocCurso").modal("hide");
  chamarJanelaEspera("");
  enviarComPost(form);

   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        resultado = http.responseText.trim();
        estadoExecucao="ja";        
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          alunosInscritos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
    }
}



