    var idPProfessor ="";
  var nomeProfessor ="";
  var valorMaximo = 10;
  var posicaoArray ="";

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu(); 
      entidade="alunos"
      directorio = "areaProfessor/historialNotas/"
      $("#luzingu").val(luzingu)
      fazerPesquisa()
      $("#luzingu").change(function(){
        window.location = "?luzingu="+$(this).val()
      });  
  }

  function change(){
    idPCurso = $("#referenciaDisciplina option:selected").attr("idPCurso");
    classe = $("#referenciaDisciplina option:selected").attr("classe");
    turma = $("#referenciaDisciplina option:selected").attr("turma");
    idPDisciplina = $("#referenciaDisciplina option:selected").attr("idPNomeDisciplina");

    fazerPesquisa()
  }
  function fazerPesquisa(){
      var html="";

      alteracoes_notas.filter(fazerPesquisaCondition).forEach(function(dado){

            var classe="black lead";
            if(idUsuarioLogado!=dado.idPEntidade){
              classe="text-danger lead";
            }
            html +="<tr><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno+"</td><td class='lead toolTipeImagem' imagem='"
            +dado.fotoEntidade+"'>"+vazioNull(dado.alteracoes_notas.nomeAlterador)
            +"</td><td class='lead'>"+converterData(dado.alteracoes_notas.dataAlteracao)+" - "+dado.alteracoes_notas.horaAlteracao
            +"</td><td class='lead'>"+dado.alteracoes_notas.alteracoes
            +"</td></tr>";

      });
      $("#histNotas").html(html);
  }

  function condicao(elem, ind, obj){
    return elem.idHistDisciplina == idPNomeDisciplina
  }