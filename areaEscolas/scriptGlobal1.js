
  function retornarPeriodo(valor) {
    if(valor=="pos"){
      return "Pós-Laboral"; 
    }else{
      return "Regular"
    }
  }

  function retornarAnoLectivo(numAno){
    return numAno;
  }

  function retornarTipoDisciplina(valor){
      if(valor=="FG"){
        return "Formação Geral";
      }else if(valor=="FE"){
        return "Formação Específica";
      }else if(valor=="FP"){
        return "Formação Profissional";
      }else if(valor=="Op"){
        return "Opção";
      }else if(valor=="CSC"){
        return "Comp. Sócio Cultural";
      }else if(valor=="CC"){
        return "Comp. Científica";
      }else if(valor=="CTTP"){
        return "Comp. Tec., Tecnol. e Prática";
      }else{
        return vazioNull(valor);
      }
  }

  function listarClasses(valCurso="", valClasse="", idCurso, idClasse){


    if((valCurso=="" && valClasse=="") || (valCurso==null && valClasse==null)){        
      $(idClasse+" #listaClasses").html(porClasses(idCurso))
    }else{
      $(idCurso).val(valCurso)
      $(idClasse+" #listaClasses").html(porClasses(idCurso))
      $(idClasse).val(valClasse)
    }
    $(idCurso).change(function(){
       $(idClasse+" #listaClasses").html(porClasses(idCurso))
       $(idClasse+" #listaClasses").val("")
    })
  }

  function porClasses(idCurso){

    var htmlClasses=""
    listaClassesPorCurso.forEach(function(curso){
      if(curso.idPNomeCurso==$(idCurso).val()){
        htmlClasses +="<option value='"+curso.classes.identificador
        +"'>"+curso.classes.designacao+"</option>"
      }
    })
    return htmlClasses
  }



function corNotasVermelhaAzul(html){
    $(html+" .inputVal").each(function(i){
        if(new Number($(this).val())<new Number($(this).attr("media")) && $(this).val().trim()!=""){
            $(this).css("color", "red");
        }else{
          $(this).css("color", "darkblue");
        }
    });

    $(html+" .textVal").each(function(i){
        if($(this).text()<new Number($(this).attr("media")) && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }else{
          $(this).css("color", "darkblue");
        }
    });

    $(html+" .obs").each(function(i){
        if($(this).val()=="N. Apto"){
            $(this).css("color", "red");
        }else if($(this).val()=="Apto"){
            $(this).css("color", "darkgreen");
        }else if($(this).val()=="Recurso"){
            $(this).css("color", "darkblue");
        }else{
          $(this).css("color", "black");
        }
    });

    $(html+" .bomMau").each(function(i){
        if($(this).val()=="Mau"){
            $(this).css("color", "red");
        }else if($(this).val()=="Muito Bom" || $(this).val()=="Bom"){
            $(this).css("color", "darkgreen");
        }else if($(this).val()=="Suficiente"){
            $(this).css("color", "darkblue");
        }else{
          $(this).css("color", "black");
        }
    }); 
}

function corNotasVermelhaAzul2(html, notaMaxima){

    $(html+" .textVal").each(function(i){
        if(new Number($(this).text())<new Number($(this).attr("media")) && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }else{
          $(this).css("color", "darkblue");
        }
    });

    $(html+" .obs").each(function(i){
        if($(this).text()=="N. Apto"){
            $(this).css("color", "red");
        }else if($(this).text()=="Apto"){
            $(this).css("color", "darkgreen");
        }else if($(this).text()=="Apto(a)"){
            $(this).css("color", "darkgreen");
        }else if($(this).text()=="Transita"){
            $(this).css("color", "darkgreen");
        }else if($(this).text()=="Recurso"){
            $(this).css("color", "darkblue");
        }else{
          $(this).css("color", "red");
        }
    });
}




  function sePossoListar13(idCurso, idClasse, modal=""){

    var duracao = $(idCurso +" option:selected").attr("duracao");

    if(duracao==4){
      $(idClasse+" option[value=13]").remove();
        $(idClasse+" optgroup.d13").append("<option value='13'>13ª Classe</option>");
      }else{
          $(idClasse+" option[value=13]").remove();
      }

    $(idCurso).change(function(){
      var duracao = $(idCurso+" option:selected").attr("duracao");
      if(duracao==4){
        $(idClasse+" option[value=13]").remove();
        $(idClasse+" optgroup.d13").append("<option value='13'>13ª Classe</option>");

      }else{
          $(idClasse+" option[value=13]").remove();
          if($(idClasse).val()==13){
            $(idClasse).val(12);
            classe=12;
          }
          
      }

    })
    if(modal!=""){
      $(modal).on("show.bs.modal", function(){
          var duracao = $(idCurso+" option:selected").attr("duracao");
          if(duracao==4){
            $(idClasse+" option[value=13]").remove();
            $(idClasse+" optgroup.d13").append("<option value='13'>13ª Classe</option>");

          }else{
              $(idClasse+" option[value=13]").remove();
              if($(idClasse).val()==13){
                $(idClasse).val(12);
                classe=12;
              }
              
          }
      });
    }
  }

  function renomearTurmas(curso, classe, turma, idAnoLectivo="", jaAbreviaram="nao"){

    if(idAnoLectivo==""){
      idAnoLectivo = idAnoActual; 
    }
    if(codigoNomeTurma[idAnoLectivo]=="" || codigoNomeTurma[idAnoLectivo]==null 
      || codigoNomeTurma[idAnoLectivo]==0 || codigoNomeTurma[idAnoLectivo]==undefined){
      return turma;
    }else{
      return (curso+classe+turma+codigoNomeTurma[idAnoLectivo]);
    }
  }

    function retornarNomeDocumento(classe, sePorSemestre="nao"){
        
      if(classe=="60" || classe==60){
        return "Certificado do Ensino Primário";
      }else if(classe=="90" || classe==90){
         return "Certificado do Ensino Básico";
      }else if(classe=="120" || classe==120){
        return "Certificado do Ensino Médio";
      }else if(classe=="1200" || classe==1200){
        return "Diploma do Ensino Médio";
      }else{
        return "Declaração da "+classeExtensa(classe, sePorSemestre);
      }
    }

function seleccionarTipoDeDeficiencia(deficiencia){
    $("#tipoDeficiencia").empty();
  if(deficiencia=="Física"){
      $("#tipoDeficiencia").append("<option>Paraplegia</option>");
      $("#tipoDeficiencia").append("<option>Paraparesia</option>");
      $("#tipoDeficiencia").append("<option>Monoplegia</option>");
      $("#tipoDeficiencia").append("<option>Monoparesia</option>");
      $("#tipoDeficiencia").append("<option>Tetraplegia</option>");
      $("#tipoDeficiencia").append("<option>Triplegia</option>");
      $("#tipoDeficiencia").append("<option>Paraplegia</option>");
      $("#tipoDeficiencia").append("<option>Triparesia</option>");
      $("#tipoDeficiencia").append("<option>Hemiplegia</option>");
      $("#tipoDeficiencia").append("<option>Hemiparesia</option>");
      $("#tipoDeficiencia").append("<option>Amputação</option>");
      $("#tipoDeficiencia").append("<option>Ostomia</option>");
  }else if(deficiencia=="Mental"){
      $("#tipoDeficiencia").append("<option>Comunicação</option>");
      $("#tipoDeficiencia").append("<option>Cuidados Pessoais</option>");
      $("#tipoDeficiencia").append("<option>Habilidades Sociais</option>");
      $("#tipoDeficiencia").append("<option>Independência na Locomação</option>");
      $("#tipoDeficiencia").append("<option>Saúde e Segurança</option>");
      $("#tipoDeficiencia").append("<option>Desempenho Escolar</option>");
      $("#tipoDeficiencia").append("<option>Lazer</option>");
      $("#tipoDeficiencia").append("<option>Trabalho</option>");
  }else if(deficiencia=="Visual"){
      $("#tipoDeficiencia").append("<option>Baixa Visão</option>");
      $("#tipoDeficiencia").append("<option>Proximo à Cegueira</option>");
      $("#tipoDeficiencia").append("<option>Cegueira Total</option>");
  }else if(deficiencia=="Auditiva"){
      $("#tipoDeficiencia").append("<option>Surdez</option>");
      $("#tipoDeficiencia").append("<option>Surdez Condutiva</option>");
      $("#tipoDeficiencia").append("<option>Surdez Sensorial</option>");
      $("#tipoDeficiencia").append("<option>Surdez Mista</option>");
      $("#tipoDeficiencia").append("<option>Surdez Central</option>");
  }
}


