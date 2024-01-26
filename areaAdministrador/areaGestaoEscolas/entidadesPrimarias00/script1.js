window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="professores";
    directorio = "areaGestaoEscolas/entidadesPrimarias00/";

    selectProvincias("#formularioEntidade #pais", "#formularioEntidade #provincia", "#municipio", "provincia", "municipio");

    selectProvincias("#formularioEntidade #pais", "#formularioEntidade #provincia", "#formularioEntidade #municipio",
     "#formularioEntidade #comuna", "provincia",
     'municipio', 'comuna')

    accoes("#tabProfessores", "#formularioEntidade", "Entidade", "Tens certeza que pretendes eliminar esta entidade?");
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioEntidade form").submit(function(){
      if(estadoExecucao=="ja"){
        if(validarFormularios("#formularioEntidade form")==true){
          estadoExecucao="espera";
          idEspera = "#formularioEntidade form #Cadastar";
          manipularEntidade();
        }
      }
        return false;
    });

     var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
               if(estadoExecucao=="ja"){
                estadoExecucao="espera";
                idEspera ="#janelaPergunta #pergSim";
                manipularEntidade();
              }

            rep=false;
          }
      })
    })
}

function porValoresNoFormulario(){
 listaEntidades.forEach(function(dado){
      if(dado.idPEntidade==idPrincipal){
        $("#formularioEntidade #nomeEntidade").val(dado.nomeEntidade);
        $("#dataNascEntidade").val(dado.dataNascEntidade);
        $("#numeroAgente").val(dado.numeroAgenteEntidade)
        $("#estadoAcesso").val(dado.estadoAcessoEntidade)
        $("#sexoEntidade").val(dado.generoEntidade)
        $("#tituloNomeEntidade").val(dado.tituloNomeEntidade)
        $("#formularioEntidade #ninjaF5").prop("checked", false)
        if(dado.ninjaF5 == "A")
          $("#formularioEntidade #ninjaF5").prop("checked", true)
      }
  });
}

function manipularEntidade(){

    $(".modal").modal("hide");
    chamarJanelaEspera("")
   var form = new FormData(document.getElementById("formularioEntidadeForm"));
   enviarComPost(form);

   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        fecharJanelaEspera()
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          listaEntidades = JSON.parse(resultado);
           mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
          fazerPesquisa();
        }
      }
    }
}

function fazerPesquisa(){

    var tbody = "";
      var contagem=0;
      $("#numTProfessores").text(completarNumero(listaEntidades.length))
      var numTMasculinos=0;
    listaEntidades.forEach(function(dado){
      contagem++;

      if(dado.generoEntidade=="M"){
          numTMasculinos++;
      }
      $("#numTMasculinos").text(completarNumero(numTMasculinos));

      tbody +="<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoEntidade+"'>"+dado.nomeEntidade
      +"</td><td class='lead text-center'>"+dado.generoEntidade
      +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaGestaoEscolas/perfilEntidade00?aWRQUHJvZmVzc29y="+dado.idPEntidade+"' class='black'>"+dado.numeroInternoEntidade
      +"</a></td><td class='lead text-center'>"+vazioNull(dado.estadoAcessoEntidade)+"</td><td class='lead text-center'>"+vazioNull(dado.ninjaF5)+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#' action='editarEntidade' idPrincipal='"+dado.idPEntidade+"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#' action='excluirEntidade' posicaoNoArray='"+dado.chave+"'  idPrincipal='"
      +dado.idPEntidade+"'><i class='fa fa-times'></i></a></div></td></tr>";

    });
    $("#tabProfessores").html(tbody);
}
