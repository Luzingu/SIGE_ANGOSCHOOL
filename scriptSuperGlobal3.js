listaTodosMunicipios = new Array()
listaTodasComunas = new Array();
var estadoBuscaProvinciasMunicipios = false;

$(document).ready(function(){
   $("#rodapePrincipal").show()
   $("#containers .main-body").css("min-height", ($(window).height()-$("header").height()-100)+"px");
   $("#containers").css("min-height", ($(window).height()-$("header").height()-135)+"px");

    verfPrazo();
    var temporizadorVerfPrazo = setInterval(verfPrazo(), 6000);

    function verfPrazo(){
      var httpVerfPrazo = new XMLHttpRequest();
      httpVerfPrazo.onreadystatechange = function(){
          if(httpVerfPrazo.readyState==4){ 

            resultadoPrazo = httpVerfPrazo.responseText.trim();
            resultadoPrazo = resultadoPrazo.substring(0,6);

            if(resultadoPrazo=="server"){
                msgPrazo = httpVerfPrazo.responseText.substring(8, httpVerfPrazo.responseText.length);
                  
                if(msgPrazo=="V"){
                  clearInterval(temporizadorVerfPrazo);
                }else if(msgPrazo!=""){
                  fecharJanelaToastInformacao();
                  mensagensRespostas('#informacoes', "<p style='color:rgba(255, 255, 255, 0.8); text-align:justify;'>"
                    +msgPrazo+"</p>");
                }
            }  
          }
      }
      httpVerfPrazo.open("GET", enderecoArquivos+"/areaEscolas/areaDirector/cursos/manipulacaoDadosDoAjax.php?"+
        "tipoAcesso=verficarPrazoEscola", true);
      httpVerfPrazo.send();
    }
     $(".nav-tabs li").click(function(){
        $(".nav-tabs li").removeClass("active");
        $(this).addClass("active");
     })
})

  





$(".chamMenuInterno").click(function(){
    $("#subMenuInterno").toggle();
});


  

$("header a.chamadorListaMenuCima").click(function(){
  var cham = $(this).attr("chamar");      
    if($("header #"+cham).is(":visible")==true){
      $("header .listaMenuCima").hide();
    }else{
      if($("#estadoBloqueo").val()!="bloqueado"){
        $("header .listaMenuCima").hide();
        $("header #"+cham).show();
      }
      
    }     
  
  
});

$("section#main-content").click(function(){
  $("header .listaMenuCima").hide();
});

 $("table, .fotFoto").bind("mouseenter click mousemove", function(){
          $(".toolTipeImagem").hover(function(){
            
              var caminhoCompletoImagem =$(this).attr("imagem");
              if(caminhoCompletoImagem!=null && caminhoCompletoImagem!=undefined && caminhoCompletoImagem!=""){
                $(this).prepend("<div class='toolTipe' style='z-index: 1000;'><div class='imagem'><img src='"+enderecoArquivos+"/fotoUsuarios/"+
                caminhoCompletoImagem+"'></div></div>");
              }
              
          }, function(){
              $(".toolTipe").remove();
          }); 
  })

 var mensagemEspera ="Carregando...";
var paginacao = new Paginacao();
var jaTemPaginacao=false;

var entidade = "";
var nomeEntidadePesquisado ="";
var numeroEntidadePesquisado ="";

var idPrincipal="";
var action ="";
var posicaoNoArray =0;

function precherDataList(array, tipoLista){
  $("#listaOpcoes").empty();
  array.forEach(function(dado){
    if(tipoLista=="professores"){
      $("#listaOpcoes").append("<option value='"+dado.nomeEntidade
        +" - "+dado.numeroInternoEntidade+"'>");
    }else if(tipoLista=="alunos"){
       $("#listaOpcoes").append("<option value='"+dado.nomeAluno
        +" - "+dado.numeroInterno+"'>");
    }else if(tipoLista=="cursos"){
       $("#listaOpcoes").append("<option value='"+dado.nomeCurso+"'>");
    }else if(tipoLista=="disciplinas"){
       $("#listaOpcoes").append("<option value='"+dado.nomeDisciplina+"'>");
    }else if(tipoLista=="escolas"){
       $("#listaOpcoes").append("<option value='"+dado.nomeEscola
        +" - "+dado.numeroInternoEscola+"'>");
    }

  })
}

$(".pesquisaEntidade").bind("keyup select", function(){
  entidade = $(this).attr("tipoEntidade");
  numeroEntidadePesquisado ="";
  nomeEntidadePesquisado="";

  for(var i=0; i<=$(this).val().split("-").length-1; i++){
    if(i==0){
      nomeEntidadePesquisado = $(this).val().split("-")[0].trim();
    }
    if(i==1){
      numeroEntidadePesquisado = $(this).val().split("-")[1].trim();
    }
  }

  if(/^\d{1,}/.test($(this).val().trim())==true){
    nomeEntidadePesquisado="";
    numeroEntidadePesquisado= $(this).val();
  }
  fazerPesquisa();
});

function fazerPesquisaCondition(elem, ind, obj){
 if(entidade=="professores"){
  return ((elem.nomeEntidade.toLowerCase().indexOf(nomeEntidadePesquisado.toLowerCase().trim())>=0) && 
    (elem.numeroInternoEntidade.toLowerCase().indexOf(numeroEntidadePesquisado.toLowerCase().trim())==0));
 }else if(entidade=="alunos"){
    return ((elem.nomeAluno.toLowerCase().indexOf(nomeEntidadePesquisado.toLowerCase().trim())>=0) && 
    (elem.numeroInterno.toLowerCase().indexOf(numeroEntidadePesquisado.toLowerCase().trim())==0));
 }  
}



function accoes (idTabela, idFormulario="", entidadeMae="", mensagemPesqunta="", msgSalVar="Cadastrando...",
 msgEditar="Editando...", msgExcluir=""){

  var repet1=true;
      $(idTabela).bind("click mouseenter", function(){
        repet1=true;
          $(idTabela+" tr td .alteracao a").click(function(){            
            if(repet1==true){
              idPrincipal = $(this).attr("idPrincipal");
              action = $(this).attr("action");
              posicaoNoArray  = $(this).attr("posicaoNoArray");
               $(idFormulario+" input[idChave=sim]").val(idPrincipal);
              $(idFormulario+" input[name=action]").val(action);
              if(action==("editar"+entidadeMae)){
                mensagemEspera =msgEditar
                porValoresNoFormulario();
                $(idFormulario).modal("show");
              }else{
                mensagemEspera =msgExcluir;
                mensagensRespostas('#janelaPergunta', mensagemPesqunta);
              }          
              repet1=false;
            }              
          });
      });



      $(".novoRegistroFormulario").click(function(){
          mensagemEspera =msgSalVar
          limparFormulario(idFormulario)
          action = "salvar"+entidadeMae;
          $(idFormulario+ " input[name=action]").val(action);
          idPrincipal=-1;
          posicaoNoArray=-1;
          $(idFormulario).modal("show");
      });

      $(idFormulario).on("show.bs.modal", function(){
        $(idFormulario+" .mensagemErroFormulario").text("");
        if(action=="salvar"+entidadeMae){
          $(idFormulario+" .submitter").html("<i class='fa fa-user-plus'></i> Cadastrar");
        }else{
          $(idFormulario+" .submitter").html("<i class='fa fa-user-edit'></i> Editar");
        }
      });
}

function DataTables(tabela,botao, paginas = [30,50,90,100,1000, 2000]){
  
  if (botao=='sim') {
 $(tabela).DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,

      "buttons": ["copy", "csv", "excel", "pdf", "print"],
       "language": {
        
            "lengthMenu": "Mostrar _MENU_ ",
            "zeroRecords": "Nenhum dado encontrado para esta busca - lamento",
            "info": "Mostrando Paginas _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum Registo Disponivel",
            "infoFiltered": "(Filtrado de _MAX_ Registo total)",
            "print": "imprimir",
            "search": "Pesquisar",
            "paginate": { 
              "next": "Próximo",
               "previous": "Anterior",

             },
             "buttons": { 

              "pdf": "PDF",
              "csv": "CSV", 
              "excel": "Excel",
              "copy": "Copiar",
              "copyKeys": "Pressione ctrl ou u2318 + C para copiar os Arquivos", 
              "copyTitle": "Copiar para a Área de Transferência",
               "print": "Imprimir",

             },  

     
        },
      "lengthMenu": [paginas, paginas]
    }).buttons().container().appendTo(tabela+"_wrapper .col-md-6:eq(0)");
  }else{
    $(tabela).DataTable({
      "language": {
              "lengthMenu": "Mostrar _MENU_ Registo por Pagina",
              "zeroRecords": "Nenhum dado encontrado para esta busca - lamento",
              "info": "Mostrando Paginas _PAGE_ de _PAGES_",
              "infoEmpty": "Nenhum Registo Disponivel",
              "infoFiltered": "(Filtrado de _MAX_ Registo total)",
              "print": "imprimir",
              "search": "Pesquisar",
              "paginate": { 
                "next": "Próximo",
                 "previous": "Anterior"

               }
             
          },
    "lengthMenu": [[30, 50, -1], [30, 50, "Todos"]]
       
    });
  }
}


 function ordenarAlunoPorNome(array){
    var i=0;
    array.sort(function(a, b){return a.nomeAluno.toLowerCase() < b.nomeAluno.toLowerCase()?-1 : a.nomeAluno.toLowerCase() > b.nomeAluno.toLowerCase() ? 1:0;
    }).forEach(function(dado){
      dado.chave = i;
      i++;
    })
    return array;
  }

  function ordenarEntidadePorNome(array){
    var i=0;
    array.sort(function(a, b){return a.nomeEntidade.toLowerCase() < b.nomeEntidade.toLowerCase()?-1 : a.nomeEntidade.toLowerCase() > b.nomeEntidade.toLowerCase() ? 1:0;
    }).forEach(function(dado){
      dado.chave = i;
      i++;
    })
    return array;
  }


function sexoExtensa(s){
  if(s=="M"){
    return "Masculino";
  }else{
    return "Feminino";
  }
}


function vazioNull(d){
  if(d==null){
    return "";
  }else{
    return d;
  }
}

function completarNumero(n){
  if(n==null || n==undefined){
    return "";
  }else if(n<=9){
    return "0"+n;
  }else{
    return n;
  }
}

function classeExtensa(classe, sePorSemestre="nao", semiExtensa="nao"){
  if(classe==null || classe==undefined){
    return ""
  }else if(classe==0){
    return "Iniciação";
  }else if(classe=="*2"){
    return "Creche";
  }else if(classe=="*1"){
    return "Pré-Classe";
  }else{
    if(sePorSemestre=="sim"){
      if(semiExtensa=="nao"){
        if(classe==10){
          return "I Ano";
        }else if(classe==11){
          return "II Ano";
        }else if(classe==12){
          return "III Ano";
        }else if(classe==13){
          return "IV Ano";
        } 
      }else{
        if(classe==10){
          return "I";
        }else if(classe==11){
          return "II";
        }else if(classe==12){
          return "III";
        }else if(classe==13){
          return "IV";
        } 
      }
    }else{
      if(semiExtensa=="nao"){
        return classe+".ª Classe";   
      }else{
        return classe+".ª"; 
      }
    }
  }
}

  function fazerParagrafos(texto, todoTexto="sim", valMaximoTexto=300){

      if(texto==null || texto==undefined || texto==""){
        return "";
      }else{
        retorno = texto.replace(new RegExp('\r?\n','g'), '<br/>');
        if(todoTexto!="sim" && texto.length>valMaximoTexto){
          retorno = retorno.substring(0, valMaximoTexto)+" ...";
        }
        return retorno;
      }
   }

   function fazerParagrafosLongos(texto, todoTexto="sim", valMaximoTexto=300){

      if(texto==null || texto==undefined || texto==""){
        return "";
      }else{
        retorno = texto.replace(new RegExp('\r?\n','g'), '<br/><br/>');
        if(todoTexto!="sim" && texto.length>valMaximoTexto){
          retorno = retorno.substring(0, valMaximoTexto)+" <br/>...";
        }
        return retorno;
      }
   }

  function removerPlaceholder(){
      var wSize = jQuery(window).width();
      if(wSize>991){
        $(".modal form input").removeAttr("placeholder");
      }
      
    }

  function abreviarNomeCurso (curso){

    if(curso==null || curso==undefined || curso==""){
      return "";
    }else{
      var retorno ="";
      if(curso=="Técnico de Administração Pública"){
        retorno="ADP";
      }else if(curso=="Técnico de Contabilidade"){
        retorno="C";
      }else{
        if(curso.substring(0, 11)=="Técnico de "){
          curso = curso.substring(11, curso.length).trim();
        }
       var palavrasCursos = curso.split(" ");
        for(var i in palavrasCursos){
          if(palavrasCursos[i].length>=3){
              retorno +=palavrasCursos[i].substring(0, 1).toUpperCase();
          }
        }
        if(palavrasCursos.length==1 || curso.length<=3){
          retorno = curso.substring(0, 2).toUpperCase();
        }
      }        
        return retorno.trim();
    }  
  }

  function calcularIdade(dataNascimento){
    if(dataNascimento==null || dataNascimento==undefined){
      return "0";
    }else{
      var dataPadrao= /(\w{4})-(\w{2})-(\w{2})/;
      var anoEstudante = dataNascimento.replace(dataPadrao, "$1");
      var idade = ano - anoEstudante;
      return idade;
    }
    
  }

  function converterData(valor){
    if(valor==null || valor=="" || valor==undefined){
      return "";
    }else{
        var dataPadrao= /(\w{4})-(\w{2})-(\w{2})/;
        var dataRetorno = valor.replace(dataPadrao, "$3/$2/$1");
        return dataRetorno;
    }
   
  }

  function converterNumerosTresEmTres(numero){
      if(numero==null || numero==""){
        return 0;
      }else{
        numero= numero.toString().split(".");
        parteInteira="";
        parteDecimal="";
        for(var i in numero){
          if(i==0){
              parteInteira = numero[0];
          }else if(i==1){
              parteDecimal = numero[1];
          }
        }
        var numeroFormatado="";
        for(var i=parteInteira.length; i>0; i=i-3){
            numeroFormatado += "." + parteInteira.substring(i-3, i);
        }
        if(parteDecimal==""){
          parteDecimal="00"
        }
        if(parteDecimal!=""){
          parteDecimal =","+parteDecimal;
        }
        return (numeroFormatado.split(".").slice(1).reverse().join(".")+parteDecimal);
      }      
  }

  function diaSemana (dia){
      if(dia==1){
        return "Segunda-Feira";
      }else if(dia==2){
        return "Terça-Feira";
      }else if(dia==3){
        return "Quarta-Feira";
      }else if(dia==4){
        return "Quinta-Feira";
      }else if(dia==5){
        return "Sexta-Feira";
      }else if(dia==6){
        return "Sábado";
      }else if(dia==0){
        return "Domingo";
      }
   }

   function retornDataExtensa(data){
    if(data==null || data==undefined){
      return "";
    }else{
      var dataPadrao= /(\w{4})-(\w{2})-(\w{2})/;
      var ano = data.replace(dataPadrao, "$1");
      var mes = data.replace(dataPadrao, "$2");
      var dia = data.replace(dataPadrao, "$3");

      var mesExtenso="";
      
      if(mes==1){
          mesExtenso="Janeiro";
      }else if(mes==2){
          mesExtenso="Fevereiro";
      }else if(mes==3){
          mesExtenso="Março";
      }else if(mes==4){
          mesExtenso="Abril";
      }else if(mes==5){
          mesExtenso="Maio";
      }else if(mes==6){
          mesExtenso="Junho";
      }else if(mes==7){
          mesExtenso="Julho";
      }else if(mes==8){
          mesExtenso="Agosto";
      }else if(mes==9){
          mesExtenso="Setembro";
      }else if(mes==10){
          mesExtenso="Outubro";
      }else if(mes==11){
          mesExtenso="Novembro";
      }else if(mes==12){
          mesExtenso="Dezembro";
      }

      if(data=="0000-00-00" || data==null || data==""){
        return "";
      }else{
        return dia+" de "+mesExtenso+" de "+ano;
      }
    }
   }

   function retornarMesExtensa(mes){
    var mesExtenso="";
    if(mes==1){
        mesExtenso="Janeiro";
    }else if(mes==2){
        mesExtenso="Fevereiro";
    }else if(mes==3){
        mesExtenso="Março";
    }else if(mes==4){
        mesExtenso="Abril";
    }else if(mes==5){
        mesExtenso="Maio";
    }else if(mes==6){
        mesExtenso="Junho";
    }else if(mes==7){
        mesExtenso="Julho";
    }else if(mes==8){
        mesExtenso="Agosto";
    }else if(mes==9){
        mesExtenso="Setembro";
    }else if(mes==10){
        mesExtenso="Outubro";
    }else if(mes==11){
        mesExtenso="Novembro";
    }else if(mes==12){
        mesExtenso="Dezembro";
    }
    return mesExtenso;
   }


  
  function seAbrirMenu(){
    if(jQuery(window).width() <= 768) {
        closeMenu();
    }else{
        openMenu(); 
    }
  }

  function validarFormularios(identificadoJanela){
    $(identificadoJanela + " .discasPrenchimento").css("font-size", "10pt");
    $(identificadoJanela + " .discasPrenchimento").css("color", "red");

    $(identificadoJanela+" input").keydown(function(){
        $(this).css("border", "solid rgba(0,0,0,0.3) 1px");
        var id = $(this).attr("id");
        $(identificadoJanela+" ."+id).text("");
    });

     var retorno=true;
    //Campos vazios
    $(identificadoJanela+" .obrigatorio").each(function () {
        if ($(this).val() == "") {
            var id = $(this).attr("id");
            $(this).focus();
            $(identificadoJanela+" ."+id).text("Este Campo é Obrigatório!");
            retorno=false;
        }
    });

    //Cammpos Somentes letras
    var regex = /[!"#$|%�&/()=?:*;+<>,_��{[}�~-]/i;
    $(identificadoJanela+" .somenteLetras").each(function(){
        if ($(this).val()!="" && (regex.test($(this).val()) || /[\d]/i.test($(this).val()))){
            var id = $(this).attr("id");
            $(this).focus();
            $(identificadoJanela+" ."+id).text("Digite um Nome Válido!");
            retorno=false;
        }
    });

    //Somente para caixas que v�o aceitar letras e n�meros
    $(identificadoJanela+" .somenteLetrasNumeros").each(function(){
        if ($(this).val() !=""  && regex.test($(this).val())){
            var id = $(this).attr("id");
            $(this).focus();
            $(identificadoJanela+" ."+id).text("Este Campo não Pode Conter Caracteres Especiais!");
            retorno=false;
        }
    });

    /*$(identificadoJanela+" .numeroBI").each(function(){
      var numeroBI = $(this).val().trim();

      var provincia = $(identificadoJanela+" .nomeProvinciaBI").val().trim();
      if(provincia==null || provincia==undefined){
        provincia="";
      }
      var pais = $(identificadoJanela+" .nomePaisBI").val().trim();
      if(pais==null || pais==undefined){
        pais="";
      }

          var meio ="OE";
          if(pais!="Angola"){
            meio="OE";
          }else if(provincia=="Bié" || provincia=="BIÉ"){
            meio="BE";
          }else{
              if(/\w{1,}\s\w{1,}/i.test(provincia)==true){
                meio = provincia.replace(/(\w{1,})(\s)(\w{1,})/, "$1").substring(0, 1).toUpperCase()+
                provincia.replace(/(\w{1,})(\s)(\w{1,})/, "$3").substring(0, 1).toUpperCase();
              }else{
                meio = provincia.substring(0, 1).toUpperCase()+provincia.substring((provincia.length-1), provincia.length).toUpperCase();
              }
          }

        if(numeroBI!="" && (new RegExp("0\\w{8}"+meio+"0\\w{2}$").test(numeroBI)==false)){
          $(identificadoJanela+" ."+$(this).attr("id")).text("Número de BI Inválido!");
          $(this).focus();
          retorno=false;
        } 
    })*/

    $(identificadoJanela+" .numeroDeTelefone").each(function(){
      var numeroTelefone = $(this).val().trim();
      if(numeroTelefone!="" && /9\w{8}$/.test(numeroTelefone)==false){
          $(this).focus();
          $(identificadoJanela+" ."+$(this).attr("id")).text("Número de Telefone Inválido!");
          retorno=false;
      }  
    });
    return retorno;
}

 

function limparFormulario (identificadorJanela){
    $(identificadorJanela+ " .vazio").val("");
    $(identificadorJanela+ " input[type=date]").val("00/00/2019");
}

function pegarProvMunicComunDoPais(idPPais, idProvincia, idMunicipio, idComuna,
  valProvincia="", valMunicipio="", valComuna=""){

    var pegarProvMunicComunDoPais = new XMLHttpRequest();
    estadoBuscaProvinciasMunicipios = true;
    $(idProvincia+", "+idMunicipio+", "+idComuna).empty();
    $(idProvincia+", "+idMunicipio+", "+idComuna).append("<option value=''>Carregando...</option>"); 

    pegarProvMunicComunDoPais.onreadystatechange = function(){
        if(pegarProvMunicComunDoPais.readyState==4){

          estadoBuscaProvinciasMunicipios = false;

          $(idProvincia+", "+idMunicipio+", "+idComuna).empty();
          $(idProvincia+", "+idMunicipio+", "+idComuna).removeAttr("disabled")

            resultadoChadrackFinal = JSON.parse(pegarProvMunicComunDoPais.responseText.trim())
            var i=0; var idInicial=0;
            resultadoChadrackFinal[0].forEach(function(dado){
              $(idProvincia).append("<option value='"+dado.idPProvincia+"'>"+dado.nomeProvincia+"</option>");
              if((i==0 && valProvincia=="") || valProvincia==dado.idPProvincia){
                idInicial=dado.idPProvincia;
              }
              i++
            })
            if(valProvincia!=""){
              $(idProvincia).val(valProvincia)
            }

            var i=0; var idInicial2=0;
            listaTodosMunicipios = resultadoChadrackFinal[1];
            listaTodosMunicipios.forEach(function(dado){
              if(dado.idMunProvincia==idInicial){
                $(idMunicipio).append("<option value='"+dado.idPMunicipio+"'>"+dado.nomeMunicipio+"</option>");
              }
              if((i==0 && valMunicipio=="") || valMunicipio==dado.idPMunicipio){
                idInicial2=dado.idPMunicipio;
              }
              i++
            })
            if(valMunicipio!=""){
              $(idMunicipio).val(valMunicipio)
            }

            listaTodasComunas = resultadoChadrackFinal[2];
            listaTodasComunas.forEach(function(dado){
                if(dado.idComunMunicipio==idInicial2){
                  $(idComuna).append("<option value='"+dado.idPComuna+"'>"+dado.nomeComuna+"</option>");
                }
            })
            if(valComuna!=""){
              $(idComuna).val(valComuna)
            }
        }
    }
    pegarProvMunicComunDoPais.open("GET", enderecoArquivos+"/areaEscolas/areaDirector/cursos/manipulacaoDadosDoAjax.php?"+
      "tipoAcesso=pegarProvMunicComunDoPais&idPPais="+idPPais, true);
    pegarProvMunicComunDoPais.send();
  }

function selectProvincias (idPaises="", idProvincia="", idMunicipio="",
 idComuna="", carregarMunicProvinciaPais=true){

  if(carregarMunicProvinciaPais!=false){
    pegarProvMunicComunDoPais($(idPaises).val(), idProvincia, idMunicipio, idComuna)
  }
    
  $(idPaises).change(function(){
      pegarProvMunicComunDoPais($(idPaises).val(), idProvincia, idMunicipio, idComuna)
  })
  $(idProvincia).change(function(){
    listarMunicio(idProvincia, idMunicipio, idComuna)
  })
  $(idMunicipio).change(function(){
    listarComuna(idMunicipio, idComuna)
  })
}

function listarMunicio(idProvincia, idMunicipio, idComuna){
    $(idMunicipio).empty();
    listaTodosMunicipios.forEach(function(dado){
      if(dado.idMunProvincia==$(idProvincia).val()){
        $(idMunicipio).append("<option value='"+dado.idPMunicipio+"'>"+dado.nomeMunicipio+"</option>");
      }
    })
  listarComuna(idMunicipio, idComuna);
}

function listarComuna(idMunicipio, idComuna){
  $(idComuna).empty();
  listaTodasComunas.forEach(function(dado){
    if(dado.idComunMunicipio==$(idMunicipio).val()){
      $(idComuna).append("<option value='"+dado.idPComuna+"'>"+dado.nomeComuna+"</option>");
    }
  })
}



function passarValoresDaProvincia(idPais, idProvincia, idMunicipio, idComuna, 
    valPais, valProvincia, valMunicipio, valComuna){
    
    var temporizador=0;
    if(estadoBuscaProvinciasMunicipios==true){
      temporizador=10000;
    }
    window.setTimeout(function() {
      if(valPais!=null && valPais!="" && $(idPais).val()==valPais && estadoBuscaProvinciasMunicipios==false){
        $(idProvincia).val(valProvincia)
        listarMunicio(idProvincia, idMunicipio, idComuna)
        $(idMunicipio).val(valMunicipio)
        listarComuna(idMunicipio, idComuna)
        $(idComuna).val(valComuna)
      }else{
        $(idPais).val(valPais)
        pegarProvMunicComunDoPais(valPais, idProvincia, idMunicipio, idComuna, valProvincia, valMunicipio, valComuna);
      }
    }, temporizador);
}


function buscEstado(){
  if(estadoBuscaProvinciasMunicipios==false){
    return false
  }else{
    return true;
  }
}

function leitorPdf(arquivo, idCanvas, posicaoPagina=1){

  
  // Carrega o PDF
    const pdfUrl = arquivo;
    const container = document.getElementById(idCanvas);

    // Configura o leitor de PDF
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    loadingTask.promise.then(function (pdf) {
      // Carrega a primeira página do PDF
      pdf.getPage(posicaoPagina).then(function (page) {
        const scale = 1.5;
        const viewport = page.getViewport({ scale: scale });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        container.appendChild(canvas);

        // Renderiza a página do PDF no canvas
        const renderContext = {
          canvasContext: context,
          viewport: viewport
        };
        page.render(renderContext);
      });
    });
}

function gerarCapaLivro (arquivo, idCapa){
  var image=""
  pdfjsLib.getDocument(arquivo).promise.then(function(pdf) {
    // Carregar a primeira página do PDF
    pdf.getPage(1).then(function(page) {
      // Renderizar a página em um canvas HTML
      var viewport = page.getViewport({ scale: 1 });
      var canvas = document.createElement('canvas');
      var canvasContext = canvas.getContext('2d');
      canvas.width = viewport.width;
      canvas.height = viewport.height;

      var renderTask = page.render({ canvasContext, viewport });

      renderTask.promise.then(function() {
        // Obter a imagem do canvas
        image = canvas.toDataURL();
        $("#"+idCapa).attr("src",image)
      });
    });
  });
}

