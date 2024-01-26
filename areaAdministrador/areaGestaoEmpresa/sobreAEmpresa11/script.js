 window.onload=function(){

      fecharJanelaEspera();
      $('#acercaEscola').summernote()
      seAbrirMenu();
      porValoresNoFormulario();

      selectProvincias("#pais", "#provincia", "#municipio", "provincia", "municipio");


      directorio = "areaGestaoEmpresa/sobreAEmpresa11/";


      $("#formularioPerfil").submit(function(){

        actualizarPerfil();
        return false;
      })

    }

  function porValoresNoFormulario(){

    dadosescola.forEach(function(dado){
        $("#thNomeEscola").text(dado.nomeEscola);
        $("#thNumeroInternoEscola").text(dado.numeroInternoEscola);
        $("#thLocalizacaoEscola").text(vazioNull(dado.provincia)+" - "+vazioNull(dado.municipio));
        $("#thTelEscola").text(dado.numeroTelefone);
        $("#textNomeEscola").text(dado.nomeEscola);
        $("#textNúmeroEscola").text(dado.numeroEscola);
        $("#textNúmeroInterno").text(dado.numeroInternoEscola);
        $("#textPrivacidade").text(dado.privacidadeEscola);
        $("#textAnoFundado").text(dado.anoFundacao);
         $("#thEmail").text(dado.email)

        $("#nomeEscola").val(dado.nomeEscola);
        $("#tituloEscola").val(dado.tituloEscola);
        $("#numInternoEscola").val(dado.numeroInternoEscola)
        $("#pais").val(dado.pais);
        $("#provincia").val(dado.provincia)
        $("#municipio").val(dado.municipio)
        $("#numeroTelefone").val(dado.numeroTelefone)
        $("#valEmail").val(dado.email);
        $("#comunaEscola").val(dado.comuna)
        $("#logotipoEscola").attr("src", caminhoRecuar+"../Ficheiros/Escola_"+dado.idPEscola+"/Icones/"+dado.logoEscola);

    })

  }

  function actualizarPerfil(){
    chamarJanelaEspera("Actualizando...");
      http.onreadystatechange = function(){
      if(http.readyState<4){

      }else{
        fecharJanelaEspera();
        resultado = http.responseText.trim();
        if(resultado.substring(0, 1)=="F"){
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
        }else if(resultado!=""){
          mensagensRespostas('#mensagemCerta', "Os dados foram actualizados com sucesso!");
          dadosescola = JSON.parse(resultado);
          porValoresNoFormulario();
        }
      }
    }
    enviarComPost(new FormData(document.getElementById('formularioPerfil')));
  }
