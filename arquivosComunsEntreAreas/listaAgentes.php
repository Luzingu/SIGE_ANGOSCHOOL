<?php
  function indexAgente($manipulador, $idPEscola){
     echo "<script>var idDaEscolaReferencia ='".$idPEscola."'</script>";

    ?>
    <table id="example1" class="table table-striped table-bordered table-hover" >
        <thead class="corPrimary">
          <tr>
            <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
            <th class="lead font-weight-bolder "><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
            <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
             <th class="lead font-weight-bolder"><strong>Tipo de Agente</strong></th>
            <th class="lead font-weight-bolder"><strong>Função</strong></th>
            <th class="lead text-center" style="min-width: 30px;"></th>
          </tr>
        </thead>
        <tbody id="tabProfessores">
        </tbody>
    </table>
    <?php
      $readOnly="";
      if(valorArray($manipulador->sobreUsuarioLogado, "privacidadeEscola")!="Pública"){
        $readOnly="readonly";
      }
    ?>

    <div class="modal fade" id="formularioAgentes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <form class="modal-dialog" id="formularioAgentesForm" method="POST">
      <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-plus"></i> Agentes</h4>
            </div>

            <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-9 col-md-9 lead">
                      <label>Nome do Funcionário:</label>
                        <input type="text" required name="nomeEntidade" id="nomeEntidade" class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-3">
                      <label class="lead" for="dataEBIEntidade">Sexo</label>
                      <select name="sexoEntidade" class="form-control data" id="sexoEntidade">
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label class="lead" for="numeroTelefoneEntidade">Telefone:</label>
                       <input type="text" name="numeroTelefoneEntidade" class="form-control numeroDeTelefone vazio" id="numeroTelefoneEntidade" required autocomplete="off" maxlength="12" >
                       <div class="numeroTelefoneEntidade discasPrenchimento lead"></div>
                    </div>
                      <div class="col-lg-8 col-md-8 lead">
                       <label>E-mail</label>
                       <input type="email" name="emailEntidade" id="emailEntidade" class="form-control">
                     </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <label class="lead" for="tipoPessoal">Pessoal:</label>
                         <select name="tipoPessoal" class="form-control lead fa-border vazio" id="tipoPessoal">
                           <option value="docente">Docente</option>
                            <option value="naoDocente">Não Docente</option>
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
                      <div class="col-lg-4 col-md-4 lead">
                       <label class="lead" for="funcaoProf">Nível de Acesso:</label>
                       <select class="form-control" required id="nivelSistemaEntidade" name="nivelSistemaEntidade">

                         <?php
                         $tipoInstituicao = $manipulador->selectUmElemento("escolas", "tipoInstituicao", ["idPEscola"=>$idPEscola]);
                         foreach($manipulador->selectArray("cargos", ["idPCargo", "designacaoCargo"], ["instituicao"=>$tipoInstituicao], [], "", [], array("designacaoCargo"=>1)) as $a){
                             echo '<option class="lead" value="'.$a["idPCargo"].'">'.$a["designacaoCargo"].'</option>';

                         }
                         if ($_SESSION['idUsuarioLogado']==35){ ?>
                           <option class="lead" value="0">Usuário_Master</option>
                         <?php } ?>
                         <option class="lead" value="">Sem Acesso</option>
                       </select>
                      </div>
                  </div>
                  <div class="row">
                     <div class="col-lg-8 col-md-8 lead">
                      <label>Função</label>
                      <input type="text" required name="funcaoEnt" id="funcaoEnt" class="form-control">
                    </div>
                    <div class="col-lg-4 col-md-4 docente efetivo">
                        <label for="valorAuferidoNaEducacao">Salário (Estado):</label><input type="number" step="0.001" class="form-control lead vazio text-center" id="valorAuferidoNaEducacao" name="valorAuferidoNaEducacao" >
                    </div>
                  </div>
                  <div class="row">

                      <div class="col-lg-4 col-md-4 docente efetivo">
                        <label for="valorAuferidoNaInstituicao">Salário Báse (Instituição):</label><input type="number"  step="0.001" class="form-control lead vazio text-center" id="valorAuferidoNaInstituicao" name="valorAuferidoNaInstituicao" >
                      </div>

                    <div class="col-lg-4 col-md-4 docente efetivo">
                      <label for="pagamentoPorTempo">
                        Pagamento/Tempo
                      </label>
                      <input type="number"  step="0.001" class="form-control lead vazio text-center" id="pagamentoPorTempo" name="pagamentoPorTempo">
                    </div>
                  </div>

                </div>
              <input type="hidden" name="idPEntidade" id="idPEntidade">
              <input type="hidden" name="idDaEscolaReferencia" id="idDaEscolaReferencia" value="<?php echo $idPEscola; ?>">
               <input type="hidden" name="action" id="action">

            <div class="modal-footer">
                <div class="row">
                  <div class="col-lg-3 col-md-3 text-left">
                    <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-plus-circle"></i> Cadastrar</button>
                  </div>
                </div>
            </div>
          </div>
        </form>
    </div>


<script type="text/javascript">
  $(document).ready(function(){
    seAbrirMenu();
    fecharJanelaEspera();

      fazerPesquisa();

      DataTables("#example1","sim")

      $(".visualizadorLista").click(function(){
        window.location =caminhoRecuar+'relatoriosPdf/mapasProfessores/'+
        $(this).attr("id")+'.php?tamanhoFolha='
        +$("#tamanhoFolha").val();
      })

      $("#tipoDisciplinaProfessor").change(function(){
        if($(this).val()!=""){
          window.location =caminhoRecuar+'relatoriosPdf/mapasProfessores/'+
          'professoresDeTipoDisciplina.php?tamanhoFolha='
          +$("#tamanhoFolha").val()+"&tipoDisciplina="+$(this).val();
        }
      })
      $("#periodoProfessor").change(function(){
        if($(this).val()!=""){
          window.location =caminhoRecuar+'relatoriosPdf/mapasProfessores/'+
          'mapaForcaTrabalho.php?tamanhoFolha='
          +$("#tamanhoFolha").val()+"&periodoProfessor="+$(this).val();
        }
      })

      var rep=true
      $("#tabProfessores").bind("click mouseenter", function(){
        var rep=true
        $("#tabProfessores tr .alteracao").click(function(){
          if(rep==true){
            idPrincipal = $(this).attr("idPrincipal")
            $("#formularioAgentes #action").val($(this).attr("action"))
            $("#formularioAgentes #idPEntidade").val(idPrincipal)
            if($(this).attr("action")=="editarAgente"){
              porValoresNoFormulario();
              $("#formularioAgentes").modal("show")
            }else{
              mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular este agente?");
            }
            rep=false;
          }
        })
      })
      $("#novoAgente").click(function(){
        $("#formularioAgentes #action").val("adicionarAgente")
        limparFormulario("#formularioAgentes")
        $("#formularioAgentes").modal("show")
      })

      $("#formularioAgentes form").submit(function(){
        if(estadoExecucao=="ja"){
          if(validarFormularios("#formularioAgentes form")==true){
            estadoExecucao="espera";
            manipularEntidade();
          }
        }
          return false;
      });

      var rep=true;
      $("body").bind("mouseenter click", function(){
            rep=true;
          $("#janelaPergunta #pergSim").click(function(){
            if(rep==true){
                 if(estadoExecucao=="ja"){
                  estadoExecucao="espera";
                  idEspera ="#janelaPergunta #pergSim";
                  fecharJanelaToastPergunta()
                  manipularEntidade();
                }

              rep=false;
            }
        })
      })
  })
  function porValoresNoFormulario(){
    listaEntidades.forEach(function(dado){
      if(dado.idPEntidade==idPrincipal){
        $("#formularioAgentes #nomeEntidade").val(dado.nomeEntidade)
        $("#formularioAgentes #dataNascEntidade").val(dado.dataNascEntidade)
        $("#formularioAgentes #biEntidade").val(dado.biEntidade)
        $("#formularioAgentes #dataEBIEntidade").val(dado.dataEBIEntidade)
        $("#formularioAgentes #dataCaducBI").val(dado.dataCaducBI)
        $("#formularioAgentes #emailEntidade").val(dado.emailEntidade)
        $("#formularioAgentes #numeroTelefoneEntidade").val(dado.numeroTelefoneEntidade)
        $("#formularioAgentes #sexoEntidade").val(dado.generoEntidade)
        $("#formularioAgentes #dataInicioFuncoesEntidade").val(dado.escola.dataInicioFuncoesEntidade)
        $("#formularioAgentes #funcaoEnt").val(dado.escola.funcaoEnt)
        $("#formularioAgentes #nivelSistemaEntidade").val(dado.escola.nivelSistemaEntidade)
        $("#formularioAgentes #naturezaVinc").val(dado.escola.naturezaVinc)
        $("#formularioAgentes #tipoPessoal").val(dado.escola.tipoPessoal)



        $("#formularioAgentes #pagamentoPorTempo").val(dado.escola.pagamentoPorTempo)
        $("#formularioAgentes #valorAuferidoNaInstituicao").val(dado.escola.valorAuferidoNaInstituicao)
        $("#formularioAgentes #valorAuferidoNaEducacao").val(dado.valorAuferidoNaEducacao)
      }
    })
  }

  function manipularEntidade(){
    chamarJanelaEspera("");
     var form = new FormData(document.getElementById("formularioAgentesForm"));
     enviarComPost(form);
    $("#formularioAgentes").modal("hide");
     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          estadoExecucao="ja";
          fecharJanelaEspera();
          if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
            listaEntidades = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
  }

  function fazerPesquisa(){

      var tbody = "";
        var contagem=-1;
        $("#numTProfessores").text(completarNumero(listaEntidades.length))
      listaEntidades.forEach(function(dado){
        contagem++;

        var agente = vazioNull(dado.escola.tipoPessoal);
        if(agente=="docente"){
          agente="Docente";
        }else if(agente=="naoDocente"){
          agente="Não Docente";
        }else if(agente=="empresa"){
          agente="Empresa"
        }
        tbody +="<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoEntidade+"'>"+dado.nomeEntidade
        +"</td><td class='lead text-center'>"+dado.numeroInternoEntidade
        +"</td><td class='lead'>"+agente+"</td><td class='lead'>"+vazioNull(dado.escola.funcaoEnt)+"</td><td class='text-center'>"+
        "<div class='btn-group text-right'><a class='btn btn-success alteracao' title='Editar' href='#' action='editarAgente'"+
        " idPrincipal='"+dado.idPEntidade+"'><i class='fa fa-pen'></i></a>&nbsp;&nbsp;<a class='btn btn-danger alteracao' title='Excluir' href='#' action='excluirAgente'"+
        " idPrincipal='"+dado.idPEntidade+"'><i class='fa fa-times'></i></a></div></td></tr>";
      });
      $("#tabProfessores").html(tbody);
  }
</script>
<?php }

class manipuladorDadosAgente{

  public $db;
  function __construct($db){
    $this->db = $db;
    $this->idPEntidade = isset($_POST['idPEntidade'])?$_POST['idPEntidade']:"";
    if($this->idPEntidade=="" || $this->idPEntidade==NULL){
      $this->idPEntidade=$_SESSION['idUsuarioLogado'];
    }

    $this->idDaEscolaReferencia = isset($_POST["idDaEscolaReferencia"])?$_POST["idDaEscolaReferencia"]:$_SESSION["idEscolaLogada"];
    if($this->idDaEscolaReferencia==4){
      $this->db->conDb("teste", true);
    }


    $dadosAnterior = $this->db->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPEntidade, "escola.idEntidadeEscola"=>$this->idDaEscolaReferencia], ["escola"]);

    if($this->db->accao=="adicionarAgente"){
      $dadosAnterior=array();
    }

    $this->nomeEntidade = isset($_POST["nomeEntidade"])?limpadorEspacosDuplicados($_POST["nomeEntidade"]):valorArray($dadosAnterior, "nomeEntidade");

    $this->sexoEntidade = isset($_POST["sexoEntidade"])?$_POST["sexoEntidade"]:valorArray($dadosAnterior, "sexoEntidade");
    $this->nivelAcademicoEntidade = isset($_POST["nivelAcademicoEntidade"])?$_POST["nivelAcademicoEntidade"]:valorArray($dadosAnterior, "nivelAcademicoEntidade");

    $this->dataNascEntidade = isset($_POST["dataNascEntidade"])?$_POST["dataNascEntidade"]:valorArray($dadosAnterior, "dataNascEntidade");
    $this->pais = isset($_POST["pais"])?$_POST["pais"]:valorArray($dadosAnterior, "paisNascEntidade");
    $this->provincia = isset($_POST["provincia"])?$_POST["provincia"]:valorArray($dadosAnterior, "provNascEntidade");
    $this->municipio = isset($_POST["municipio"])?$_POST["municipio"]:valorArray($dadosAnterior, "municNascEntidade");
    $this->comuna = isset($_POST["comuna"])?$_POST["comuna"]:valorArray($dadosAnterior, "comunaNascEntidade");
    $this->biEntidade = isset($_POST["biEntidade"])?$_POST["biEntidade"]:valorArray($dadosAnterior, "biEntidade");
    $this->dataEBIEntidade = isset($_POST["dataEBIEntidade"])?$_POST["dataEBIEntidade"]:valorArray($dadosAnterior, "dataEBIEntidade");

    $this->paiEntidade = isset($_POST["paiEntidade"])?limpadorEspacosDuplicados($_POST["paiEntidade"]):valorArray($dadosAnterior, "paiEntidade");
    $this->maeEntidade = isset($_POST["maeEntidade"])?limpadorEspacosDuplicados($_POST["maeEntidade"]):valorArray($dadosAnterior, "maeEntidade");
    $this->numeroTelefoneEntidade = isset($_POST["numeroTelefoneEntidade"])?$_POST["numeroTelefoneEntidade"]:valorArray($dadosAnterior, "numeroTelefoneEntidade");

    $this->emailEntidade = isset($_POST["emailEntidade"])?$_POST["emailEntidade"]:valorArray($dadosAnterior, "emailEntidade");
    $this->numeroAgenteEntidade = isset($_POST["numeroAgenteEntidade"])?$_POST["numeroAgenteEntidade"]:valorArray($dadosAnterior, "numeroAgenteEntidade");
    $this->estadoAcesso = isset($_POST["estadoAcesso"])?$_POST["estadoAcesso"]:valorArray($dadosAnterior, "estadoAcesso");
    $this->categoriaEntidade = isset($_POST["categoriaEntidade"])?$_POST["categoriaEntidade"]:valorArray($dadosAnterior, "categoriaEntidade", "escola");

    $this->nomeBanco = isset($_POST["nomeBanco"])?$_POST["nomeBanco"]:valorArray($dadosAnterior, "nomeBanco", "escola");
    $this->numeroContaBancaria = isset($_POST["numeroContaBancaria"])?$_POST["numeroContaBancaria"]:valorArray($dadosAnterior, "numeroContaBancaria", "escola");
    $this->ibanContaBancaria = isset($_POST["ibanContaBancaria"])?$_POST["ibanContaBancaria"]:valorArray($dadosAnterior, "ibanContaBancaria", "escola");

    $this->cursoEnsinoMedio = isset($_POST["cursoEnsinoMedio"])?$_POST["cursoEnsinoMedio"]:valorArray($dadosAnterior, "cursoEnsinoMedio");
    $this->escolaEnsinoMedio = isset($_POST["escolaEnsinoMedio"])?$_POST["escolaEnsinoMedio"]:valorArray($dadosAnterior, "escolaEnsinoMedio");
    $this->cursoLicenciatura = isset($_POST["cursoLicenciatura"])?$_POST["cursoLicenciatura"]:valorArray($dadosAnterior, "cursoLicenciatura");
    $this->escolaLicenciatura = isset($_POST["escolaLicenciatura"])?$_POST["escolaLicenciatura"]:valorArray($dadosAnterior, "escolaLicenciatura");
    $this->cursoMestrado = isset($_POST["cursoMestrado"])?$_POST["cursoMestrado"]:valorArray($dadosAnterior, "cursoMestrado");
    $this->escolaMestrado = isset($_POST["escolaMestrado"])?$_POST["escolaMestrado"]:valorArray($dadosAnterior, "escolaMestrado");
    $this->cursoDoutoramento = isset($_POST["cursoDoutoramento"])?$_POST["cursoDoutoramento"]:valorArray($dadosAnterior, "cursoDoutoramento");

    $this->escolaDoutoramento = isset($_POST["escolaDoutoramento"])?$_POST["escolaDoutoramento"]:valorArray($dadosAnterior, "escolaDoutoramento");

    $this->dataCaducBI  = isset($_POST["dataCaducBI"])?$_POST["dataCaducBI"]:valorArray($dadosAnterior, "dataCaducBI");
    $this->funcaoEnt = isset($_POST["funcaoEnt"])?$_POST["funcaoEnt"]:valorArray($dadosAnterior, "funcaoEnt", "escola");
    $this->dataInicioFuncoesEntidade = isset($_POST["dataInicioFuncoesEntidade"])?$_POST["dataInicioFuncoesEntidade"]:valorArray($dadosAnterior, "dataInicioFuncoesEntidade", "escola");
    $this->dataInicOutraEsc = isset($_POST["dataInicOutraEsc"])?$_POST["dataInicOutraEsc"]:valorArray($dadosAnterior, "dataInicOutraEsc", "escola");

    $this->pagamentoPorTempo = isset($_POST["pagamentoPorTempo"])?$_POST["pagamentoPorTempo"]:valorArray($dadosAnterior, "pagamentoPorTempo", "escola");

    $this->tempoServOutraEsc = isset($_POST["tempoServOutraEsc"])?$_POST["tempoServOutraEsc"]:valorArray($dadosAnterior, "tempoServOutraEsc", "escola");

    $this->dataInicEduc = isset($_POST["dataInicEduc"])?$_POST["dataInicEduc"]:valorArray($dadosAnterior, "dataInicEduc");
    $this->numSegSocial = isset($_POST["numSegSocial"])?$_POST["numSegSocial"]:valorArray($dadosAnterior, "numSegSocial");
    $this->numDespacho = isset($_POST["numDespacho"])?$_POST["numDespacho"]:valorArray($dadosAnterior, "numDespacho");

    $this->dataDespacho = isset($_POST["dataDespacho"])?$_POST["dataDespacho"]:valorArray($dadosAnterior, "dataDespacho");
    $this->naturezaVinc = isset($_POST["naturezaVinc"])?$_POST["naturezaVinc"]:valorArray($dadosAnterior, "naturezaVinc", "escola");
     $this->tipoPessoal = isset($_POST["tipoPessoal"])?$_POST["tipoPessoal"]:valorArray($dadosAnterior, "tipoPessoal", "escola");

     $this->valorAuferidoNaEducacao = isset($_POST["valorAuferidoNaEducacao"])?$_POST["valorAuferidoNaEducacao"]:valorArray($dadosAnterior, "valorAuferidoNaEducacao");

    $this->valorAuferidoNaInstituicao = isset($_POST["valorAuferidoNaInstituicao"])?$_POST["valorAuferidoNaInstituicao"]:valorArray($dadosAnterior, "valorAuferidoNaInstituicao", "escola");
    $this->comFormPedag = isset($_POST["comFormPedag"])?"V":"F";

    $this->comMagisterio = isset($_POST["comMagisterio"])?"V":"F";

    $this->tambemColaboradorNaInstituicao = isset($_POST["tambemColaboradorNaInstituicao"])?"V":"F";

    $this->cargoPedagogicoEnt =  isset($_POST["cargoPedagogicoEnt"])?$_POST["cargoPedagogicoEnt"]:valorArray($dadosAnterior, "cargoPedagogicoEnt", "escola");

    $this->nivelSistemaEntidade =  isset($_POST["nivelSistemaEntidade"])?$_POST["nivelSistemaEntidade"]:valorArray($dadosAnterior, "nivelSistemaEntidade", "escola");

    $this->formatoDocumento = isset($_POST["formatoDocumento"])?$_POST["formatoDocumento"]: valorArray($dadosAnterior, "formatoDocumentoEnt");

    $this->ibanContaBancaria = isset($_POST["ibanContaBancaria"])?$_POST["ibanContaBancaria"]: valorArray($dadosAnterior, "ibanContaBancaria", "escola");

    $this->fotoEntidade = $this->db->upload("fotoEntidade", "foto_".$this->idPEntidade.$this->db->segundos, 'fotoUsuarios', "../../../", valorArray($dadosAnterior, "fotoEntidade"));
  }

  public function adicionarAgente(){
    $jaExistemNumero="V";
    while ($jaExistemNumero=="V"){
          $characters= "123456789";
          $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS00".substr(str_shuffle($characters),0, 2);
          if(count($this->db->selectArray("entidadesprimaria", [], ["numeroInternoEntidade"=>$numeroUnico]))<=0){
            $jaExistemNumero="F";
          }
    }
    if($this->naturezaVinc=="Colaborador"){
      $this->efectividade="F";
    }else{
      $this->efectividade="V";
    }
    $this->fotoEntidade = $this->db->upload("fotoEntidade", "foto_".$this->idPEntidade, 'fotoUsuarios', "../../../", "default.png");

    if(seTudoMaiuscula($this->nomeEntidade)){
        echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
    }if(count($this->db->selectArray("entidadesprimaria", ["idPEntidade"], ["numeroTelefoneEntidade"=>$this->numeroTelefoneEntidade]))>0){
      echo "FEJá existe um agente  no AngoSchool com este número de telefone.";
    }else if($this->db->inserir("entidadesprimaria", "idPEntidade", "tituloNomeEntidade, nomeEntidade, numeroTelefoneEntidade, generoEntidade, formatoDocumentoEnt, fotoEntidade, idEntCadastrou, numeroInternoEntidade, senhaEntidade, estadoAcessoEntidade, valorAuferidoNaEducacao, emailEntidade", [$this->nomeEntidade, $this->nomeEntidade, $this->numeroTelefoneEntidade, $this->sexoEntidade, "pdf", $this->fotoEntidade, $_SESSION['idUsuarioLogado'], $numeroUnico, "0c7".criptografarMd5("0000")."ab", "A", $this->valorAuferidoNaEducacao, $this->emailEntidade])=="sim"){

      $this->idPEntidade = $this->db->selectUmElemento("entidadesprimaria", "idPEntidade", ["numeroInternoEntidade"=>$numeroUnico]);

      $this->db->inserirObjecto("entidadesprimaria", "escola", "idP_Escola", "funcaoEnt, dataUltimaActualizacao, naturezaVinc, tipoPessoal, estadoActividadeEntidade, idFEntidade, idEntidadeEscola, chaveEnt, nivelSistemaEntidade, pagamentoPorTempo, valorAuferidoNaInstituicao", [$this->funcaoEnt, $this->db->dataSistema, $this->naturezaVinc, $this->tipoPessoal, "A", $this->idPEntidade, $this->idDaEscolaReferencia, $this->idPEntidade."-".$this->idDaEscolaReferencia, $this->nivelSistemaEntidade, $this->pagamentoPorTempo, $this->valorAuferidoNaInstituicao], ["idPEntidade"=>$this->idPEntidade]);

        if(!isset($_POST['editadoNoPerfilEntidade'])){
          $this->listar();
        }
    }else{
      echo "FNão foi possível adicionar o funcionário.";
    }
  }



  public function editarAgente(){

      $this->efectividade="";
      if($this->naturezaVinc=="Colaborador"){
        $this->efectividade="F";
      }else{
        $this->efectividade="V";
      }
      $this->idDaEscolaReferencia = isset($_POST["idDaEscolaReferencia"])?$_POST["idDaEscolaReferencia"]:$_SESSION["idEscolaLogada"];

    if(seTudoMaiuscula($this->nomeEntidade)){
        echo "FOs dados não podem ser todos em letras maiúsculas. Digite bem os dados.";
    }else if($this->biEntidade!="" && count($this->db->selectArray("entidadesprimaria", ["idPEntidade"], ["biEntidade"=>$this->biEntidade, "idPEntidade"=>array('$ne'=>(int)$this->idPEntidade)]))>0){
      echo "FJá existe um agente cadastrado no AngoSchool com este Número de Bilhete de Identidade.";
    }else if($this->numeroAgenteEntidade!="" && count($this->db->selectArray("entidadesprimaria", ["idPEntidade"], ["numeroAgenteEntidade"=>$this->numeroAgenteEntidade, "idPEntidade"=>array('$ne'=>(int)$this->idPEntidade)]))>0){
      echo "FJá existe um agente cadastrado no AngoSchool com este Número de Agente.";
    }else if($this->numeroTelefoneEntidade!="" && count($this->db->selectArray("entidadesprimaria", ["idPEntidade"], ["numeroTelefoneEntidade"=>$this->numeroTelefoneEntidade, "idPEntidade"=>array('$ne'=>(int)$this->idPEntidade)]))>0){
      echo "FJá existe um agente cadastrado no AngoSchool com este Número de telefone.";

    }else if($this->db->editar("entidadesprimaria", "nomeEntidade, numeroTelefoneEntidade, dataNascEntidade, emailEntidade, generoEntidade, paisNascEntidade, provNascEntidade, municNascEntidade, comunaNascEntidade, biEntidade, dataEBIEntidade, nivelAcademicoEntidade, numeroAgenteEntidade, valorAuferidoNaEducacao, formatoDocumentoEnt, fotoEntidade, paiEntidade, maeEntidade, categoriaEntidade, cursoEnsinoMedio, escolaEnsinoMedio,  cursoLicenciatura, escolaLicenciatura, cursoMestrado, escolaMestrado, cursoDoutoramento, escolaDoutoramento, dataCaducBI, numSegSocial, numDespacho, dataDespacho, dataInicEduc, dataInicOutraEsc, tempoServOutraEsc, comFormPedag, comMagisterio", [$this->nomeEntidade, $this->numeroTelefoneEntidade, $this->dataNascEntidade, $this->emailEntidade, $this->sexoEntidade, $this->pais, $this->provincia, $this->municipio, $this->comuna, $this->biEntidade, $this->dataEBIEntidade, $this->nivelAcademicoEntidade, $this->numeroAgenteEntidade, $this->valorAuferidoNaEducacao, $this->formatoDocumento, $this->fotoEntidade, $this->paiEntidade, $this->maeEntidade, $this->categoriaEntidade, $this->cursoEnsinoMedio, $this->escolaEnsinoMedio, $this->cursoLicenciatura, $this->escolaLicenciatura, $this->cursoMestrado, $this->escolaMestrado, $this->cursoDoutoramento, $this->escolaDoutoramento, $this->dataCaducBI , $this->numSegSocial, $this->numDespacho , $this->dataDespacho, $this->dataInicEduc, $this->dataInicOutraEsc, $this->tempoServOutraEsc, $this->comFormPedag, $this->comMagisterio], ["idPEntidade"=>$this->idPEntidade])=="sim"){

      $this->db->editarItemObjecto("entidadesprimaria", "escola", "dataInicioFuncoesEntidade, funcaoEnt, cargoPedagogicoEnt, dataUltimaActualizacao, naturezaVinc, efectividade, tambemColaboradorNaInstituicao, valorAuferidoNaInstituicao, tipoPessoal, nomeBanco, numeroContaBancaria, nivelSistemaEntidade, ibanContaBancaria, pagamentoPorTempo", [$this->dataInicioFuncoesEntidade, $this->funcaoEnt, $this->cargoPedagogicoEnt, $this->db->dataSistema, $this->naturezaVinc, $this->efectividade, $this->tambemColaboradorNaInstituicao, $this->valorAuferidoNaInstituicao, $this->tipoPessoal, $this->nomeBanco, $this->numeroContaBancaria, $this->nivelSistemaEntidade, $this->ibanContaBancaria, $this->pagamentoPorTempo], ["idPEntidade"=>$this->idPEntidade], ["idEntidadeEscola"=>$this->idDaEscolaReferencia]);
        if(!isset($_POST['editadoNoPerfilEntidade'])){
          $this->listar();
        }

    }else{
      echo "FNão foi possível editar os dados do agente.";
    }
  }
  public function excluirAgente(){
    if($this->db->editarItemObjecto("entidadesprimaria", "escola", "estadoActividadeEntidade", ["I"], ["idPEntidade"=>$this->idPEntidade], ["idEntidadeEscola"=>$this->idDaEscolaReferencia])=="sim"){
      if(!isset($_POST['editadoNoPerfilEntidade'])){
        $this->listar();
      }
    }else{
      echo "FNão foi possível excluir agente na instituição";
    }
  }
  private function listar(){
    echo $this->db->selectJson("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$this->idDaEscolaReferencia, "escola.estadoActividadeEntidade"=>"A"], ["escola"], "", [], ["nomeEntidade"=>1]);
  }
}
