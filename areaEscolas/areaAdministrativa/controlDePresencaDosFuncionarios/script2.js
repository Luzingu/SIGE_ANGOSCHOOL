var idPAvalDesEnt="";
window.onload = function (){
   directorio = "areaAdministrativa/controlDePresencaDosFuncionarios/";
   	entidade ="professores";
    fecharJanelaEspera();
    seAbrirMenu();
    $("#anosLectivos").val(idPAno);
    $("#mes").val(mes);
    $("#ordemSemana").val(ordemSemana);
    fazerPesquisa()
    $(".btnAlterarNotas").hide(200)

    $("#tabDados").bind("click mouseenter", function(){
        $("#tabDados select").change(function(){
            $(this).attr("alterou", "sim")

            $(".btnAlterarNotas").show(200)
        })
    })
    

    $(".openLink").click(function(){
        window.location = "../../relatoriosPdf/mapasProfessores/"+$(this).attr("arquivo")
        +"?anoCivil="+anoCivil+"&tamanhoFolha="+$("#tamanhoFolha").val()
        +"&diaInicial="+$("#diaInicial").val()+"&mesInicial="+$("#mesInicial").val()
        +"&diaFinal="+$("#diaFinal").val()+"&mesFinal="+$("#mesFinal").val()
    })

    $("#actualizar").click(function(){
        gravarlistaAgentes()
    })
    $("#anosLectivos, #mes, #ordemSemana").change(function(){
        window.location ="?idPAno="+$("#anosLectivos").val()
        +"&mes="+$("#mes").val()+"&ordemSemana="+$("#ordemSemana").val();
    });

    $(".btnAlterarNotas").click(function(){
        irmaoLuzingu();
    })

     function irmaoLuzingu(){
        valoresEnviar = new Array();

        var nomeCampoComErroEncontrado="";
        var msgErro="";
         $("#tabDados select[alterou=sim]").each(function(){
            valoresEnviar.push({
              idPEntidade:$(this).attr("idPEntidade"),
              diaSemana:$(this).attr("diaSemana"), 
              data:$(this).attr("name"), 
              falta:$("#"+$(this).attr("id")+" option:selected").attr("numFalta"), 
              presenca:$(this).val()
            })
            
         })
         $("#dados").val(dados)
         manipularNotas()
      }


  }


function fazerPesquisa(){
    var tbody = "";
    var i=0;
      $("#numTProfessores").text(completarNumero(listaAgentes.length))
      var numTFemininos=0;

    listaAgentes.forEach(function(dado){
          i++;
          
          tbody +='<tr  idPEntidade="'+dado.idPEntidade+'">'+
          '<td class="text-center">'+completarNumero(i)
          +'</td><td class="toolTipeImagem" imagem="'+dado.fotoEntidade
          +'">'+dado.nomeEntidade+'</strong></td>'

            var contDadta=0
            datas.forEach(function(data){
                contDadta++
                if(data.diaSemana==1 || contDadta==1){
                   // tbody +='<div class="row">';
                }
                tbody +='<td class="text-center"><select type="number"'+
                '  style="font-size:14pt; font-weight:bolder;" name="'+data.data+'" idPEntidade="'+
                dado.idPEntidade+'" id="'+dado.idPEntidade+'-'+data.data+'" class="text-center inputVal lead"'+
                '>';
                if(seDiaDoFeriado(data.data)==true){
                    tbody +="<option value='F' numFalta='0-0' "+mamaPolina(dado.controlPresenca, "F", data.data)+">F</option>"
                }else if(data.diaSemana==6 || seDiaDaActividade(data.data)==true){
                    tbody +="<option value='P' numFalta='0-0' "+mamaPolina(dado.controlPresenca, "P", data.data)+">P</option>"
                    tbody +="<option value='NP' numFalta='T-6' "+mamaPolina(dado.controlPresenca, "NP", data.data)+">Não P</option>"
                }else{
                    var totTempos = filtradorTempo(dado.idPEntidade, data.diaSemana);
                    if(totTempos==0){
                        tbody +="<option value='DP' numFalta='0-0' "+mamaPolina(dado.controlPresenca, "DP", data.data)+">DP</option>"
                        tbody +="<option value='P' numFalta='0-0' "+mamaPolina(dado.controlPresenca, "P", data.data)+">P</option>"
                        tbody +="<option value='NP' numFalta='T-6' "+mamaPolina(dado.controlPresenca, "NP", data.data)+">Não P</option>"
                    }else{
                        tbody +="<option value='"+totTempos+"' numFalta='0-0' "+mamaPolina(dado.controlPresenca, totTempos, data.data)+">"+totTempos+"</option>"
                    }
                    for(var i=(totTempos-1); i>=1; i--){
                        tbody +="<option value='"+i+"' numFalta='0-"+(totTempos-i)+"' "+mamaPolina(dado.controlPresenca, i, data.data)+">"+i+"</option>"
                    }
                    if(totTempos>0){
                        tbody +="<option value='0' numFalta='T-"+totTempos+"' "+mamaPolina(dado.controlPresenca, 0, data.data)+">0</option>"
                    }                    
                }
                tbody +='</select></td>'
                if(data.diaSemana==6 || contDadta==datas.length){
                    //tbody +='</div>';
                }
            })

            tbody +='</form>';

    });
    $("#tabDados").html(tbody)
}

function seDiaDaActividade(data){
    var retorno=false;
    if(diasDasActividades!="" && diasDasActividades!=null){
        diasDasActividades.split(";").forEach(function(dado){
            if(converterData(data)==dado.trim()){
                retorno = true
            }
        })
    }
    return retorno;
}

function seDiaDoFeriado(data){
    var retorno=false;
    if(diasDosFeriados!="" && diasDosFeriados!=null){
        diasDosFeriados.split(";").forEach(function(dado){
            if(converterData(data)==dado.trim()){
                retorno = true
            }
        })
    }
    return retorno;
}

function filtradorTempo(idPEntidade, diaSemana){
    var contador=0;
    horario.forEach(function(dado){
        if(dado.idPEntidade==idPEntidade && dado.dia==diaSemana){
            contador++
        }
    }) 
    return contador;
}

function mamaPolina(controlPresenca, valor, data){
    var retorno="";
    if(controlPresenca!=undefined){
        controlPresenca.forEach(function(dado){
            if(dado.data==data && dado.presencas==valor && dado.idEscola==idEscolaLogada){
                retorno="selected";
            }
        })
    }
    return retorno;
}

function manipularNotas(){
    chamarJanelaEspera("")
      http.onreadystatechange = function(){
        if(http.readyState==4){
            estadoExecucao ="ja";
            fecharJanelaEspera();
            resultado = http.responseText.trim()
            $(".btnAlterarNotas").hide(200)
            if(resultado.substring(0, 1)=="F"){
                mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
            }else if(resultado!=""){
                mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso.")
                listaAgentes = JSON.parse(resultado);
                fazerPesquisa();
            }
        }    
      }
    $("#formularioDados #action").val("manipularControlFaltas")
    $("#formularioDados #dados").val(JSON.stringify(valoresEnviar))
    var form = new FormData(document.getElementById("formularioDados"))
    enviarComPost(form)
  }


