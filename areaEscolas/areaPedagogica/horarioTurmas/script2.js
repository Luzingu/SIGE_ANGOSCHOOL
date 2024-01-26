 var classe =0;
  var dia="";

  var dadosHorario=new Array();
  var tempoDia = new Array();

  window.onload=function(){
    
      fecharJanelaEspera();
      seAbrirMenu();

      $("#luzingu").val(luzingu);

      
        listarHorario();
        listarDivisao() 
      
      directorio = "areaPedagogica/horarioTurmas/";

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

      $("#visualizarHorario").click(function(){
          window.location =caminhoRecuar+"relatoriosPdf/horarioTurmas/index.php?turma="+turma
            +"&classe="+classeP+"&idPCurso="+idCursoP;
      })

      $(".btnAlterar").click(function(){
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao";
          var i=0;
          $("table#tabHorario tr td select").each(function(){
            tempoDia[i] = $(this).attr("identificador")+" => "+$(this).val()
            i++;
          })

          listaDivisaoProfessores = new Array();
          $("#tabDivisao select").each(function(){

            var avaliacoesContinuas="I";
            if($("#aval"+$(this).attr("idPDivisao")).prop("checked")==true){
              avaliacoesContinuas="A"
            }

            listaDivisaoProfessores.push({idPDivisao:$(this).attr("idPDivisao"),
              idPEntidade:$(this).val(), avalContinuas:avaliacoesContinuas})
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

  function porValoresNoFormulario(){
    $("#formularioHorario select").val(-1);
     horario.forEach(function(dado){
        if(dado.dia==dia){            

            var idPDisciplina=dado.idHorDisc;
            if(idPDisciplina==null){
              idPDisciplina="-1";
            }
            $("#formularioHorario #tempo"+dado.tempo).val(idPDisciplina);

        }
     });
     $("#formularioHorario").modal("show");
  }




  function listarHorario(){
    if(horario.length==0){
      $("#tabHorario").hide();
    }else{
      $("#tabHorario").show();
    }
    $("#tabHorario tr td select").val("")
    horario.forEach(function(dado){     
        var disciplina = dado.idPNomeDisciplina;
        if(disciplina==null || disciplina==""){
          disciplina="";
        }
        $("#tabHorario tr td select#t"+dado.tempo+dado.dia).val(disciplina);      
    })
  }

  function listarDivisao(){
    var html="";
    var atributoSelect="";
    var contadorLinhas=0;

    divisaoProfessor.forEach(function(dado){ 
      contadorLinhas++;
      if(contadorLinhas>=2 && classeP<=classeModoDocencia){
        atributoSelect="disabled";
      }         
      html +="<tr><td class='lead'>"+dado.nomeDisciplina
      +"</td><td class='lead'>"+
      "<select "+atributoSelect+" style='font-weight:bolder; font-size:13pt;' class='form-control lead' idPDivisao='"+
    dado.idPDivisao+"'>"+
      selecProfessores(dado.idPEntidade)+
      "</select></td>"

      avaliacoesContinuas = dado.avaliacoesContinuas
      if(avaliacoesContinuas=="A"){
        avaliacoesContinuas="checked";
      }else{
        avaliacoesContinuas="";
      }
      html +='<td class="text-center"><div class="switch"><label class="lead"><input type="checkbox"'+
      ' style="margin-left: -15px;" id="aval'+dado.idPDivisao+'" '+avaliacoesContinuas
      +' class="altEstado"><span class="lever"></span></label></div></td><td class="text-center lead">'
        
      if(dado.idPEntidade!=null){
        html +='<a href="../../relatoriosPdf/relatoriosProfessores/horarioProfessor.php?idPProfessor='+
          dado.idPEntidade+'"><i class="fa fa-print"></i> Horário Indiv.</a>'
      }

      html +="</td></tr></tr>";
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

  function gravarHorarios(){
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){
          estadoExecucao="ja";
          fecharJanelaEspera()
          if(http.responseText.trim().substring(0, 1)=="F"){
            mensagensRespostas('#mensagemErrada', http.responseText.trim().substring(1, http.responseText.trim().length)); 
          }else if(http.responseText.trim()!=""){
            var resultado = JSON.parse(http.responseText.trim())

            horario = resultado[0]
            divisaoProfessor = resultado[1]
            listarHorario();
            listarDivisao()
          }                      
      }
    }
    enviarComGet("tipoAcesso=gravarHorarios&classe="+classeP
      +"&idPCurso="+idCursoP+"&turma="
      +turma);
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
            var resultado = JSON.parse(http.responseText.trim())
            horario = resultado[0]
            divisaoProfessor = resultado[1]
            listarHorario();
            listarDivisao()           
          }               
      }
    }
    enviarComGet("tipoAcesso=manipularHorario&idPCurso="+idCursoP
        +"&classe="+classeP+"&turma="+turma
        +"&dados="+tempoDia+
      "&listaDivisaoProfessores="+JSON.stringify(listaDivisaoProfessores)
      +"&classeModoDocencia="+classeModoDocencia);
  }