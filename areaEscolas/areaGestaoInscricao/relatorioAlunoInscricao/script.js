$(document).ready(function(){
  fecharJanelaEspera();
  seAbrirMenu();
  
      directorio = "areaGestaoInscricao/relatorioAluno/";
      $("#pesquisarAluno").submit(function(){
        window.location ="?valorPesquisado="+$("#valorPesquisado").val();
        return false;
      });
});   

  


    

