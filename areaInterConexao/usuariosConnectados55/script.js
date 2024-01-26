$(document).ready(function(){
	 directorio = "areaConversa/chat/";
	fecharJanelaEspera();
	seAbrirMenu();
	
	fazerPesquisa();
	
	if(estadoUsuarios=="online"){
		$("#toogleUsuario").attr("href", "?estadoUsuarios=offline");
	}else{
		$("#toogleUsuario").attr("href", "?estadoUsuarios=online");
	}
	$('#action_menu_btn').click(function(){
		$('.action_menu').show();
	});

	$("#pesUsuarioOnline").keyup(function(){
		pesquisarUsuario()
	})
});


function fazerPesquisa(){
	var html="";
  if(usuarios.length==0){
  	$("#totalUsuarios").text("Nenhum Usuário");
  }else if(usuarios.length==1){
  	$("#totalUsuarios").html('<span class="quantidadeTotal">01</span>');
  }else{
  	$("#totalUsuarios").html('<span class="quantidadeTotal">'+completarNumero(usuarios.length)
  		+'</span>');
  }
	usuarios.forEach(function(dado){

		link =caminhoRecuar+"../areaInterConexao/mensagem55/index.php?usuario="+dado.tipoUsuario
		+"_"+dado.idUsuarioLogado
		html +='<li><a class="link" href="'+link+'">'+
      '<img src="'+caminhoRecuar+"../fotoUsuarios/"+dado.fotoUsuario+'" class="imgUsuario">'+
      '<span class="users-list-name lead">'+dado.nomeUsuario+'</span>'

    if(dado.funcao!="Usuário_Master"){
      html +='<span class="users-list-date text-danger">'+dado.funcao+'</span>'+
      '<span class="users-list-date text-danger">'+dado.abrevNomeEscola+'</span>'
    }
    html +='</a></li>'
	})

	$("#usuarios").html(html);
}

function pesquisarUsuario(){
  var http2 = new XMLHttpRequest();
  http2.onreadystatechange = function(){
  	if(http2.readyState==4){
  		usuarios = JSON.parse(http2.responseText.trim())
      fazerPesquisa();
    }
  }
  http2.open("GET", caminhoRecuar+"../areaInterConexao/usuariosConnectados/manipulacaoDadosDoAjax.php?"+
  	"tipoAcesso=pesqUsuarios&usuarioPesq="+$("#pesUsuarioOnline").val(), true);
  http2.send();
}