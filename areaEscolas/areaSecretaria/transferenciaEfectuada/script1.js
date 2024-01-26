var idPTransferencia="";

window.onload = function(){
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaSecretaria/transferenciaEfectuada/";
    $("#anosLectivos").val(idPAno);
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#anosLectivos").change(function(){
      window.location ="?idPAno="+$("#anosLectivos").val();
    });

    var repet=true;
    $("#tabTransferencia").bind("click mouseenter", function(){
        repet=true;
        $("#tabTransferencia a.anular").click(function(){
            if(repet==true){
              idPTransferencia = $(this).attr("idPTransferencia");
              idPMatricula = $(this).attr("idPMatricula");
              mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular esta transferência?");
              repet=false;
            }
        })
    })
    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              idEspera = "#janelaPergunta #pergSim";
              estadoExecucao="espera";
              cancelarTransferencia();
            }
            rep=false;
          }         
      })
    }) 
}
    
    function fazerPesquisa(){
    var contagem=0;
    var html ="";
    var masculino=0;
    $(".numTMasculinos").text(masculino);  

    $(".numTAlunos").text(completarNumero(alunosTransferidos.length));

     alunosTransferidos.forEach(function(dado){
         contagem++;
          if(dado.transferencia.estadoTransferencia=="V"){
            masculino++;
          }

          if(dado.transferencia.idTransfEscolaDestino==null || dado.transferencia.idTransfEscolaDestino==undefined){
              escola = dado.transferencia.nomeEscolaDestino;
          }else{
              escola = dado.nomeEscola;
          }

          if(dado.transferencia.estadoTransferencia=="V"){
            estado ="<i class='fa fa-check text-success'></i>";
          }else{
            estado ="<i class='fa fa-refresh text-primary'></i>";
          }

          $(".numTMasculinos").text(completarNumero(masculino)); 

          html += "<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
          +"</td><td class='lead text-center'>"+dado.numeroInterno
          +"</td><td class='lead'>"+escola
          +"</td><td class='lead text-center'>"+estado
          +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"relatoriosPdf/guiaTransferencia/?idPTransferencia="+
          dado.transferencia.idPTransferencia+"&idPMatricula="+dado.idPMatricula+"'><i class='fa fa-print'></i> Visualizar</a></td>"+
          "<td class='text-center'><br/><a href='#' class='lead text-center text-danger anular' idPTransferencia='"
          +dado.transferencia.idPTransferencia+"'idPMatricula='"
          +dado.idPMatricula+"'><i class='fa fa-times-circle'></i></a></td></tr>";
                
     });
     $("#tabTransferencia").html(html);
    };

    function cancelarTransferencia(){
        chamarJanelaEspera("..."); 
        http.onreadystatechange = function(){
          if(http.readyState==4){
            fecharJanelaEspera();
            estadoExecucao="ja";
            resultado = http.responseText.trim()
            if(resultado.substring(0,1)=="F"){
              mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));             
            }else{
              mensagensRespostas('#mensagemCerta', "A transferência foi cancelada com sucesso."); 
              alunosTransferidos = JSON.parse(resultado)
              fazerPesquisa();
               
            }
          }
        }
        enviarComGet("tipoAcesso=cancelarTransferencia&idPTransferencia="+idPTransferencia
          +"&idPMatricula="+idPMatricula);
    }


    
    