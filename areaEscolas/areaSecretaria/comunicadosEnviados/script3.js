var seApenasAlunosReconf="nao"
window.onload=function(){
  fecharJanelaEspera();
  seAbrirMenu();
  listarComunicados()

  DataTables("#example1", "sim") 
  $("#data").val(data)
  $("#data").change(function(){
    window.location='?data='+$(this).val()
  })
}

function listarComunicados(){
    var tbody=""
    var i=0 
    listacomunicados.forEach(function(dado){
      i++
      tbody +="<tr><td class='text-center'>"+completarNumero(i)+"</td><td>"
      +dado.nome+"</td><td class='text-center'>"+dado.telefone+"</td><td class='text-center'>"+dado.autor
      +"</td><td class='text-center'>"+dado.hora
      +"</td><td class='text-center'>"+dado.mensagem
      +"</td></tr>";           
    });
    $("#numeroMensagens").text(listacomunicados.length)
    $("#tabela").html(tbody);
}
