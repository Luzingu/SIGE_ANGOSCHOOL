window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaRelatEEstatistica/escolas/";
  
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

    $("#numTEscolas").text(completarNumero(listaAgentes.filter(condition).length));
    listaAgentes.filter(condition).forEach(function(dado){
      i++
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
      if(dado.generoEntidade=="F"){
        totSexoF++
      }

      tbody +="<tr><td class='text-center'>"+completarNumero(i)+"</td><td class='toolTipeImagem' imagem='"+dado.fotoEntidade+"'>"+dado.nomeEntidade
      +"</td><td class='text-center'>"+vazioNull(dado.numeroAgenteEntidade)+"</td>"+
      "<td class=''>"+vazioNull(dado.categoriaEntidade)
      +"</td><td class=''>"+dado.nomeEscola
      +"</td><td class=''>"+vazioNull(dado.funcaoEnt)
      +"</td></tr>";    
    })
    $("#sexoFeminino").text(completarNumero(totSexoF))
    $("#tabEscola").html(tbody);
  }

  function condition(elem, ind, arr){
    return ($("#categoriaEscola").val()=="" || elem.nivelEscola==$("#categoriaEscola").val()
      || elem.tipoInstituicao==$("#categoriaEscola").val())
  }

