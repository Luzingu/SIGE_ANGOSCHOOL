  var dadosEnviar = new Array();
  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaPedagogica/gestaoLinguasEOpcoes/";
      passarDados();
      $("#actualizar").click(function(){
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao";
          actualizarDados();
        }        
      })

      $("#btnAlterar").click(function(){

        dadosEnviar = new Array();
        $("#tabTurmas tr").each(function(){
          var id = $(this).attr("id")

          var disciplinasOpcao="";
          var linguasEntrangeira="";
          $("#tabTurmas tr#"+id+" td input[type=checkbox].linguasEstrangeira").each(function(){
            if($(this).prop("checked")==true){
              if(linguasEntrangeira==""){
                linguasEntrangeira=$(this).attr("idDisciplina");
              }else{
                linguasEntrangeira+=","+$(this).attr("idDisciplina");
              }
            }
          })

          $("#tabTurmas tr#"+id+" td input[type=checkbox].disciplinasOpcao").each(function(){
            if($(this).prop("checked")==true){
              if(disciplinasOpcao==""){
                disciplinasOpcao=$(this).attr("idDisciplina");
              }else{
                disciplinasOpcao+=","+$(this).attr("idDisciplina");
              }
            }
          })
          dadosEnviar.push({id:id, disciplinasOpcao:disciplinasOpcao, linguasEntrangeira:linguasEntrangeira})
          alterarDados();
        })
      })
  }  

  function actualizarDados(){
      chamarJanelaEspera("");
      http.onreadystatechange = function(){
        if(http.readyState==4){
            estadoExecucao="ja";
            fecharJanelaEspera();
            resultado = http.responseText.trim();
            if(http.responseText.trim().substring(0, 1)=="F"){
              mensagensRespostas('#mensagemErrada', http.responseText.trim().substring(1, http.responseText.trim().length)); 
            }else{
              window.location='?luzingu=lua kiesse';
            }                
        }
      }
      enviarComGet("tipoAcesso=actualizarDados");
  }

  function alterarDados(){
      chamarJanelaEspera("");
      http.onreadystatechange = function(){
        if(http.readyState==4){
            estadoExecucao="ja";
            fecharJanelaEspera();
            resultado = http.responseText.trim()
            if(http.responseText.trim().substring(0, 1)=="F"){
              mensagensRespostas('#mensagemErrada', http.responseText.trim().substring(1, http.responseText.trim().length)); 
            }else if(http.responseText.trim()!=""){
               mensagensRespostas('#mensagemCerta', "Dados alterados com sucesso."); 
              listaValores = JSON.parse(resultado)
              passarDados();
            }                
        }
      }
      enviarComGet("tipoAcesso=alterarDados&dados="+JSON.stringify(dadosEnviar));
  }

  function passarDados(){
    $("#tabTurmas tr td input[type=checkbox]").prop("checked", false)
    listaValores.forEach(function(dado){

      var idsLinguasEtrang = new Array();
      if(dado.gerencMatricula.idsLinguasEtrang!=null && dado.gerencMatricula.idsLinguasEtrang!=undefined  && dado.gerencMatricula.idsLinguasEtrang!=""){
        idsLinguasEtrang = dado.gerencMatricula.idsLinguasEtrang.toString().split(",")
      }
 
      var idsDisciplOpcao = new Array();
      if(dado.gerencMatricula.idsDisciplOpcao!=null && dado.gerencMatricula.idsDisciplOpcao!=undefined && dado.gerencMatricula.idsDisciplOpcao!=""){
        idsDisciplOpcao = dado.gerencMatricula.idsDisciplOpcao.toString().split(",")
      }

      for(var i=0; i<=(idsLinguasEtrang.length-1); i++){
        $("#tabTurmas tr#"+dado.gerencMatricula.idPGerMatr+" td input[idDisciplina="+idsLinguasEtrang[i]
          +"]").prop("checked", true)
      }
      for(var i=0; i<=(idsDisciplOpcao.length-1); i++){
        $("#tabTurmas tr#"+dado.gerencMatricula.idPGerMatr+" td input[idDisciplina="+idsDisciplOpcao[i]
          +"]").prop("checked", true)
      }

    })
  }