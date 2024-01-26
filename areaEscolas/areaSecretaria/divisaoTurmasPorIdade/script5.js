    var valorPesquisado="";
    var idPMatricula="";
    var numeroTurmas  ="";
    var numeroInscritos=0
  $(document).ready(function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      entidade ="alunos";
      directorio = "areaSecretaria/divisaoTurmasPorIdade/";

      $("#luzingu").val(luzingu)
      $("#idPAnexo").val(idPAnexo)
      $("#turno").val(turno)

      numeroInscritos = listaAlunos.length
      numeroInscritosPorClasse();
      pegarTurmas();      

      $("#luzingu, #anosLectivos, #idPAnexo, #turno").change(function(){ 
        window.location ="?luzingu="+$("#luzingu").val()
        +"&idPAnexo="+$("#idPAnexo").val()+"&turno="+$("#turno").val();
      });

        $("#turma").change(function(){
          fazerPesquisa();
        })


        $("#formDivisaoTurmas").submit(function(){
            if($("#turma").val()== null){
              if(estadoExecucao=="ja"){
                estadoExecucao="aindaNao";
                numeroTurmas  = $("#numeroTurmas").val();
                dividirTurmas ();
              }                  
            }else{
              mensagensRespostas('#janelaPergunta', "Já há uma divisão de turmas nesta classe,"+
                    " se dividires de novo podes trocares as turmas de alguns alunos! pretendes continuar com a divisão?");
            } 
            return  false;
        });

        var rep=true;
        $("body").bind("mouseenter click", function(){
              rep=true;
            $("#janelaPergunta #pergSim").click(function(){
              if(rep==true){
                    fecharJanelaToastPergunta();
                    numeroTurmas  = $("#numeroTurmas").val();
                    dividirTurmas ();
                
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
          html += "<option value='"+dado.nomeTurma+"'>"+dado.nomeTurma
          +" ("+dado.designacaoTurma+")</option>";
        })
        $("#turma").html(html);
        $("#turmaTrocar").html(html);
        fazerPesquisa();
    }

    function fazerPesquisa(){
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

                  tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"+completarNumero(i)
                  +"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
                  +"'>"+dado.nomeAluno
                  +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
                  +"' class='lead black'>"+dado.numeroInterno
                  +"</a></td><td class='lead text-center'>"+dado.sexoAluno
                  +"</td><td class='lead text-center'>"
                  +calcularIdade(dado.dataNascAluno)+" Anos</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
                  "<a class='btn' action='reconfirmar' idPMatricula='"
                  +dado.idPMatricula+"' numeroInterno='"+dado.numeroInterno+"' grupo='"+dado.grupo+"' turma='"
                  +dado.reconfirmacoes.nomeTurma+"' title='Trocar a turma'><i class='fa fa-check'></i></a></div></td></tr>";
              } 
          });
        $("#listaAlunos").html(tbody)
    }

    function condition(elem, ind, obj){
      return (elem.reconfirmacoes.nomeTurma == $("#turma").val());
    } 

   


      
    var numeroAlunoNaTurma = new Array();
    var posicaoActual =1;
    var registrosJaCadastrados=0;
    var nomeTurma="";
    var x =new XMLHttpRequest();
    function dividirTurmas(){
      $(".modal").modal("hide");            
      var numeroPorTurma = Math.floor(numeroInscritos/numeroTurmas);
      var numeroSobrado = numeroInscritos - numeroPorTurma*numeroTurmas;

      for (var i=0; i<=numeroTurmas; i++){
         numeroAlunoNaTurma[i]=numeroPorTurma;
      }

      if(numeroSobrado!=0){
            for(var n=0; n<=numeroSobrado; n++){
                numeroAlunoNaTurma[n]=numeroAlunoNaTurma[n]+1;
            }
       }
            

          var estadoDivisao=true;
          var numeroVez=0;
          comeco = 1;
          registrosJaCadastrados+=numeroAlunoNaTurma[posicaoActual];
          final = numeroAlunoNaTurma[posicaoActual];
            irNoBanco();  
    }
    
    function irNoBanco(){
      chamarJanelaEspera("Criando a "+posicaoActual+".ª turma, por favor aguarde...");
      http.onreadystatechange = function(){
          if(http.readyState==4){         
            fecharJanelaEspera()
            
            if(posicaoActual<numeroTurmas){
                posicaoActual++; 
                comeco += numeroAlunoNaTurma[posicaoActual-1];
                registrosJaCadastrados+=numeroAlunoNaTurma[posicaoActual];
                final = registrosJaCadastrados;
                if(http.responseText.trim().substring(0,1)=="F"){
                   
                  estadoExecucao="ja";                                
                  $("#numeroTurmas").val(turmas.length);
                     mensagensRespostas('#mensagemErrada', http.responseText.trim().substring(1,http.responseText.length));
                }else{
                   irNoBanco();
                }                                                                                         
            }else{
                  registrosJaCadastrados =0;
                  posicaoActual = 1;
                  gravarTurmas("As turmas foram criadas com sucesso.");                                                     
            }
          }    
        }
        enviarComGet("tipoAcesso=dividirTurmas&classe="+classeP+"&idPCurso="+idCursoP
          +"&comeco="+comeco+"&final="+final+"&posTurmaDividir="+posicaoActual
          +"&idPAnexo="+idPAnexo+"&periodo="+periodo+"&turno="+$("#turno").val()
          +"&turmasFaltasParaDividir="+(numeroTurmas-posicaoActual));
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
                $("#trocarTurma").modal("hide");
                  mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
              }
          }    
        }
        enviarComGet("tipoAcesso=trocarTurmaAluno&idPMatricula="+idPMatricula+"&turma="
          +$("#trocarTurma #turmaTrocar").val()+"&classe="+classeP
          +"&idPCurso="+idCursoP+"&periodo="+periodo
          +"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo+"&grupoAluno="+grupoAluno);           
      }

      function  actualizarListaAlunos(mensagem=''){ 
        chamarJanelaEspera("...");
        http.onreadystatechange = function(){
          if(http.readyState==4){
            estadoExecucao="ja";
            fecharJanelaEspera()
            if(mensagem!=""){
               mensagensRespostas('#mensagemCerta', mensagem);                    
            }
            listaAlunos = JSON.parse(http.responseText.trim())
            pegarTurmas();                                 
          }    
        }
        enviarComGet("tipoAcesso=actualizarListaAlunosTurmas&classe="
            +classeP+"&idPCurso="+idCursoP
            +"&periodo="+periodo+"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo);                    
      }

      function gravarTurmas(mensagem=""){
        $(".modal").modal("hide");
        chamarJanelaEspera("...");
        http.onreadystatechange = function(){
            if(http.readyState==4){
              turmas = JSON.parse(http.responseText);
              actualizarListaAlunos(mensagem);
            }    
        }
        enviarComGet("tipoAcesso=gravarTurmas&classe="+classeP
          +"&idCurso="+idCursoP+"&periodo="+periodo+"&turno="+$("#turno").val()+"&idPAnexo="+idPAnexo);
      }

