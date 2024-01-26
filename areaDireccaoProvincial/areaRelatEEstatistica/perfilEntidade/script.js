 window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();

       selectProvincias("#modalGuiaMarcha #pais", "#modalGuiaMarcha #provincia",
          "#modalGuiaMarcha #municipio", "#modalGuiaMarcha #comuna");
 
      directorio = caminhoRecuar+"../areaDirector/perfilProfessor/";

       $("#pesquisarAluno").submit(function(){
       	window.location ="?valorPesquisado="+$("#valorPesquisado").val();
       		return false;
       })

       var tipoDeclaracao="";

       $("#guiaMarcha").click(function(){
       		if(idPProfessor!=null && idPProfessor!=""){
       			$("#modalGuiaMarcha").modal("show");
       		}
       })

       $("#declarao").click(function(){
       		if(idPProfessor!=null && idPProfessor!=""){
                tipoDeclaracao="declarao";
                $("#modalDeclaraoTrabalho .paraDeclaracaoVencimento").hide();
       			$("#modalDeclaraoTrabalho").modal("show");
       		}
       })

       $("#declaraoVencimento").click(function(){
        if(idPProfessor!=null && idPProfessor!=""){
            $("#modalDeclaraoTrabalho .paraDeclaracaoVencimento").show();
            tipoDeclaracao="declaraoComVencimento";
            $("#modalDeclaraoTrabalho").modal("show");
        }
       })

       $("#formGuiaMarcha").submit(function(){
          window.location =caminhoRecuar+"relatoriosPdf/relatoriosFuncionarios/guiaMarcha.php?numeroGuiaMarcha="+$("#numeroGuiaMarcha").val()
            +"&pais="+$("#formGuiaMarcha [name=pais]").val()+"&provincia="+$("#formGuiaMarcha [name=provincia]").val()
            +"&municipio="+$("#formGuiaMarcha [name=municipio]").val()+"&comuna="+$("#formGuiaMarcha [name=comuna]").val()+"&motivo="+$("#motivo").val()
            +"&assinante="+$("#funcionarioAssinar").val()+"&idPProfessor="+idPProfessor;
 		     return false;
       });

       $("#declaraoTrabalhoForm").submit(function(){
            if(tipoDeclaracao=="declarao"){
       	    window.location =caminhoRecuar+"relatoriosPdf/relatoriosFuncionarios/declaracaoFuncionario.php?idPProfessor="+idPProfessor
       	    +"&motivoDeclaracao="+$("#motivoDeclaracao").val()+"&assinante="+$("#dirigenteAssinar").val()
                +"&numeroDeclaracao="+$("#numeroDeclaracao").val()+"&declVencimento=nao";
            }else if(tipoDeclaracao=="declaraoComVencimento"){
                  window.location =caminhoRecuar+"relatoriosPdf/relatoriosFuncionarios/declaracaoFuncionario.php?idPProfessor="+idPProfessor
                  +"&motivoDeclaracao="+$("#motivoDeclaracao").val()+"&assinante="+$("#dirigenteAssinar").val()
                  +"&numeroDeclaracao="+$("#numeroDeclaracao").val()+"&declVencimento=sim";
            }
       	return false;
       })
  }
  

  function fazerPesquisa(){};