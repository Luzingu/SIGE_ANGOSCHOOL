
var estado="";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaPedagogica/gerenciadorTrimestres/";

    $("#luzingu").val(luzingu);
    $("#luzingu").change(function(){
       window.location ="?luzingu="+$("#luzingu").val();
    })
    fazerPesquisa();

    var repet=true;
    $("#example1").bind("click mouseenter", function(){
      repet=true;
      $("#example1 tr td input").click(function(){
          if(repet==true){
            if($(this).prop("checked")==true){
              estado="sim";
            }else{
              estado="nao";
            }
            alterarEstadoTrimestre($(this).attr("id"));
            repet=false;
          }
      })  
    })
}

function fazerPesquisa(){
  var todoTrim1=true;
  var todoTrim2=true;
  var todoTrim3=true;
  var todoExame=true;
  var todoConselho=true;
  var todoRecurso=true;
  var todaPauta=true;

    var tbody = "";
    listaDivisaoProfessores.forEach(function(dado){


        var trimestre1 =retornarCheck(dado.idPDivisao+"-trimestre1", dado.periodoTrimestre, "trimestre1");
        var trimestre2 =retornarCheck(dado.idPDivisao+"-trimestre2", dado.periodoTrimestre, "trimestre2");
        var trimestre3 =retornarCheck(dado.idPDivisao+"-trimestre3", dado.periodoTrimestre, "trimestre3");
        var exame =retornarCheck(dado.idPDivisao+"-exame", dado.periodoTrimestre, "exame");
        var conselho =retornarCheck(dado.idPDivisao+"-conselho", dado.periodoTrimestre, "conselho");
        var recurso =retornarCheck(dado.idPDivisao+"-recurso", dado.periodoTrimestre, "recurso");
        var todos =retornarCheck(dado.idPDivisao+"-todos", dado.periodoTrimestre, "todos");
        
        var terraMoto = "-"+classeExtensa(dado.classe, dado.sePorSemestre, "sim")
        if(dado.classe<=9){
          terraMoto=classeExtensa(dado.classe, dado.sePorSemestre, "sim")
        }
      tbody +="<tr><td class='lead' colspan='2'>"+vazioNull(dado.abrevCurso)
      +terraMoto+"-"+dado.designacaoTurmaDiv+" / "+dado.abreviacaoDisciplina2
      +"</td><td class='lead text-center'>"+trimestre1+"</td><td class='lead text-center'>"
      +trimestre2+"</td><td class='lead text-center'>"
      +trimestre3+"</td><td class='lead text-center'>"
      +exame+"</td><td class='lead text-center'>"
      +conselho+"</td><td class='lead text-center'>"
      +recurso+"</td><td class='lead text-center'>"
      +todos+"</td></tr>";           
    });
    $("#tabela").html(tbody)
}

function alterarEstadoTrimestre(inputAlterar){
  chamarJanelaEspera("...");
  http.onreadystatechange = function(){
    if(http.readyState==4){
      estadoExecucao="ja";
      fecharJanelaEspera();
      resultado = http.responseText.trim()
      if(resultado.substring(0,1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else if(http.responseText.trim()!=""){
          listaDivisaoProfessores = JSON.parse(resultado);
          fazerPesquisa();
      }
    }
  }
  enviarComGet("tipoAcesso=aletrarEstados&input="+inputAlterar
    +"&estado="+estado+"&classeP="+classeP+"&idCursoP="+idCursoP+"&turma="+turma+
    "&itemAfetar="+$("#itemAfetar").val());




}

function retornarCheck(idCheck, peridoTrimestre, valorPretendido){
  var labelTrimestre = "<small>"+valorPretendido+"</small>";

  var retorno="";
  if(peridoTrimestre==valorPretendido || 
    (peridoTrimestre=="todos" && valorPretendido!="conselho")){
    retorno = labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" checked style="margin-left: -15px;"'+
      ' id="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>';
  }else{      
      retorno =labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" style="margin-left: -15px;"'+
      ' id="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>';
  }
  return retorno;
}