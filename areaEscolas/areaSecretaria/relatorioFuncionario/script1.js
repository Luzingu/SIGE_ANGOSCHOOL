 window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaSecretaria/novaMatricula/"; 
      $("#formularioPerfil #pais").val("")
      selectProvincias("#formularioPerfil #pais", "#formularioPerfil #provincia", "#formularioPerfil #municipio","#formularioPerfil #comuna", false)
       porValoresFormulario();


       selectProvincias("#modalGuiaMarcha #pais", "#modalGuiaMarcha #provincia",
          "#modalGuiaMarcha #municipio", "#modalGuiaMarcha #comuna");

        $("#formularioPerfil").submit(function(){
            if(validarFormularios("#formularioPerfil")==true){
              actualizarPerfil();      
            }
            return false;
        })

       $("#pesquisarAluno").submit(function(){
       	window.location ="?valorPesquisado="+$("#valorPesquisado").val();
       		return false;
       })

       var tipoDeclaracao="";

       $(".fichaAvaliacaoDesempenho").click(function(){
             window.location =caminhoRecuar+"relatoriosPdf/relatoriosProfessores/fichaAvaliacaoDesempenho.php?idPEntidade="
             +idPProfessor+"&idPAno="+$("#anosLectivos").val()+"&trimestre="+$(this).attr("id");
       })

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
          window.location =caminhoRecuar+"relatoriosPdf/relatoriosProfessores/guiaMarcha.php?numeroGuiaMarcha="+$("#numeroGuiaMarcha").val()
            +"&pais="+$("#formGuiaMarcha [name=pais]").val()+"&provincia="+$("#formGuiaMarcha [name=provincia]").val()
            +"&municipio="+$("#formGuiaMarcha [name=municipio]").val()+"&comuna="+$("#formGuiaMarcha [name=comuna]").val()+"&motivo="+$("#motivo").val()
            +"&assinante="+$("#funcionarioAssinar").val()+"&idPProfessor="+idPProfessor;
 		return false;
       });

       $("#declaraoTrabalhoForm").submit(function(){
            if(tipoDeclaracao=="declarao"){
       	    window.location =caminhoRecuar+"relatoriosPdf/relatoriosProfessores/declaracaoProfessor.php?idPProfessor="+idPProfessor
       	    +"&motivoDeclaracao="+$("#motivoDeclaracao").val()+"&assinante="+$("#dirigenteAssinar").val()
                +"&numeroDeclaracao="+$("#numeroDeclaracao").val()+"&declVencimento=nao";
            }else if(tipoDeclaracao=="declaraoComVencimento"){
                  window.location =caminhoRecuar+"relatoriosPdf/relatoriosProfessores/declaracaoProfessor.php?idPProfessor="+idPProfessor
                  +"&motivoDeclaracao="+$("#motivoDeclaracao").val()+"&assinante="+$("#dirigenteAssinar").val()
                  +"&numeroDeclaracao="+$("#numeroDeclaracao").val()+"&declVencimento=sim";
            }
       	return false;
       })
  }

  function porValoresFormulario(){
          limparFormulario("#formularioPerfil");
          listaValores.forEach(function(dado){
              $("#formatoDocumento").val(dado.formatoDocumentoEnt)
              $("#nomeEntidade").val(dado.nomeEntidade);
                $("#dataNascEntidade").val(dado.dataNascEntidade);
                 $("#biEntidade").val(dado.biEntidade)
                 $("#dataEBIEntidade").val(dado.dataEBIEntidade)
                 $("#paiEntidade").val(dado.paiEntidade)
                 $("#maeEntidade").val(dado.maeEntidade)
                 $("#numeroTelefoneEntidade").val(dado.numeroTelefoneEntidade)
                 $("#emailEntidade").val(dado.emailEntidade)
                 $("#nivelAcademicoEntidade").val(dado.nivelAcademicoEntidade)
                 $("#numeroAgenteEntidade").val(dado.numeroAgenteEntidade)
                 $("#periodoActividade").val(dado.periodoActividade)
                 $("#estadoEntidade").val(dado.estadoEntidade)
                 $("#estadoAcesso").val(dado.estadoAcesso)
                 $("#categoriaEntidade").val(dado.categoriaEntidade)
                 $("#cursoEnsinoMedio").val(dado.cursoEnsinoMedio)
                $("#escolaEnsinoMedio").val(dado.escolaEnsinoMedio)
                $("#cursoLicenciatura").val(dado.cursoLicenciatura)
                $("#escolaLicenciatura").val(dado.escolaLicenciatura)
                $("#cursoMestrado").val(dado.cursoMestrado)
                $("#escolaMestrado").val(dado.escolaMestrado)
                $("#cursoDoutoramento").val(dado.cursoDoutoramento)
                $("#escolaDoutoramento").val(dado.escolaDoutoramento)
                $("#dataInicioFuncoesEntidade").val(dado.escola.dataInicioFuncoesEntidade);
                $("#dataCaducBI").val(dado.dataCaducBI)
                $("#nivelSistemaEntidade").val(dado.escola.nivelSistemaEntidade)

                $("#pagamentoPorTempo").val(dado.escola.pagamentoPorTempo)

                
                $("#dataInicOutraEsc").val(dado.dataInicOutraEsc)
                 $("#tempoServOutraEsc").val(dado.tempoServOutraEsc)
                 $("#dataInicEduc").val(dado.dataInicEduc)
                 $("#numSegSocial").val(dado.numSegSocial)
                 $("#numDespacho").val(dado.numDespacho)
                 $("#dataDespacho").val(dado.dataDespacho)
                 $("#naturezaVinc").val(dado.escola.naturezaVinc)
                 $("#cargoPedagogicoEnt").val(dado.escola.cargoPedagogicoEnt)
                 $("#numeroContribuinte").val(dado.numeroContribuinte);
                $("#valorAuferidoNaEducacao").val(dado.valorAuferidoNaEducacao);
                $("#valorAuferidoNaInstituicao").val(dado.escola.valorAuferidoNaInstituicao);
                $("#tipoPessoal").val(dado.escola.tipoPessoal);
                $("#funcaoEnt").val(dado.escola.funcaoEnt);

                $("#nomeBanco").val(dado.escola.nomeBanco)
                $("#numeroContaBancaria").val(dado.escola.numeroContaBancaria)
                $("#ibanContaBancaria").val(dado.escola.ibanContaBancaria)

                passarValoresDaProvincia("#formularioPerfil #pais", "#formularioPerfil #provincia", 
              "#formularioPerfil #municipio", "#formularioPerfil #comuna", dado.paisNascEntidade,dado.provNascEntidade, dado.municNascEntidade, dado.comunaNascEntidade);

                $("#imageProfessor").attr("src", '../../../fotoUsuarios/'+dado.fotoEntidade)
                 if(dado.comFormPedag=="V"){
                    $("#comFormPedag").prop("checked", true);
                 }else{
                    $("#comFormPedag").prop("checked", false);
                 }

                 if(dado.escola.tambemColaboradorNaInstituicao=="V"){
                    $("#tambemColaboradorNaInstituicao").prop("checked", true);
                 }else{
                    $("#tambemColaboradorNaInstituicao").prop("checked", false);
                 }

                 if(dado.comMagisterio=="V"){
                    $("#comMagisterio").prop("checked", true);
                 }else{
                    $("#comMagisterio").prop("checked", false);
                 }
          })

        }

      function actualizarPerfil(){
      chamarJanelaEspera("...")
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim();
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{
            mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso."); 
          }
        }
      }
        enviarComPost(new FormData(document.getElementById('formularioPerfil')));
      }