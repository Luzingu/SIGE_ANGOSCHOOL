 var classe =0;
  var dia="";

  var dadosHorario=new Array();
  var tempoDia = new Array();

  window.onload=function(){
    
      fecharJanelaEspera();
      seAbrirMenu();

      $("#luzingu").val(luzingu);

      listarDivisao() 
      
      directorio = "areaPedagogica/comissaoExames/";

      $("#paraTodosProf").change(function(){
        $("#tabDivisao select").val($(this).val())
      })
      $("#anosLectivos, #luzingu").change(function(){
         window.location ="?luzingu="+$("#luzingu").val();
      })
      $("#totoCheckBox").change(function(){
        if($(this).prop("checked")==true){
          $("#tabDivisao input[type=checkbox]").prop("checked", true)
        }else{
          $("#tabDivisao input[type=checkbox]").prop("checked", false)
        }
      })

      $(".btnAlterar").click(function(){
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao";

          listaDivisaoProfessores = new Array();
          $("#tabDivisao select").each(function(){

            var estadoComissaoExame="I";
            if($("#aval"+$(this).attr("idPDivisao")).prop("checked")==true){
              estadoComissaoExame="A"
            }

            listaDivisaoProfessores.push({idPDivisao:$(this).attr("idPDivisao"),
              idPresidenteComissaoExame:$(this).val(), "estadoComissaoExame":estadoComissaoExame})
          })
          manipularHorario()
        }
      })

      $("#actualizar").click(function(){  
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao";
          gravarHorarios();
        }       
      });
  }

  function listarDivisao(){
    var html="";

    divisaoProfessor.forEach(function(dado){

      html +="<tr><td class='lead'>"+dado.nomeDisciplina
      +"</td><td class='lead'>"+
      "<select  style='font-weight:bolder; font-size:13pt;' class='form-control lead' idPDivisao='"+
    dado.idPDivisao+"'>"+
      selecProfessores(dado.idPresidenteComissaoExame)+
      "</select></td>"

      estadoComissaoExame = dado.estadoComissaoExame
      if(estadoComissaoExame=="A"){
        estadoComissaoExame="checked";
      }else{
        estadoComissaoExame="";
      }
      html +='<td class="text-center"><div class="switch"><label class="lead"><input type="checkbox"'+
      ' style="margin-left: -15px;" id="aval'+dado.idPDivisao+'" '+estadoComissaoExame
      +' class="altEstado"><span class="lever"></span></label></div></td></tr>';
    });
     $("#tabDivisao").html(html);
  }

  function selecProfessores(idPEntidade){

    var selecRetorno="<option value='-1'>Nenhum Professor</option>";

    listaProfessores.forEach(function(dado){
      var seSelected=""
      if(dado.idPEntidade==idPEntidade){
        seSelected="selected";
      }
      selecRetorno +="<option value='"+dado.idPEntidade+"' "+
      seSelected+">"+dado.nomeEntidade+"</option>";
    })
    return selecRetorno;
  }

  function manipularHorario(){
  chamarJanelaEspera("...")     
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        estadoExecucao="ja";
        resultado = http.responseText.trim()
        if(resultado.substring(0, 1)=="F"){
          mensagensRespostas('#mensagemErrada', resultado.substring(1, http.responseText.trim().length)); 
        }else if(http.responseText.trim()!=""){
          mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso.");         
        }               
      }
    }
    enviarComGet("tipoAcesso=alterarDados&dadosEnviar="+JSON.stringify(listaDivisaoProfessores));
  }