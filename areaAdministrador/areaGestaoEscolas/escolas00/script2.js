window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaGestaoEscolas/escolas00/";

    selectProvincias("#formularioEscola #pais", "#formularioEscola #provincia", "#formularioEscola #municipio",
     "#formularioEscola #comuna")

    accoes("#tabEscola", "#formularioEscola", "Escola", "Tens certeza que pretendes eliminar esta escola?");

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioEscola form").submit(function(){
      if(estadoExecucao=="ja"){
        manipularEscola();
      }
        return false;
    })

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
              if(estadoExecucao=="ja"){
                estadoExecucao="espera";
                fecharJanelaToastPergunta();
                manipularEscola();
              }

            rep=false;
          }
      })
    })
}

function porValoresNoFormulario(){
 listaEscolas.forEach(function(dado){
      if(dado.idPEscola==idPrincipal){
         $("#formularioEscolaForm #nomeEscola").val(dado.nomeEscola);
          $("#formularioEscolaForm #estadoEscola").val(dado.estadoEscola);
          $("#privacidade").val(dado.privacidadeEscola)
          $("#tipoInstituicao").val(dado.tipoInstituicao)
          $("#periodosEscolas").val(dado.periodosEscolas);
          $("#abrevNomeEscola").val(dado.abrevNomeEscola);
          $("#abrevNomeEscola2").val(dado.abrevNomeEscola2);
          $("#criterioEscolhaTurno").val(dado.criterioEscolhaTurno);
          passarValoresDaProvincia("#pais", "#provincia",
          "#municipio", "#comuna", dado.pais,dado.provincia, dado.municipio, dado.comuna);
      }
  });
}

function manipularEscola(){
    var form = new FormData(document.getElementById("formularioEscolaForm"));
    $(".modal").modal("hide");
    chamarJanelaEspera("")
   enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        fecharJanelaEspera();
       if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção terminada com sucesso.");
          listaEscolas = JSON.parse(resultado)
          fazerPesquisa();
        }

      }
    }
}

function fazerPesquisa(){

    var tbody = "";
    var i=0;
    var contagem=-1;
    var numEscolaA=0;

    $("#numTEscolas").text(completarNumero(listaEscolas.length));

    listaEscolas.forEach(function(dado){

      contagem++;
      if(dado.estadoEscola=="A"){
          numEscolaA++;
      }
      $("#numTActivo").text(completarNumero(numEscolaA));
          i++;
          var tipoInstituicao=dado.tipoInstituicao;

          tbody +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead'>"+dado.nomeEscola+" ["+vazioNull(dado.idPEscola)+"]</td>"+
          "<td class='lead text-center'>"+vazioNull(dado.periodosEscolas)
          +"</td><td class='lead text-center'>"+dado.estadoEscola
          +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#' action='editarEscola'"+
          " idPrincipal='"+dado.idPEscola+"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#' action='excluirEscola'  idPrincipal='"
          +dado.idPEscola+"'><i class='fa fa-times'></i></a></div></td></tr>";

    });
    $("#tabEscola").html(tbody);
}
