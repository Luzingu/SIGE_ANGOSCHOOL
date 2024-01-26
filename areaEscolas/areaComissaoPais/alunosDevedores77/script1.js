  
  var totalMarcado=0;
  window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaComissaoPais/alunosDevedores77/";

      $("#mesAtraso").val(mes);
      $("#anosLectivos").val(idPAno)
      $("#idPNomeCurso").val(idPNomeCurso)

      fazerPesquisa();
      DataTables("#example1", "sim") 

      $("#idPNomeCurso, #mesAtraso, #anosLectivos").change(function(){ 
        window.location ="?idPAno="+$("#anosLectivos").val()+"&mes="+$("#mesAtraso").val()
        +"&idPNomeCurso="+$("#idPNomeCurso").val();
      });

      precificador();
      $("#textoMensagem").bind("change keyup", function(){
        precificador();
      })
      var repet=true
      $("#example1").bind("click mouseenter", function(){
        repet=true
        $("#example1 tr td input[type=checkbox]").change(function(){
          if(repet==true){
            var contador=0
            $("#example1 tr td input[type=checkbox]").each(function(){
                if($(this).prop("checked")==true){
                  contador++
                }
            })
            totalMarcado = contador
            precificador()
            repet=false
          }
        })
      })

      $("#formSubmit").submit(function(){
        var dadosEnviar=new Array();
        $("#example1 tr td input[type=checkbox]").each(function(){
            if($(this).prop("checked")==true){

              dadosEnviar.push({id:$(this).attr("id"), "nome":$(this).attr("nome"),
                "telefone":$(this).attr("telefone")})
            }
        })
        if(dadosEnviar.length==0){
          mensagensRespostas2("#mensagemErrada", "Não seleccionaste nenhum destinatário.")
        }else{
          $("#dadosEnviar").val(JSON.stringify(dadosEnviar))
          enviarMensagens()
        }
        return false
      })

  }
   function  fazerPesquisa(){
      var html ="";

      $("#totContas").text(completarNumero(listaAlunos.length));
      var i=0;

      totalMarcado=0
      listaAlunos.forEach(function(dado){
          i++;
         html += "<tr><td class='lead text-center'>"
         if(dado.telefoneAluno!=null && dado.telefoneAluno!=undefined
          && dado.telefoneAluno!=""){
            totalMarcado++
            html +="<input type='checkbox' id='"+
            dado.idPMatricula+"' nome='"+dado.nomeAluno+"' telefone='"+dado.telefoneAluno+"' checked>"
         }
         html +="</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
         +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+
         "areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
            +"' class='lead black'>"+dado.numeroInterno
          +"</a></td><td class='text-center lead'>"+dado.reconfirmacoes.classeReconfirmacao
          +"</td><td class='text-center lead'>"+vazioNull(dado.reconfirmacoes.nomeTurma)
          +"</td></tr>";      
      })
      $("#example1 tbody").html(html)
   }

   function precificador(){

    var numeroCaracteres = (abrevNomeEscola2+
      " "+$("#textoMensagem").val().trim()).length
    $("#numeroCaracter").text(numeroCaracteres)
    multiplicador = Math.floor(numeroCaracteres/160)+1;

    var precoTotSMS = new Number(totalMarcado)*new Number(precoPorMensagem)*multiplicador
    $("#precoTotSMS").val(precoTotSMS)
    $("#precoMensagem").text(totalMarcado+" / "+
      converterNumerosTresEmTres(precoTotSMS)+" AOA")
  }

  function enviarMensagens(){
    chamarJanelaEspera("")
    http.onreadystatechange = function(){
      if(http.readyState==4){
          estadoExecucao ="ja";
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          if(resultado.substring(0, 1)=="F"){
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
          }else{
            $("#textoMensagem").val("")
            mensagensRespostas('#mensagemCerta', "A mensagem foi enviada com sucesso.")
          }
      }    
    }
    var form = new FormData(document.getElementById("formSubmit"))
    enviarComPost(form)
  }