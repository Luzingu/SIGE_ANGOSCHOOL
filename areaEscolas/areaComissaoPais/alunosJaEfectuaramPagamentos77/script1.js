  var idPMatricula="";
  var classe = 0;
  var tipoPagamento ="declaracao";
  var mesPagamento =1;
  var nomeDocumento =0;
  var idAnoMatricula=0;
  var idPCurso="";
  window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      entidade ="alunos";
      directorio = "areaComissaoPais/dividaMensalidades/";

      $("#mesAtraso").val(mes);
      $("#anosLectivos").val(idPAno)
      $("#idPNomeCurso").val(idPNomeCurso)

      fazerPesquisa();
      DataTables("#example1", "sim") 

      $("#mesAtraso, #anosLectivos, #idPNomeCurso").change(function(){ 
        window.location ="?idPAno="+$("#anosLectivos").val()
        +"&idPNomeCurso="+$("#idPNomeCurso").val()+"&mes="+$("#mesAtraso").val();
      });

  }
   function  fazerPesquisa(){
      var html ="";

      $("#totContas").text(completarNumero(listaAlunos.length));
      if(jaTemPaginacao==false){
        paginacao.baraPaginacao(listaAlunos.length, 100);
      }else{
          jaTemPaginacao=false;
      }
      var i=0;

      listaAlunos.forEach(function(dado){
          i++;
         html += "<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
         +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+
         "areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
            +"' class='lead black'>"+dado.numeroInterno
          +"</a></td><td class='text-center lead'>"+
          dado.reconfirmacoes.classeReconfirmacao+"</td><td class='text-center lead'>"+
          dado.reconfirmacoes.designacaoTurma+"</td></tr>";      
      })
      $("#example1 tbody").html(html)
   }