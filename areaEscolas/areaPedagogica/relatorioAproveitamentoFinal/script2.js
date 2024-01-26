var idPAval="";
var posicaoArray=0;

  window.onload = function (){

  fecharJanelaEspera();
  seAbrirMenu();
      $("#anosLectivos").val(idPAno);
      $("#curso").val(idCursoP);

      fazerPesquisa();
      DataTables("#example1", "sim")

      directorio = "areaPedagogica/relatorioAproveitamentoFinal/";

      $("#anosLectivos, #curso").change(function(){
          window.location ="?idCurso="+$("#curso").val()+"&idPAno="
          +$("#anosLectivos").val()+"&obs="+obs;
      });
  }

      function fazerPesquisa(){
          $("#numTAlunos").text(0);
          $("#numTMasculinos").text(0);

          $("#numTotalAlunos").text(completarNumero(listaAlunos.length));
                $("#numTMasculinos").text(0);
                
            var tbody = "";
              var contagem=0;
                var numM=0;

            listaAlunos.forEach(function(dado){
                contagem++;
                if(dado.sexoAluno=="F"){
                    numM++;
                }

                $("#numTMasculinos").text(completarNumero(numM));
                 
                tbody +="<tr><td class=' text-center'>"+
                completarNumero(contagem)+"</td><td class=' toolTipeImagem' imagem='"
                +dado.fotoAluno+"'>"+
                dado.nomeAluno+"</td><td class=' text-center lead'><a href='"+caminhoRecuar
                +"areaSecretaria/relatorioAluno?idPMatricula="+
                dado.idPMatricula+"' class=' black'>"+dado.numeroInterno
                +"</a></td><td class='lead text-center'>"+dado.reconfirmacoes.classeReconfirmacao
                +"</td><td class='lead text-center'>"+dado.reconfirmacoes.designacaoTurma
                +"</td></tr>";
                
                   
            });
          $("#tabListaAlunos").html(tbody);
        }