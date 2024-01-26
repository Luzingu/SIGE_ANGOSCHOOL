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
          +dado.fotoEntidade+"'>"+dado.nomeEntidade
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