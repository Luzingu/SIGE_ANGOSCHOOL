$(document).ready(function(){
	fazerPesquisa();
	directorio = "areaInterConexao/mensagem55/";
	fecharJanelaEspera();
	seAbrirMenu();
	$('#action_menu_btn').click(function(){
		$('.action_menu').show();
	});
	$("#novaMensagem").submit(function(){
		enviarMensagem();
		return false;
	})
});

function enviarMensagem(){
		$("#novaMensagem #btnSubmit").html('<i class="fa fa-spinner fa-spin fa-fw"></i>') 
    var form = new FormData(document.getElementById("novaMensagem"));
    var http2 = new XMLHttpRequest();
    http2.onreadystatechange = function(){
    	if(http2.readyState==4){
    		$("#novaMensagem #btnSubmit").html('<i class="fas fa-location-arrow"></i> Enviar')
        resultado = http2.responseText.trim();
        listaMensagens = JSON.parse(resultado);
        $("#novaMensagem #mensagem").val("");
        fazerPesquisa();
    	}
        
    }
    http2.open("POST", "../../areaInterConexao/mensagem55/manipulacaoDadosDoAjax.php", true);
    http2.send(form);
}

function fazerPesquisa(){
	var html="";

	listaMensagens.forEach(function(dado){

		if(dado.emissor==usuarioLogado){
			html +='<div class="direct-chat-msg">'+
      '<div class="direct-chat-infos clearfix">'+
        '<span class="direct-chat-name float-left">'+dado.nomeEmissor+'</span>'+
        '<span class="direct-chat-timestamp float-right">'+retornDataExtensa(dado.dataMensagem)+' '+dado.horaMensagem+'</span>'+
      '</div>'+
      '<img class="direct-chat-img" src="'+caminhoRecuar+'../fotoUsuarios/'+dado.fotoEmissor+'" alt="message user image">'+
      '<div class="direct-chat-text">'+dado.textoMensagem+'</div></div>';
		}else{
			html +='<div class="direct-chat-msg right" style="min-width:55% !important;">'+
      '<div class="direct-chat-infos clearfix">'+
        '<span class="direct-chat-name float-right">'+dado.nomeEmissor+'</span>'+
        '<span class="direct-chat-timestamp float-left">'+retornDataExtensa(dado.dataMensagem)+' '+dado.horaMensagem+'</span>'+
      '</div>'+
      '<img class="direct-chat-img" src="'+caminhoRecuar+'../fotoUsuarios/'+dado.fotoEmissor+'">'+
      '<div class="direct-chat-text">'+dado.textoMensagem+'</div></div>';
		}
		
	})
	$("#mensagens").html(html);
}