
var idPOnline="";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaInterConexao/usuariosOnline/";
    precherDataList(entidadesOnline, "professores");

    fazerPesquisa();
    var repet=true;
    $("#tabela").bind("mouseenter click", function(){
        repet=true;
        $("#tabela button").click(function(){
            if(repet==true){
              idPOnline = $(this).attr("idPOnline");
               mensagensRespostas('#janelaPergunta', "Ao expulsar o usuário pode interrompe-lo com o trabalho que está fazendo neste momento, tens certeza que pretendes continuar com a acção?");
              
              repet=false;
            }
        });
    });

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
               fecharJanelaToastPergunta();
               expulsarProfessor();
            
            rep=false;
          }         
      })
    }) 
}

function fazerPesquisa(){

  $("#numTProfessores").text(completarNumero(entidadesOnline.length))
  var html="";
  var i=0
  entidadesOnline.forEach(function(dado){

    i++;
    var nomeUsuario = dado.nomeEntidade;
    var tipoUsuario= "Entidade";
    var numeroInterno = dado.numeroInternoEntidade;
    var foto = dado.fotoEntidade;
    var link ="../areaAdministrador/areaGestaoEscolas/perfilEntidade?aWRQUHJvZmVzc29y="+dado.idPEntidade;

    if(nomeUsuario==null || nomeUsuario==undefined){
        nomeUsuario = dado.nomeAluno;
        tipoUsuario="Aluno";
        numeroInterno = dado.numeroInterno;
        foto = dado.fotoAluno;
        var link ="../areaAdministrador/areaGestaoEscolas/perfilAluno?aWRQTWF0cmljdWxh="+dado.idPMatricula;
    }

    html +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+foto+"'>"+nomeUsuario
    +"</td><td class='lead text-center'><a href='"+caminhoRecuar+link
    +"' class='black'>"+numeroInterno+"</a></td><td class='lead text-center'>"+tipoUsuario
    +"</td><td class='lead'>"+dado.nomeEscola+"</td><td class='lead text-center'>"+
    dado.horaEntrada+" - "+converterData(dado.dataEntrada)+"</td>"
    +"<td><button type='button' class='btn btn-danger lead' idPOnline='"+dado.idPOnline+"'><i class='fa fa-minus-circle'></i> Expulsar</button></td>";
  
    html +="</tr>";
  })
  $("#tabela").html(html);
}

  function expulsarProfessor(){
    var http1 = new XMLHttpRequest();
    chamarJanelaEspera("...");
    http1.onreadystatechange = function(){
      if(http1.readyState==4){
        fecharJanelaEspera()
        entidadesOnline = JSON.parse(http1.responseText.trim())
        fazerPesquisa()
      }
    }
    http1.open("GET", caminhoRecuar+"../areaInterConexao/usuariosOnline/manipulacaoDadosDoAjax.php?tipoAcesso=expulsarUsuario&idPOnline="+idPOnline, true);
    http1.send();

  }