    var idPProfessor ="";
  var nomeProfessor ="";
  var valorMaximo = 10;

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu(); 
      entidade="alunos"
      directorio = "areaProfessor/historialNotas/"
      $("#luzingu").val(luzingu)

      if(classe>=7){
        valorMaximo=20;
      }
      fazerPesquisa()
      $("#luzingu").change(function(){
        window.location = "?luzingu="+$(this).val()
      });  
  }

  function fazerPesquisa(){
      var html="";
      var i=0;
      var numTotFeminino=0
      var numTotAprovado=0
      $("#numTotFeminino").text(0)
      $("#numTotAprovado").text(0)
      $("#numTotAlunos").text(listaAlunos.filter(fazerPesquisaCondition).length)
      listaAlunos.filter(fazerPesquisaCondition).forEach(function(dado){
        i++

        if(dado.sexoAluno=="F"){
          numTotFeminino ++
        }        
        $("#numTotFeminino").text(numTotFeminino)

        if(dado.pautas.mfd>(valorMaximo/2)){
          numTotAprovado++
        }
        $("#numTotAprovado").text(numTotAprovado)

        html +="<tr><td class='lead'>"+completarNumero(i)
        +"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
        +"</td><td class='lead text-center textVal'>"+vazioNull(dado.pautas.mtI)
        +"</td><td class='lead text-center textVal'>"+vazioNull(dado.pautas.mtII)
        +"</td><td class='lead text-center textVal'>"+vazioNull(dado.pautas.mtIII)
        +"</td><td class='lead text-center textVal'>"+vazioNull(dado.pautas.mfd)
        +"</td></tr>";
      });
      $("#histNotas").html(html);

    corNotasVermelhaAzul2("#histNotas tr", valorMaximo)
  }