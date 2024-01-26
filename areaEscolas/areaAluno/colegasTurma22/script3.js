  window.onload = function(){ 
  
    fecharJanelaEspera();
    seAbrirMenu();

    $(".visualizadorRelatorio").click(function(){
      window.location =caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho");
    })
    fazerPesquisa()
    DataTables("#example1", "sim") 
  }  
  function fazerPesquisa(){
    var tbody =""
    var i=0;
    listaAlunos.forEach(function(dado){
      i++
      tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
      +"</td><td class='lead text-center'>"+sexoExtensa(dado.sexoAluno)+"</td><td class='lead text-center'>"+vazioNull(dado.telefoneAluno)
      +"</td><td class='lead'>"+vazioNull(dado.emailAluno)
      +"</td></tr>";
        
    });
    $("#listaAlunos").html(tbody);
  }