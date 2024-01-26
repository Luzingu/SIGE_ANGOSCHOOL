<?php function adicionarAgentes($manipulador, $idPEscola){
  echo "<script>var idDaEscolaReferencia ='".$idPEscola."'</script>";
?>
  <div class="row">
    <div class="col-lg-12 col-md-19" id="pesqUsario">
          <div class="form-group input-group col-lg-12 col-md-12">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="search" class="form-control lead" placeholder="Pesquisar Agente (Nome, BI ou Número Interno)..." id="pesqAgentes">
          </div>  
    </div>
  </div>

  <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover" >
          <thead>
              <tr>
                  <th class="lead text-center"></th>
                  <th class="lead text-center"><strong>Número Interno</strong></th>
                  <th class="lead text-center"><strong>BI</strong></th>
                  <th class="lead text-center" style="min-width: 30px;"></th>
              </tr>
          </thead>
          <tbody id="tabProfessores">
          </tbody>
      </table>
  </div>

  <div class="modal fade" id="dadosFuncionario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <form class="modal-dialog" id="dadosFuncionarioForm" method="POST">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-plus"></i> Funcionário</h4>
          </div>

          <div class="modal-body">
              <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                      <label>Nome do Funcionário:</label>
                        <select id="idPEntidade" class="form-control lead" name="idPEntidade">
                          
                        </select>
                    </div>
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 lead">
                    <label>Inicio das Funções</label>
                    <input type="date" required value="<?php echo $manipulador->dataSistema ?>" name="dataInicioFuncoesEntidade" id="dataInicioFuncoesEntidade" class="form-control">
                  </div>
                  <div class="col-lg-8 col-md-8 lead">
                    <label>Função</label>
                    <input type="text" required name="funcaoEnt" id="funcaoEnt" list="listaFuncoes" class="form-control">
                    <datalist id="listaFuncoes">
                        <option>Director</option>
                        <option>Subdirector Pedagógico</option>
                        <option>Subdirectora Administrativa</option>
                        <option>Coordenador de Turno</option>
                        <option>Coordenador do Giva</option>
                        <option>Coordenador de Área</option>
                        <option>Coordenador de Curso </option>
                        <option>Chefe de Secretaria</option>
                        <option>Chefe de Secretaria Pedagógica</option>
                        <option>Chefe de Secretaria Administrativa</option>
                        <option>Coordenador de Educação Física e Desporto Escolar</option>
                        <option>Coordenador de Actividade Extracular Círculo de Interesse e Extra-Escolar</option>
                        <option>Coordenador de Disciplina</option>
                        <option>Coordenador de Área Disciplinar</option>
                        <option>Professor</option>
                        <option>Auxiliar Administrativo de Limpeza</option>
                        <option>Auxiliar de Limpeza</option>
                      </datalist>
                  </div>
                </div>
                <div class="row">                    
                   <div class="col-lg-7 col-md-7 lead">
                    <label class="lead" for="funcaoProf">Nível de Acesso:</label>
                    <select class="form-control" required id="nivelSistemaEntidade" name="nivelSistemaEntidade">
                      <?php 
                        $tipoInstituicao = $manipulador->selectUmElemento("escolas", "tipoInstituicao", ["idPEscola"=>$idPEscola]);
                        foreach($manipulador->selectArray("cargos", ["idPCargo", "designacaoCargo"], ["instituicao"=>$tipoInstituicao], [], "", [], array("designacaoCargo"=>1)) as $cargo){

                          echo "<option value='".$cargo["idPCargo"]."'>".$cargo["designacaoCargo"]."</option>";
                        }
                        if($_SESSION['idUsuarioLogado']==35){
                          echo "<option value='0'>Usuário Master</option>";
                        }
                       ?> 
                    </select>
                   </div>

                    <div class="col-lg-5 col-md-5 lead"><label class="lead" for="naturezaVinc">Natureza do Vínculo:</label>
                      <select type="text" required class="form-control vazio" id="naturezaVinc" name="naturezaVinc">
                        <option>Provimento Provisório</option>
                        <option>Pessoal Do Quadro</option>
                        <option>Comissão De  Serviço</option>
                        <option>Eventual</option>
                        <option>Colaborador</option>
                      </select>
                    </div>
                </div> 

            </div>
            <input type="hidden" id="action" name="action" value="adicionarAgente">
            <input type="hidden" id="idDaEscolaReferencia" name="idDaEscolaReferencia" value="<?php echo $idPEscola; ?>">

          <div class="modal-footer">
              <div class="row">
                <div class="col-lg-3 col-md-3 text-left">
                  <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-plus-circle"></i> Adicionar</button>
                </div>                    
              </div>                
          </div>
        </div>
      </form>
  </div>
  <script type="text/javascript">
    var agentesEncontrados = new Array();
    var idPEntidade="";
    $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      $("#pesqAgentes").keyup(function(){
        pesquisarAgentesNaoOcupados();
      })
      $("#dadosFuncionarioForm").submit(function(){
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao"
          manipular();
        }
        return false;
      })

      var rep=true
        $("#tabProfessores").bind("click mouseenter", function(){
          var rep=true
          $("#tabProfessores tr .alteracao").click(function(){
            if(rep==true){
              idPEntidade = $(this).attr("idPEntidade")
              $("#dadosFuncionario #idPEntidade").html("")
              agentesEncontrados.forEach(function(dado){
                  if(dado.idPEntidade==idPEntidade){
                    $("#dadosFuncionario #idPEntidade").html("<option value='"+
                      dado.idPEntidade+"'>"+dado.nomeEntidade+"</option>")
                    $("#dadosFuncionario #nivelSistemaEntidade").val("Professor")
                  }
              })
              $("#dadosFuncionario").modal("show");
              rep=false;
            }
          })
        })
    })

    function pesquisarAgentesNaoOcupados(){
      enviarComGet("tipoAcesso=pesquisarAgentesNaoOcupados&valorPesquisado="
        +$("#pesqAgentes").val()+"&idDaEscolaReferencia="+idDaEscolaReferencia);
      $("#tabProfessores").html("<tr><td colspan='5' class='text-center'>Buscando...</td></tr>")
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          agentesEncontrados = JSON.parse(resultado)
          listar();
        }
      }
    }
    function manipular(){
        $("#dadosFuncionario").modal("hide")
        chamarJanelaEspera("")
       http.onreadystatechange = function(){
          if(http.readyState==4){
            resultado = http.responseText.trim()
            estadoExecucao="ja";
            if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
            }else{
              mensagensRespostas("#mensagemCerta", "O(a) Agente foi adicionado(a) com suceso.");
              pesquisarAgentesNaoOcupados();
            }
          }
        }
        var form = new FormData(document.getElementById("dadosFuncionarioForm"));
       enviarComPost(form);
    }

    function listar(){
        var tbody = "";
        agentesEncontrados.forEach(function(dado){
          tbody +="<tr><td class='lead toolTipeImagem' imagem='"+dado.fotoEntidade+"'>"+dado.nomeEntidade
          +"</td><td class='lead text-center'>"+dado.numeroInternoEntidade
          +"</td><td class='lead text-center'>"+vazioNull(dado.biEntidade)
          +"</td><td class='lead text-center'><a href='#' class='btn btn-success alteracao"+
          "' idPEntidade='"+dado.idPEntidade+"' title='Adicionar'><i class='fa fa-sign-out-alt'></i></a></td></tr>";
      });
      $("#tabProfessores").html(tbody)
    }

  </script>
  
<?php } 

  function pesquisarAgentesNaoOcupados($manipulador){
      $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
      $idDaEscolaReferencia = isset($_GET["idDaEscolaReferencia"])?$_GET["idDaEscolaReferencia"]:"";
      $arrayRetorno=array();
      $manipulador->conDb("escola", true);
      
      foreach($manipulador->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "fotoEntidade", "escola.idEntidadeEscola", "escola.estadoActividadeEntidade"], ['$or'=>[array("nomeEntidade"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("biEntidade"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("numeroInternoEntidade"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado)))]], [], 100, [], array("nomeEntidade"=>1)) as $a){

          if(count(listarItensObjecto($a, "escola", ["estadoActividadeEntidade=A", "idEntidadeEscola=".$idDaEscolaReferencia]))<=0){
              $arrayRetorno[]=$a;
          }
          
      }
      echo json_encode($arrayRetorno);
  }

  function adicionarAgente($manipulador){
      $idPEntidade = isset($_POST["idPEntidade"])?$_POST["idPEntidade"]:"";
      $idDaEscolaReferencia = isset($_POST["idDaEscolaReferencia"])?$_POST["idDaEscolaReferencia"]:"";
      $dataInicioFuncoesEntidade = isset($_POST["dataInicioFuncoesEntidade"])?$_POST["dataInicioFuncoesEntidade"]:"";
      $funcaoEnt = isset($_POST["funcaoEnt"])?$_POST["funcaoEnt"]:"";
      $nivelSistemaEntidade = isset($_POST["nivelSistemaEntidade"])?$_POST["nivelSistemaEntidade"]:"";
      $naturezaVinc = isset($_POST["naturezaVinc"])?$_POST["naturezaVinc"]:"";
      if($naturezaVinc=="Colaborador"){
        $efectividade="F";
      }else{
        $efectividade="V";
      }
      if($idDaEscolaReferencia==4){
        $manipulador->conDb("teste", true);
      }
      $manipulador->inserirObjecto("entidadesprimaria", "escola", "idP_Escola", "nivelSistemaEntidade, idFEntidade, idEntidadeEscola, estadoActividadeEntidade, chaveEnt, funcaoEnt, dataInicioFuncoesEntidade, tipoPessoal", [$nivelSistemaEntidade, $idPEntidade, $idDaEscolaReferencia, "A", $idPEntidade."-".$idDaEscolaReferencia, $funcaoEnt, $dataInicioFuncoesEntidade, "docente"], ["idPEntidade"=>$idPEntidade]);


      $manipulador->editarItemObjecto("entidadesprimaria", "escola", "estadoActividadeEntidade, funcaoEnt, dataInicioFuncoesEntidade, nivelSistemaEntidade, naturezaVinc, efectividade, tipoPessoal", ["A", $funcaoEnt, $dataInicioFuncoesEntidade, $nivelSistemaEntidade, $naturezaVinc, $efectividade, "docente"], ["idPEntidade"=>$idPEntidade], ["idEntidadeEscola"=>$idDaEscolaReferencia]);
  } ?>