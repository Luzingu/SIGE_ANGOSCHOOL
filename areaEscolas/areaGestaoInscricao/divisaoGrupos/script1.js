    var valorPesquisado="";
    var idPMatricula="";
    var numeroGrupos  ="";
  window.onload = function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      entidade ="alunos";
     directorio = "areaGestaoInscricao/divisaoGrupos/";

      $("#curso").val(idCurso);
 
      porNumeroInscritos();
      pegarGrupos();
      fazerPesquisa();

      $("#curso").change(function(){
        window.location ="?idCurso="+$("#curso").val();          
      });

      $("#grupo").change(function(){
        fazerPesquisa();
      })

      $("#pesquisaAluno").keyup(function(){
        fazerPesquisa();
      })

      $(".visualizadorLista").click(function(){
          window.location =caminhoRecuar+"relatoriosPdf/relatoriosInscricao/"+
          $(this).attr("referencia")+"?idPCurso="+idCurso+"&grupo="+$("#grupo").val()
      })

      $("#formDivisaoTurmas").submit(function(){

          if($("#grupo").val()== null){
            if(estadoExecucao=="ja"){
              estadoExecucao="aindaNao";
              numeroGrupos  = $("#numeroGrupos").val();
              dividirGrupos();
            }                  
          }else{
            mensagensRespostas('#janelaPergunta', "Já há uma divisão de grupos neste grupo,"+
                  " se dividires de novo podes trocares os grupos de alguns alunos. pretendes continuar com a divisão?");
          } 
            return  false;
        });

        var rep=true;
        $("body").bind("mouseenter click", function(){
              rep=true;
            $("#janelaPergunta #pergSim").click(function(){
              if(rep==true){
                    fecharJanelaToastPergunta();
                    numeroGrupos  = $("#numeroGrupos").val();
                    dividirGrupos ();
                
                rep=false;
              }         
          })
        }) 

        var repet1=true;
        $("#listaAlunos").bind("click mouseenter", function(){
            repet1=true;
             $("#listaAlunos tr td .alteracao a").click(function(){
                if(repet1==true){
                 idPAluno = $(this).attr("idPAluno");
                  $("#trocarGrupo #nomeAluno").val($(this).attr("nomeAluno"));
                  $("#trocarGrupo #grupoTrocar").val($(this).attr("grupo"));
                  $("#trocarGrupo #Cadastrar").html('<i class="fa fa-check"></i> Trocar');
                  $("#trocarGrupo").modal("show");

                  repet1=false;
                }
             });
        });

        $("#trocarGrupo form").submit(function(){
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
           trocarGrupoDoAluno();
          }
          return false;
        });
  }

    function porNumeroInscritos(){
        $("#numInscritos").text(completarNumero(numeroInscritos)); 
        if(numeroInscritos<=26){
          $("form#formDivisaoTurmas #numeroGrupos").attr("max", numeroInscritos);
        }else{
          $("form#formDivisaoTurmas #numeroGrupos").attr("max", 26);
        }
    }

     function pegarGrupos(){
          var html="";
        $("#numeroGrupos").val(grupos.length);
        grupos.forEach(function(dado){
          html += "<option value='"+dado.numeroGrupo+"'>Grupo nº "+completarNumero(dado.numeroGrupo)+"</option>";
        })
        $("#grupo").html(html);
        $("#grupoTrocar").html(html);
        listaAlunos = ordenarAlunoPorNome (listaAlunos);
        fazerPesquisa();
    }

    function fazerPesquisa(){

            if(jaTemPaginacao==false){
              paginacao.baraPaginacao(listaAlunos.filter(condition).length, 50);
            }else{
                jaTemPaginacao=false;
            }
            var i=paginacao.comeco;
            var contagem=-1;

              $("#numTotalAlunos").text(completarNumero(listaAlunos.filter(condition).length));
              $("#numTMasculinos").text(0);

          var numM=0;
          var tbody="";
          listaAlunos.filter(condition).forEach(function(dado){

              contagem++;
              if(dado.sexoAluno=="F"){
                      numM++;
                  }
                  $("#numTMasculinos").text(completarNumero(numM));
              if(contagem>=paginacao.comeco && contagem<=paginacao.final){
                  i++;
                  var s = dado.sexoAluno;
                  if(s=="M"){
                    s="<i class='fa fa-male'></i>"
                  }else{
                    s="<i class='fa fa-female'></i>";
                  }
                  var idade = calcularIdade(dado.dataNascAluno);
                  tbody +="<tr id='linha"+dado.idPAluno+"'><td class='lead text-center'>"+completarNumero(i)
                  +"</td><td class='lead' ><a class='black' href='"+caminhoRecuar+"areaGestaoInscricao/relatorioAluno?idPAluno="+dado.idPAluno+"'>"
                  +dado.nomeAluno
                  +"</a></td><td class='lead text-center'>"+s+"</td><td class='lead text-center'>"+
                  vazioNull(dado.telefoneAluno)+"</td><td class='lead text-center'>"
                  +idade+" Anos</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn' action='reconfirmar' idPAluno='"
                  +dado.idPAluno+"' grupo='"
                  +dado.grupo.grupoNumero+"' nomeAluno='"+dado.nomeAluno+"' title='Trocar o Grupo'><i class='fa fa-check'></i></a></div></td></tr>";
              } 
          });
        $("#listaAlunos").html(tbody)
    }

    function condition(elem, ind, obj){
        return (elem.grupo.grupoNumero == $("#grupo").val() && 
          elem.nomeAluno.indexOf($("#pesquisaAluno").val())>=0);    
    } 

      var numeroAlunoNaTurma = new Array();
      var posicaoActual =1;
      var registrosJaCadastrados=0;
      var grupoNumero="";

    var x =new XMLHttpRequest();

     function dividirGrupos(){
            $(".modal").modal("hide");            
            var numeroPorTurma = Math.floor(numeroInscritos/numeroGrupos);
            var numeroSobrado = numeroInscritos - numeroPorTurma*numeroGrupos;

                for (var i=0; i<=numeroGrupos; i++){
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

            grupoNumero =  posicaoActual;
            irNoBanco();  
    }
    
    function irNoBanco(){
                chamarJanelaEspera("Criando o grupo nº "+grupoNumero+", por favor aguarde...");
              http.onreadystatechange = function(){
                    if(http.readyState==4){
                          fecharJanelaEspera(); 
                          if(posicaoActual<numeroGrupos){
                              posicaoActual++; 
                              comeco += numeroAlunoNaTurma[posicaoActual-1];
                              registrosJaCadastrados+=numeroAlunoNaTurma[posicaoActual];
                              final = registrosJaCadastrados;
                              grupoNumero =  posicaoActual;
                              if(http.responseText.trim().substring(0,1)=="F"){
            
                                estadoExecucao="ja";                                
                                $("#numeroGrupos").val(grupos.length);
                                   mensagensRespostas('#mensagemErrada', http.responseText.trim().substring(1,http.responseText.length));
                              }else{
                                 irNoBanco();
                              }                                                                                         
                          }else{
                                registrosJaCadastrados =0;
                                posicaoActual = 1;
                                gravarGrupos("Os grupos foram criados com sucesso.", "nao");                                                     
                          }
                    }    
                }
                enviarComGet("tipoAcesso=dividirGrupos&idPCurso="+$("#curso").val()
                  +"&comeco="+comeco+"&final="+final+"&grupoNumero="+grupoNumero
                  +"&gruposFaltasParaDividir="+(numeroGrupos-posicaoActual));
    } 


      function trocarGrupoDoAluno(){
        $("#trocarGrupo #Cadastrar").html('<i class="fa fa-spinner fa-spin"></i> Trocando...');
          http.onreadystatechange = function(){
              if(http.readyState==4){
                   resultado = http.responseText.trim()    
                  if(resultado.substring(0,1)=="V") {
                    gravarGrupos(resultado.substring(1,resultado.length), "nao");        
                  }else{
                    estadoExecucao="ja";
                    $(".modal").modal("hide");
                      mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
                  }
              }    
          }
          enviarComGet("tipoAcesso=trocarGrupoAluno&idPAluno="+idPAluno+"&numeroGrupo="
            +$("#trocarGrupo #grupoTrocar").val()+"&idPCurso="+$("#curso").val());        
      }

      function  actualizarListaAlunos(mensagem='', possoListarTodos="sim"){ 
          chamarJanelaEspera("...");
          http.onreadystatechange = function(){
              if(http.readyState==4){
                estadoExecucao="ja";
                fecharJanelaEspera();
                listaAlunos = JSON.parse(http.responseText);                
                if(mensagem!=""){
                   mensagensRespostas('#mensagemCerta', mensagem);                    
                }
                pegarGrupos();                                 
              }    
          }
          enviarComGet("tipoAcesso=actualizarListaAlunosGrupos&idPCurso="+$("#curso").val())
      }

      function gravarGrupos(mensagem="", possoListarTodos="sim"){
          chamarJanelaEspera("...");
          $(".modal").modal("hide");
          http.onreadystatechange = function(){
              if(http.readyState==4){
                grupos = JSON.parse(http.responseText);
                actualizarListaAlunos(mensagem, possoListarTodos);
              }    
          }
          enviarComGet("tipoAcesso=gravarGrupos&idPCurso="+$("#curso").val())

      }


      
