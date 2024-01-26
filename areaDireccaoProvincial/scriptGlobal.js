
  function retornarPeriodo(valor) {
    if(valor=="pos"){
      return "Pós-Laboral"; 
    }else{
      return "Regular"
    }
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
      }

  }

function classeExtensa(classe){
  if(classe==null || classe==undefined){
    return ""
  }else if(classe==0){
    return "Iniciação";
  }else{
    return classe+"ª Classe";
  }
}


