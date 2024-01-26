var valorMaximo =10;
window.onload = function (){
  fecharJanelaEspera();
  seAbrirMenu();
 entidade="alunos"
 directorio = "areaPedagogica/cadeirantes/";

  $("#luzingu").val(luzingu);

  if(classeP<=6){
    valorMaximo =10;
  }else{
    valorMaximo = 20;
  }

  fazerPesquisa();

  $("#luzingu").change(function(){
      window.location ="?luzingu="+$("#luzingu").val(); 
  })
  
  var repet=true;
  $("#listaAlunos").bind("click mouseenter", function(){
    repet=true;
    $("#listaAlunos form").submit(function(){
      if(repet==true){
        if(repet==true){
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
             $(".form"+$(this).attr("idPPauta")+" button").html('<i class="fa fa-spinner fa-spin"></i>');
            manipularPautasCadeirantes(new FormData(this));
          }
          repet=false;
        }
        return false;
      }
    });
  })

}

function manipularPautasCadeirantes(valores){
  http.onreadystatechange = function(){
      if(http.readyState==4){
        estadoExecucao="ja"
        var resultado = http.responseText.trim()
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1,resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "A nota foi alterada com sucesso.");          
          pautas = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
    }
   enviarComPost(valores);
}

  function fazerPesquisa(){
            var html="";
              var notas = new Array();
              var i=0;
              var contagem = -1;

              if(jaTemPaginacao==false){
                 paginacao.baraPaginacao(pautas.filter(fazerPesquisaCondition).length, 50);
              }else{
                  jaTemPaginacao=false;
              }
             
             $("#numTotal").text(completarNumero(pautas.filter(fazerPesquisaCondition).length));

             var numTotAprovado=0;
             var numTotMasculino =0;

           pautas.filter(fazerPesquisaCondition).forEach(function(dado){
              contagem++;
               if(contagem>=paginacao.comeco && contagem<=paginacao.final){
                  i++;

                  html +='<form class="row form'+dado.cadeiras_atraso.idPCadeirantes+' formulario" idPPauta="'
                    +dado.cadeiras_atraso.idPCadeirantes+'" method="POST">'+
                    '<div class="col-lg-4 col-md-4 lead toolTipeImagem" imagem="'+dado.fotoAluno+'"><div class="visible-md visible-lg"><br/></div>'+
                    '<strong class="nomeAluno" style="font-size:17pt;">'+dado.nomeAluno
                    +'</strong><br/>(<a href="'+caminhoRecuar+'areaSecretaria/relatorioAluno?idPMatricula='+dado.idPMatricula
                    +'" style="font-size:15pt !important;" class="lead black">'+dado.numeroInterno
                    +'</a>)</div>'+
                    '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead" style="font-size:15pt;"><div class="visible-md visible-lg"><br/></div>'+dado.nomeDisciplina
                    +'</div><div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>ANO LECTIVO</strong><input type="text" name="anoLectivo" required class="form-control text-center lead" step="1" disabled value="'+
                    dado.numAno
                    +'"></div><div class="col-md-2 text-center"><strong>EXAME ESPEC.</strong><input type="number" name="exameEspecial"'
                    +' class="form-control text-center inputVal lead" step="0.01" min="'+(valorMaximo/2)+'" max="'
                    +valorMaximo+'" required value="'+dado.cadeiras_atraso.exameEspecial
                    +'"></div><div class="col-lg-1 col-md-1"><br/><button type="submit" class="lead form-control text-center btn-primary"><i class="fa fa-check"></i></button></div>'+
                    '<input type="hidden" value="'+dado.cadeiras_atraso.idPCadeirantes+'" name="idPCadeirantes">'+
                    '<input type="hidden" value="'+classeP+'" name="classe">'+
                    '<input type="hidden" value="'+idCursoP+'" name="idPCurso">'+
                    '<input type="hidden" value="'+dado.grupo+'" name="grupo">'+
                     '<input type="hidden" value="'+dado.cadeiras_atraso.idCadDisciplina+'" name="idPNomeDisciplina">'+
                     '<input type="hidden" value="'+dado.idPMatricula+'" name="idPMatricula">'+
                    '<input type="hidden" value="manipularPautasCadeirantes" name="action">'+
                    '</form>'
              }
           });
      $("#listaAlunos").html(html);
      corCelula();
  };


    function corCelula(){
      $("#listaAlunos form .inputVal").each(function(i){
          if($(this).val()<valorMaximo/2 && $(this).val().trim()!=""){
              $(this).css("color", "red");
          }else{
            $(this).css("color", "darkblue");
          }
      });
    }


