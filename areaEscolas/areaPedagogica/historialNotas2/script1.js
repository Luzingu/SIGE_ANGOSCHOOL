  var idPProfessor ="";
  var nomeProfessor ="";
  var valorMaximo = 10;
  var posicaoArray ="";

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      entidade="alunos"
      directorio = "areaPedagogica/historialNotas/"
      $("#luzingu").val(luzingu)
      $("#idPNomeDisciplina").val(idPNomeDisciplina)

      fazerPesquisa()
      $("#luzingu, #idPNomeDisciplina").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()
          +"&idPNomeDisciplina="+$("#idPNomeDisciplina").val()+"&trimestre="+trimestre
      })
  }

  function fazerPesquisa(){
      var html="";

      alteracoes_notas.filter(fazerPesquisaCondition).forEach(function(dado){

          var classe="black lead";
          if(idUsuarioLogado!=dado.idPEntidade){
            classe="text-danger lead";
          }
          html +="<tr><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno+"</td><td class='lead toolTipeImagem' imagem='"
          +dado.fotoEntidade+"'>"+dado.alteracoes_notas.nomeAlterador
          +"</td><td class='lead'>"+converterData(dado.alteracoes_notas.dataAlteracao)+" - "+dado.alteracoes_notas.horaAlteracao
          +"</td><td class='lead'>"+dado.alteracoes_notas.alteracoes +"</td></tr>";

      });
      $("#histNotas").html(html);
  }