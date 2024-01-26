    var valorPesquisado="";
    var idPMatricula="";
    var numeroTurmas  ="";
  $(document).ready(function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      entidade ="alunos";
      directorio = "areaSecretaria/divisaoTurmasPersonalizada/";

      $("#luzingu").val(luzingu);      
      $("#idPAnexo").val(idPAnexo)
      $("#turno").val(turno)

      pegarTotalInscritosSemTurmas();
      pegarTurmas();      

      $("#luzingu, #idPAnexo, #turno").change(function(){ 
        window.location ="?luzingu="+$("#luzingu").val()
        +"&idPAnexo="+$("#idPAnexo").val()+"&turno="+$("#turno").val();
      });

      $("#linguaEstangeira, #disciplinaOpcao").change(function(){
         pegarTotalInscritosSemTurmas();
      })

      $("#btnResetTurma").click(function(){
        mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes "+
          "excluir todas turmas deste período?");
      })

        $("#turma").change(function(){
          fazerPesquisa();
        });

        var rep=true;
        $("body").bind("mouseenter click", function(){
              rep=true;
            $("#janelaPergunta #pergSim").click(function(){
              if(rep==true){
                    fecharJanelaToastPergunta();
                    resetTurmas ();
                
                rep=false;
              }         
          })
        }) 

        var repet1=true;
        $("#listaAlunos").bind("click mouseenter", function(){
            repet1=true;
             $("#listaAlunos tr td .alteracao a").click(function(){
                if(repet1==true){
                  
                 idPMatricula = $(this).attr("idPMatricula");
                 grupoAluno = $(this).attr("grupo");
                  $("#trocarTurma #numeroInterno").val($(this).attr("numeroInterno"));
                  $("#trocarTurma #turmaTrocar").val($(this).attr("turma"));
                  $("#trocarTurma #Cadastrar").html('<i class="fa fa-check"></i> Trocar');
                  $("#trocarTurma").modal("show");
                                  
                  repet1=false;
                }
             });
        });

        $("#trocarTurma form").submit(function(){
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
           trocarTurmaDoAluno();
          }
          return false;
        });

        $("#formCriarTurmas").submit(function(){
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
           criarNovaTurma();
          }
          return false;
        })
  })

    function numeroInscritosPorClasse(){
        $("#numInscritos").text(completarNumero(numeroInscritos)); 
        if(numeroInscritos<=26){
          $("form#formDivisaoTurmas #numeroTurmas").attr("max", numeroInscritos);
        }else{
          $("form#formDivisaoTurmas #numeroTurmas").attr("max", 26);
        }
    }

     function pegarTurmas(){
          var html="";
        $("#numeroTurmas").val(turmas.length);
        turmas.forEach(function(dado){
            
            var discOpcao1="";
          var discOpcao2="";
        
        if(dado.atributoTurma!=null && dado.atributoTurma!=undefined && dado.atributoTurma!=""){

          var atributoT = dado.atributoTurma.toString().split("-");
          if(atributoT.length==1){
              discOpcao1 = nomeDisciplina(atributoT[0]);
          }else if(atributoT.length==2){
              discOpcao1 = nomeDisciplina(atributoT[0]);
              if(discOpcao1!=""){
                  discOpcao1+="-";
              }
              discOpcao2 +=nomeDisciplina(atributoT[1]);
          }
        }
          html += "<option value='"+dado.nomeTurma+"'>"+dado.nomeTurma+" ("+dado.designacaoTurma+") ["+discOpcao1+discOpcao2+"]"+"</option>";
        })
        $("#turma").html(html);
        $("#turmaTrocar").html(html);
        fazerPesquisa();
    }

    function fazerPesquisa(){
      var tabMatriculados = document.getElementById("");
      var tbody = "";
      if(jaTemPaginacao==false){
        paginacao.baraPaginacao(listaAlunos.filter(condition).filter(fazerPesquisaCondition).length, 50);
      }else{
          jaTemPaginacao=false;
      }
      var i=paginacao.comeco;
      var contagem=-1;

      $("#numTotalAlunos").text(completarNumero(listaAlunos.filter(fazerPesquisaCondition).filter(condition).length));
      $("#numTMasculinos").text(0);

      var numM=0;
      var tbody="";
      listaAlunos.filter(condition).filter(fazerPesquisaCondition).forEach(function(dado){
        contagem++;
        if(dado.sexoAluno=="F"){
                numM++;
        }
        $("#numTMasculinos").text(completarNumero(numM));
        
        if(contagem>=paginacao.comeco && contagem<=paginacao.final){
          i++;
          var seCadeirante = "";
          if(dado.classeCadeirante!="" && dado.classeCadeirante!=null){
            seCadeirante=" (Cadeirante)";
          }

          tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"+
          completarNumero(i)
          +"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
          +"'>"+dado.nomeAluno +seCadeirante
          +"</td><td class='lead text-center'><a href='"+caminhoRecuar+
          "areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
          +"' class='lead black'>"+dado.numeroInterno
          +"</a></td><td class='lead text-center'>"+dado.sexoAluno
          +"</td><td class='lead text-center'>"
          +calcularIdade(dado.dataNascAluno)+" Anos</td><td class='text-center'>"+
          "<div class='btn-group alteracao text-right'>"+
          "<a class='btn' action='reconfirmar' idPMatricula='"
          +dado.idPMatricula+"' numeroInterno='"+dado.numeroInterno+"' grupo='"+dado.grupo+"' turma='"
          +dado.reconfirmacoes.nomeTurma+"' title='Trocar a turma'><i class='fa fa-check'>"+
          "</i></a></div></td></tr>";
        } 
      });
      $("#listaAlunos").html(tbody)
    }
    function condition(elem, ind, obj){
      return (elem.reconfirmacoes.nomeTurma == $("#turma").val() && $("#turma").val()!=null);
    }

    function pegarTotalInscritosSemTurmas(){
      chamarJanelaEspera("");      
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));        
          }else{
            $("#numInscritosPendentes").html("<strong>"+completarNumero(resultado)
              +" Aluno(s)</strong>")
            $("#numeroAlunosTurma").attr("max", resultado);
            $("#numeroAlunosTurma").val("");             
          }       
          
        }    
      }
      enviarComGet("tipoAcesso=pegarTotalInscritos&classe="+classeP
      +"&idPCurso="+idCursoP+"&periodo="+periodo+"&turno="+$("#turno").val()
      +"&linguaEstangeira="+$("#linguaEstangeira").val()
      +"&disciplinaOpcao="+$("#disciplinaOpcao").val()
      +"&idPAnexo="+idPAnexo);           
    }

    function resetTurmas(){
      chamarJanelaEspera("");      
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));        
          }else{
            gravarTurmas();
            mensagensRespostas('#mensagemCerta', "Todas turmas foram excluídas com sucesso.");
                                       
          }       
        }    
      }
      enviarComGet("tipoAcesso=resetTurmas&classe="+classeP+"&idPCurso="+idCursoP
        +"&periodo="+periodo+"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo);           
    }

    function criarNovaTurma(){
      chamarJanelaEspera("");      
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));        
          }else{
            gravarTurmas();
            mensagensRespostas('#mensagemCerta', "A turma foi criada com sucesso.");     
          } 
        }    
      }
      enviarComGet("tipoAcesso=criarNovaTurma&classe="+classeP
      +"&idPCurso="+idCursoP+"&periodo="+periodo+"&turno="+$("#turno").val()
      +"&linguaEstangeira="+$("#linguaEstangeira").val()
      +"&disciplinaOpcao="+$("#disciplinaOpcao").val()
      +"&numeroAlunosTurma="+$("#numeroAlunosTurma").val()+"&idPAnexo="+idPAnexo);          
    }
      
      function trocarTurmaDoAluno(){
        $("#trocarTurma #Cadastrar").html('<i class="fa fa-spinner fa-spin"></i> Trocando...');
        http.onreadystatechange = function(){
          if(http.readyState==4){
               resultado = http.responseText.trim()
              if(resultado.substring(0,1)=="V") {
                gravarTurmas(resultado.substring(1,resultado.length), "nao");        
              }else{
                estadoExecucao="ja";
                $(".modal").modal("hide");
                  mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
              }
          }    
        }
        enviarComGet("tipoAcesso=trocarTurmaAluno&idPMatricula="+idPMatricula+"&turma="
          +$("#trocarTurma #turmaTrocar").val()+"&classe="+classeP
          +"&idPCurso="+idCursoP+"&periodo="+periodo+"&turno="+$("#turno").val()
          +"&linguaEstangeira="+linguaEstangeira
          +"&disciplinaOpcao="+disciplinaOpcao+"&idPAnexo="+idPAnexo+"&grupoAluno="+grupoAluno);           
      }

      function  actualizarListaAlunos(mensagem=''){ 
        chamarJanelaEspera("Actualizando as listas...");
        http.onreadystatechange = function(){
            if(http.readyState==4){
              estadoExecucao="ja";
              fecharJanelaEspera();
              if(mensagem!=""){
                 mensagensRespostas('#mensagemCerta', mensagem);                    
              }
              listaAlunos = JSON.parse(http.responseText.trim())

              pegarTotalInscritosSemTurmas();
              pegarTurmas();                                 
            }    
        }
        enviarComGet("tipoAcesso=actualizarListaAlunosTurmas&classe="
          +classeP+"&idPCurso="+idCursoP
          +"&periodo="+periodo+"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo);                    
      }

      function gravarTurmas(mensagem=""){
        $(".modal").modal("hide");
        chamarJanelaEspera("Actualizando a lista...");
        http.onreadystatechange = function(){
            if(http.readyState==4){
              turmas = JSON.parse(http.responseText);
              actualizarListaAlunos(mensagem);
            }    
        }
        enviarComGet("tipoAcesso=gravarTurmas&classe="+classeP+"&idCurso="+
        idCursoP+"&periodo="+periodo+"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo);
      }

      function nomeDisciplina (id){
        var nome="";
        disciplinasDeOpcao.forEach(function(dado){
          if(dado.idPNomeDisciplina==id){
            nome = dado.nomeDisciplina;
          }
        })
        return nome;
      }

