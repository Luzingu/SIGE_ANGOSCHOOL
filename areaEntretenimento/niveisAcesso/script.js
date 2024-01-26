window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();

    directorio = "areaEntretenimento/niveisAcesso/";
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#markAcesso").change(function(){
      if($(this).prop("checked")==true){
        $("#formularioNiveisAcessoForm input[type=checkbox]").prop("checked", true);
      }else{
        $("#formularioNiveisAcessoForm input[type=checkbox]").prop("checked", false);
      }
    }) 

    $("#formularioNiveisAcessoForm").submit(function(){
        alterarNiveisAcesso();
      return false;
    });

    $("#formularioAltClasse form").submit(function(){
      alterarAcessoClasse();
      return false;
    })

    var repet=true;
    $("#tabela").bind("mouseenter click", function(){
      repet=true;
      $("#tabela div a").click(function(){
          if(repet==true){
            $("#formularioAltClasse input[type=checkbox]").prop("checked", false);
            $("#formularioNiveisAcessoForm input[type=checkbox]").prop("checked", false);
            idPrincipal = $(this).attr("idPrincipal");
            if($(this).attr("action")=="altNivel"){
              passarValoresNiveisAcessoNoFormulario();
              $("#formularioNiveisAcesso").modal("show");
            }
            repet=false;
          }
      })
    })

}

function fazerPesquisa(){
  $("#numTCursos").text(listaProfessores.length);

  var tbody = "";
  var i=0;
  listaProfessores.forEach(function(dado){
    i++
    var listaAcessos = ""
    if(dado.acessos!=undefined && dado.acessos!=null){

      dado.acessos.forEach(function(acesso){
        niveisAcessos.forEach(function(niveis){
          if(niveis.idPMenu==acesso.idPMenu && acesso.idEscola==idPEscola){
            if(listaAcessos!=""){
              listaAcessos+=", ";
            }
            listaAcessos += acesso.designacaoMenu
          }
        })
        
      })
    }

    tbody +="<tr><td class='text-center lead'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoEntidade+"'><a href='"+caminhoRecuar
    +"areaSecretaria/relatorioFuncionario?idPFuncionario="+dado.idPEntidade
    +"' class='black'>"+dado.nomeEntidade
    +"</a></td><td class='lead'>"+listaAcessos+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar'"+
    " href='#as' action='altNivel' idPrincipal='"+dado.idPEntidade
    +"'><i class='fa fa-check-circle'></i></a></div></td></tr>";
   
  });
  $("#tabela").html(tbody)
}

function passarValoresNiveisAcessoNoFormulario(){
  $("#formularioNiveisAcesso .listaAcessos input").prop("checked", false)
  listaProfessores.forEach(function(dado){
    if(dado.idPEntidade==idPrincipal){
      var listaAcessos = ""
      if(dado.acessos!=undefined && dado.acessos!=null){
        dado.acessos.forEach(function(acesso){
          if(acesso.idEscola==idPEscola){
            $("#formularioNiveisAcesso .listaAcessos input[idPMenu="+acesso.idPMenu+"]").prop("checked", true)
          }
        })
      }
    } 
  })
}


function alterarNiveisAcesso(){

  chamarJanelaEspera("...")
  $("#formularioNiveisAcesso").modal("hide");

    var acessosActivo = new Array(); 
    var acessosInactivo = new Array(); 
    $("#formularioNiveisAcessoForm .listaAcessos input[type=checkbox]").each(function(dado){

      if($(this).prop("checked")==true){
        acessosActivo.push({idPMenu:$(this).attr("idPMenu"), designacaoMenu:$(this).attr("designacaoMenu")})
      }else{
        acessosInactivo.push({idPMenu:$(this).attr("idPMenu")})
      }  
    })
   enviarComGet("tipoAcesso=alterarNiveisAcesso&acessosActivo="+JSON.stringify(acessosActivo)+"&acessosInactivo="
    +JSON.stringify(acessosInactivo)+"&idPProfessor="+idPrincipal);
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera()
        resultado = http.responseText.trim()
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acesso alterado com sucesso.");
          listaProfessores = JSON.parse(resultado)
          fazerPesquisa()
        }
        
      }
    }
}
