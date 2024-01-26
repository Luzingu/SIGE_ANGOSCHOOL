var idPGestor="";
var tipoResultado="";
window.onload=function(){

  fecharJanelaEspera();
  seAbrirMenu()
  directorio = "areaGestaoInscricao/processarResultados/";
  listarGestorVagas();

  var repet=true;
  $("#tabela").bind("click mouseenter", function(){
    repet=true;
      $("#tabela tr td a.resultado").click(function(){
       idPGestor = $(this).attr("idPGestor");
       tipoResultado = $(this).attr("tipoResultado");

       idGestCurso = $(this).attr("idGestCurso");
       if(tipoResultado=="definitivo"){
          mensagensRespostas('#janelaPergunta', "Ao lançar os resultados definitivos não poderás"+
            " fazer alteração de nenhum dado neste curso. Pretendes continuar com esta acção?");
       }else{
          processarResultado();
       }
       
      })
  })

  var rep=true;
  $("body").bind("mouseenter click", function(){
        rep=true;
      $("#janelaPergunta #pergSim").click(function(){
        if(rep==true){
              fecharJanelaToastPergunta();
              numeroGrupos  = $("#numeroGrupos").val();
              processarResultado();
          
          rep=false;
        }         
    })
  })  
}


function listarGestorVagas(){
  var html="";
  gestorvagas.forEach(function(dado){
    $("#codigoTurma").val(dado.codigoDeTurma);
    var vagasReg = dado.vagasReg;
    if(vagasReg==null){
      vagasReg=0;
    }
    var vagasPos = dado.vagasPos;
    if(vagasPos==null){
      vagasPos=0;
    }
      var criterio  = vazioNull(dado.criterioTeste);
      if(criterio=="exameAptidao"){
        criterio ="Exame de Aptidão"
      }else if(criterio=="factor"){
        criterio ="Factores"
      }else if(criterio=="criterio"){
        criterio ="Critérios"
      }

      var estado=""
      var relatorio=""
      if(dado.estadoTransicaoCurso=="V"){        
        estado ="<i class='fa fa-check text-success' title='Resultados definitivos'></i>"
      }else if(dado.estadoTransicaoCurso=="Y"){
         estado ="<i class='fa fa-refresh text-primary' title='Resultados provisórios'></i>"
      }else if(dado.estadoTransicaoCurso=="F"){
         estado ="<i class='fa fa-times text-danger' title='Nenhum resultado'></i>"
      }

      if(dado.estadoTransicaoCurso=="V" || dado.estadoTransicaoCurso=="Y"){
        relatorio ="<a href='"+caminhoRecuar+"areaGestaoInscricao/resultadoFinal/?idPCurso="+dado.idGestCurso+"' class='btn btn-primary'><i class='fa fa-eye'></i> ver</a>&nbsp;&nbsp;&nbsp;"+
        "<a class='btn btn-primary' href='../../relatoriosPdf/relatoriosInscricao/listaResultados.php?idPCurso="+dado.idGestCurso+"'><i class='fa fa-print'></i> visualizar</a>"
      }

    html +="<tr><td class='lead text-center'>"+abrevNomeDoCurso(dado.idGestCurso)+"</td>"
    +"<td class='lead text-center'>"+(parseInt(vagasReg)+parseInt(vagasPos))
    +"</td>"
    +"<td class='lead text-center'>"+criterio+"</td>"
    +"<td class='lead text-center'>"+estado+"</td>"
    +"<td class='lead text-center'><a href='#' class='resultado' title='Processar resultados provisórios' idPGestor='"+dado.idPGestor
    +"' tipoResultado='provisorio' idGestCurso='"+dado.idGestCurso+"'>"+
    "<i class='fa fa-hammer fa-2x text-primary'></i></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    + "<a href='#' class='resultado' title='Lançar resultados definitivos' idPGestor='"+dado.idPGestor
    +"' tipoResultado='definitivo' idGestCurso='"+dado.idGestCurso+"'>"+
    "<i class='fa fa-hammer text-success fa-2x'></i></a>"
    +"</td><td class='lead text-center'>"+relatorio+"</td>"
    +"</tr>";
  })
  $("#tabela").html(html);
}


function processarResultado(){
  chamarJanelaEspera("...");   
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      resultado = http.responseText.trim()
      if(resultado.substring(0,1)=="F") {
        mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
      }else{
        mensagensRespostas('#mensagemCerta', "Os resultados foram lançados com sucesso. Clique em ver ou visulizar para"+
          " poder listar os resultados dos alunos.");
        gestorvagas = JSON.parse(resultado);
        listarGestorVagas(); 
          
      }
    }
  }
  enviarComGet("tipoAcesso=processarResultado&tipoResultado="
    +tipoResultado+"&idPGestor="+idPGestor+"&idGestCurso="+idGestCurso);
} 