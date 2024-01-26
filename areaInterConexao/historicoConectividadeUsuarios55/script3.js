
var idPEntidade="";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="professores";
    directorio = "areaInterConexao/historicoConectividade/";

    $("#dataExp").val(dataExp);

    fazerPesquisa();
    $("#idPEscola").change(function(){
        fazerPesquisa()
    })
    $("#dataExp").change(function(){
      window.location ='?dataEx='+$("#dataExp").val();
    })

    $("#pesquisarEntidade").keyup(function(){
        fazerPesquisa();
    })
}

function fazerPesquisa(){
      if(jaTemPaginacao==false){

        $("#numTProfessores").text(completarNumero(entidadesOnline.filter(fazerCondicao).length));

        paginacao.baraPaginacao(entidadesOnline.length, 100);
      }else{
          jaTemPaginacao=false;
      }
    var i=paginacao.comeco;
    var contagem=-1;
    var html="";

  entidadesOnline.filter(fazerCondicao).forEach(function(dado){
     contagem++;

     if(contagem>=paginacao.comeco && contagem<=paginacao.final){

        i++;
        html +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoUsuario+"'>"+dado.nomeUsuario
        +" ("+vazioNull(dado.numeroInterno)+")</td><td class='lead text-center'>"+dado.tipoUsuario
        +"</td><td class='lead'>"+dado.nomeEscola+"</td><td class='lead' style='font-size:10.5pt;'>"+
        vazioNull(dado.areasAcessadas)+"</td><td class='lead text-center'>"+
        dado.horaEntrada+" - "+
        dado.horaSaida+"</td>";

        html +="</tr>";
      }
  })
  $("#tabela").html(html);
}


function fazerCondicao(elem, ind, obj){
 return (elem.idOnlineEntEscola==$("#idPEscola").val() || $("#idPEscola").val()=="") && elem.nomeUsuario.toLowerCase().indexOf($("#pesquisarEntidade").val().toLowerCase())>=0
 
}
