 window.onload=function(){
      
      fecharJanelaEspera();
      $('#acercaEscola').summernote()
      seAbrirMenu();
      porValoresNoFormulario();

      selectProvincias("#pais", "#provincia", "#municipio", "provincia", "municipio");
      directorio = "areaGestaoGPE/definicoesConta1/";
      $("#formularioPerfil").submit(function(){
        actualizarPerfil();
        return false;
      })

    }
    
  function porValoresNoFormulario(){

    dadosescola.forEach(function(dado){
        $("#thNomeEscola").text(dado.nomeEscola);
        $("#thNumeroInternoEscola").text(dado.numeroInternoEscola);
        $("#thLocalizacaoEscola").text(vazioNull(dado.nomeProvincia)+" - "+vazioNull(dado.nomeMunicipio));
        $("#thTelEscola").text(dado.numeroTelefone);
        $("#textNomeEscola").text(dado.nomeEscola);
        $("#textNúmeroInterno").text(dado.numeroInternoEscola);
        $("#textPrivacidade").text(dado.privacidadeEscola);
        $("#textAnoFundado").text(dado.anoFundacao);
         $("#thEmail").text(dado.email)

        $("#nomeEscola").val(dado.nomeEscola);
        $("#tituloEscola").val(dado.tituloEscola);

        $("#numInternoEscola").val(dado.numeroInternoEscola)

        $("#numeroTelefone").val(dado.numeroTelefone) 
        $("#valEmail").val(dado.email);
        $("#decretoCriacaoInstituicao").val(dado.decretoCriacaoInstituicao)
        $("#codOrganismo").val(dado.codOrganismo)
        $("#corCabecalhoTabelas").val(dado.corCabecalhoTabelas)
        $("#corLetrasCabecalhoTabelas").val(dado.corLetrasCabecalhoTabelas)
        
        $("#logotipoEscola").attr("src", caminhoRecuar+"../Ficheiros/Escola_"+dado.idPEscola+"/Icones/"+dado.logoEscola);
        
    })

  }

  function actualizarPerfil(){
    chamarJanelaEspera("Actualizando...");
      http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        resultado = http.responseText.trim()
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

