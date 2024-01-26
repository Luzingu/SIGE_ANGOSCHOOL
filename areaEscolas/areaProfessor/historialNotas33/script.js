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
            +dado.fotoEntidade+"'>"+vazioNull(dado.nomeEntidade)
            +"</td><td class='lead'>"+converterData(dado.alteracoes_notas.dataAlteracao)+" - "+dado.alteracoes_notas.horaAlteracao
            +"</td>";

            //$("#curso option:selected").attr("tipoCurso")=="geral"
            if($("#trimestre").val()=="IV"){
                html += "<td class='lead'>"+vazioNull(dado.alteracoes_notas.nota2I)+"</td><td class='lead'><strong>"+vazioNull(dado.alteracoes_notas.nota2F)
              +"</strong></td>";
            }else{
              html += "<td class='lead'>"+vazioNull(dado.alteracoes_notas.nota1I)+"</td><td class='lead'><strong>"+vazioNull(dado.alteracoes_notas.nota1F)
            +"</strong></td><td class='lead'>"+vazioNull(dado.alteracoes_notas.nota2I)+"</td><td class='lead'><strong>"+vazioNull(dado.alteracoes_notas.nota2F)
            +"</strong></td><td class='lead'>"+vazioNull(dado.alteracoes_notas.nota3I)+"</td><td class='lead'><strong>"+vazioNull(dado.alteracoes_notas.nota3F)+"</strong></td>";
            }
                   
          html +="</tr>";

      });
      $("#histNotas").html(html);
  }

  function condicao(elem, ind, obj){
    return elem.idHistDisciplina == idPNomeDisciplina
  }