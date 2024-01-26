window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaRelatEEstatistica/escolas/";

    $("#idPEscola").val(idPEscola)
    $("#idPEscola").change(function(){
      window.location='?idPEscola='+$(this).val()
    })

    $(".visualizadorLista").click(function(){
      window.location =caminhoRecuar+'relatoriosPdf/mapasFuncionarios/'+
      $(this).attr("id")+'.php?tamanhoFolha='
      +$("#tamanhoFolha").val();
    })

    $("#tipoDisciplinaProfessor").change(function(){
      if($(this).val()!=""){
        window.location =caminhoRecuar+'relatoriosPdf/mapasFuncionarios/'+
        'professoresDeTipoDisciplina.php?tamanhoFolha='
        +$("#tamanhoFolha").val()+"&tipoDisciplina="+$(this).val();
      }
    })
    $("#periodoProfessor").change(function(){
      if($(this).val()!=""){
        window.location =caminhoRecuar+'relatoriosPdf/mapasFuncionarios/'+
        'mapaForcaTrabalho.php?tamanhoFolha='
        +$("#tamanhoFolha").val()+"&periodoProfessor="+$(this).val();
      }
    })
  
    fazerPesquisa();

    DataTables("#example1", "sim", [100, 200, 300, 400, 1000]);

    $("#categoriaEscola").change(function(){
      fazerPesquisa();
    })

}

function fazerPesquisa(){

    var tbody = "";
    var i=0;
    var totSexoF = 0;

    $("#numTEscolas").text(completarNumero(listaAgentes.length));
    listaAgentes.forEach(function(dado){
      i++
      if(dado.generoEntidade=="F"){
        totSexoF++
      }
      var nivelEscola = dado.nivelEscola
      if(nivelEscola=="primaria"){
        nivelEscola="Primário"
      }else if(nivelEscola=="basica"){
        nivelEscola="I Ciclo"
      }else if(nivelEscola=="media"){
        nivelEscola="II Ciclo"
      }else if(nivelEscola=="primBasico"){
        nivelEscola="Complexo (Primária e I Ciclo)"
      }else if(nivelEscola=="basicoMedio"){
        nivelEscola="Complexo (I e II Ciclo)"
      }else if(nivelEscola=="complexo"){
        nivelEscola="Complexo (Primária, I e II Ciclo)"
      }

      var periodosEscolas = dado.periodosEscolas
      if(periodosEscolas=="reg"){
        periodosEscolas="Regular"
      }else{
        periodosEscolas="Regular e Pós-Laboral"
      }

      tbody +="<tr><td class='text-center'>"+completarNumero(i)+"</td><td class='toolTipeImagem' imagem='"+dado.fotoEntidade+"'>"+dado.nomeEntidade
      +"</td><td class='text-center'>"+vazioNull(dado.numeroAgenteEntidade)+"</td>"+
      "<td class=''>"+vazioNull(dado.categoriaEntidade)
      +"</td><td class=''>"+retornDataExtensa(dado.dataInicioFuncoesEntidade)
      +"</td><td class=''>"+vazioNull(dado.funcaoEnt)
      +"</td></tr>";    
    })
    $("#sexoFeminino").text(completarNumero(totSexoF))
    $("#tabEscola").html(tbody);
  }

