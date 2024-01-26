window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaRelatEEstatistica/escolas/";
  
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)')

    $("#categoriaEscola").change(function(){
      fazerPesquisa();
    })

}

function fazerPesquisa(){

    var tbody = "";
    var i=0;

    $("#numTEscolas").text(completarNumero(listaEscolas.filter(condition).length));
    listaEscolas.filter(condition).forEach(function(dado){
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

      tbody +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead'>"+dado.nomeEscola
      +"</td><td class='lead'>"+nivelEscola+"</td>"+
      "<td class='lead'>"+periodosEscolas
      +"</td><td class='lead'>"+vazioNull(dado.nomeMunicipio)
      +"</td><td class='lead'>"+vazioNull(dado.nomeComuna)
      +"</td></tr>";    
    })
    $("#tabEscola").html(tbody);
  }

  function condition(elem, ind, arr){
    return ($("#categoriaEscola").val()=="" || elem.nivelEscola==$("#categoriaEscola").val())
  }

