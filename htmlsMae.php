<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }

  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/perfilFuncionario.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/historicoConectividade.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/adicionarAgentes.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/arquivosComunsEntreAreas/listaAgentes.php');

class includarHtmlsMae extends manipulacaoDados{
    function __construct(){
      parent::__construct("");
      echo "<script>var enderecoArquivos='".$this->enderecoArquivos."'</script>";     
    }
    function __destruct(){

      $diferenca = calcularDiferencaEntreDatas($this->dataSistema, valorArray($this->sobreEscolaLogada, "dataBackup1"));
      if(valorArray($this->sobreUsuarioLogado, "BACKUP", "escola")=="V" && $diferenca>=3){
        echo "<script>var diferenca='".$diferenca."'</script>";

        if($diferenca<8){ ?>
           <script type="text/javascript">
            $(document).ready(function(){
              mensagensRespostas("#informacoes", "<p style='color:rgba(255, 255, 255, 0.8); text-align:justify;'>Saudações.<br> Informamos que ainda ainda não fez o blackup dos dados nos últimos "+diferenca+" dias. Daqui a "+(8-diferenca)+" dia(s), a tua conta será temporariamente suspensa.</p>");
            })
          </script>
    <?php  
        }else { ?>
          <script type="text/javascript">
            $(document).ready(function(){
              mensagensRespostas("#informacoes", "<p style='color:rgba(255, 255, 255, 0.8); text-align:justify;'>Saudações.<br> Informamos que a sua conta foi temporariamente suspensa por não realizar BACKUP dos dados nos últimos "+diferenca+" dia(s).</p>");
            })
          </script>
    <?php 
        } ?>

     

     <?php } $this->mensagemBemVindo();
    }

    public function formularioAgentes(){ 
      formularioAgentes($this);
    }
    public function perfilFuncionario(){ 
      perfilFuncionario($this); 
    }
    public function historicoConectividade(){ 
      historicoConectividade($this);
    }
    public function adicionarAgentes($idPEscola=""){
      if($idPEscola==""){
        $idPEscola=$_SESSION['idEscolaLogada'];
      }
      adicionarAgentes($this, $idPEscola);
    }
    public function indexAgente($idPEscola=""){
      if($idPEscola==""){
        $idPEscola=$_SESSION['idEscolaLogada'];
      }
      indexAgente($this, $idPEscola);
    }

   function formTrocarSenha() { ?>
    <div class="modal fade" id="formularioTrocarSenha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none;">
      <form class="modal-dialog" id="formularioTrocarSenhaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-key"></i> Alterar Senha</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="lead col-lg-12 text-danger font-weight-bolder" id="discasPrenchimento"></div>
                  </div>

                  <div class="row">
                      <div class="col-lg-12 lead">
                        Senha Antiga:
                        <input type="password" name="antigaSenha" class="form-control fa-border vazio" id="antigaSenha" title="Senha Antiga" required>
                        <div class="antigaSenha discasPrenchimento lead"></div>
                      </div>
                  </div>

                   <div class="row">
                      <div class="lead col-lg-12">
                        Nova Senha:
                        <input type="password" name="novaSenha" class="form-control fa-border vazio" id="novaSenha" title="Nova Senha" required>
                        <div class="novaSenha discasPrenchimento lead"></div>
                      </div>
                  </div>

                  <div class="row">

                      <div class="col-lg-12 lead">
                        Confirmar:
                        <input type="password" name="confirmarSenha" class="form-control vazio" id="confirmarSenha" title="Confirmar Senha" required>

                        <div class="confirmarSenha discasPrenchimento lead"></div>
                      </div>
                  </div>
                  <input type="hidden" name="action" value="trocarSenha">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-success lead" id="Submit"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>


     

    <script type="text/javascript">
      
      $(document).ready(function(){

        $("#deficiencia").change(function(){
          seleccionarTipoDeDeficiencia($("#deficiencia").val());
        })        

        $("#trocarSenha").click(function(){
           $("#formularioTrocarSenha #Submit").html('<i class="fa fa-check"></i> Alterar');
          $("#formularioTrocarSenha").modal("show");
        })

        $("#trocarSenha").click(function(){
           $("#formularioTrocarSenha #discasPrenchimento").empty();
          limparFormulario("#formularioTrocarSenha form");
          $("#formularioTrocarSenha").modal("show");
        })

        $("#formularioTrocarSenha form").submit(function(){
          trocarSenha();
          return false;
        });


      function trocarSenha(){ 
        var form = new FormData(document.getElementById("formularioTrocarSenhaForm"));
        var http2 = new XMLHttpRequest();
        $("#formularioTrocarSenha #discasPrenchimento").text(""); 
        $("#formularioTrocarSenha #Submit").html('<i class="fa fa-spinner fa-spin"></i> Alterando...');
        http2.onreadystatechange = function(){
            if(http2.readyState==4){
              $("#formularioTrocarSenha #Submit").html('<i class="fa fa-check"></i> Trocar');
              $("#formularioTrocarSenha #discasPrenchimento").text("");
              $("#formularioTrocarSenha input[type=password]").css("border", "solid black 1px");
              
              if(http2.responseText.trim().substring(0, 1)=="X"){
                $("#formularioTrocarSenha #discasPrenchimento").text(http2.responseText.trim().substring(1, http2.responseText.trim().length));
                $("#formularioTrocarSenha #antigaSenha").css("border", "solid red 1px");

              }else if(http2.responseText.trim().substring(0, 1)=="P"){
                $("#formularioTrocarSenha #discasPrenchimento").text(http2.responseText.trim().substring(1, http2.responseText.trim().length));                
                  $("#formularioTrocarSenha #novaSenha").css("border", "solid red 1px");
                  $("#formularioTrocarSenha #confirmarSenha").css("border", "solid red 1px");

              }else if(http2.responseText.trim().substring(0, 1)=="V"){
                $(".modal").modal("hide");
                 mensagensRespostas('#mensagemCerta', http2.responseText.trim().substring(1, http2.responseText.trim().length));
              }else{
                $(".modal").modal("hide");
                mensagensRespostas('#mensagemErrada', http2.responseText.trim().substring(1, http2.responseText.trim().length));
              }                
            }
        }
        http2.open("POST", caminhoRecuar+"../areaAdministrador/areaGestaoEscolas/escolas00/manipulacaoDadosDoAjax.php", true);
        http2.send(form);
      }
      })
      


    </script>


<style type="text/css">
   #formularioTrocarSenha #discasPrenchimento p{
        margin-top: -5px;
        margin-bottom: -10px;
        font-size: 7pt;
        color: black;
        margin-left: 10px;
    }

    #formularioTrocarSenha #discasPrenchimento div{
      color:red; 
      font-weight:600;
      font-size: 14pt;
    }

</style>

<?php } function dataList(){?>
  <datalist id="listaOpcoes">
  </datalist>
<?php } private function mensagemBemVindo(){ 
    $log = isset($_GET["login"])?$_GET["login"]:'__';

    if($log!="__"){
      $ultimoLogin = $this->selectArray("entidadesonline", [], ["idUsuarioLogado"=>$_SESSION["idUsuarioLogado"], "estadoExpulsao"=>"I"], [], "", [], ["_id"=>-1]);
     
      $nomeUsuario="";
      if(isset($_SESSION["idUsuarioLogado"]) && isset($_SESSION['idEscolaLogada']) && isset($_SESSION['tipoUsuario'])){
        if($_SESSION['tipoUsuario']=="aluno"){
          $nomeUsuario = valorArray($this->sobreUsuarioLogado, "nomeAluno");
        }else{
          $nomeUsuario = valorArray($this->sobreUsuarioLogado, "nomeEntidade");
        }        
      }

      if(count($ultimoLogin)<=0){
          echo '<script type="text/javascript">
          $(document).ready(function(){
              toastr.success("Bem-vindo ao AngoSchool senhor(a) <strong>'.$nomeUsuario.'</strong>.<br/><br/>Desejamos-lhe uma boa estadia!"); $(".toast").addClass("show");

          })
        </script>';
      }else{
          echo '<script type="text/javascript">
          $(document).ready(function(){
              toastr.success("Bem-vindo de volta ao AngoSchool senhor(a) <strong>'.$nomeUsuario.'</strong>.<br/><br/>Data da última entrada: '.dataExtensa(valorArray($ultimoLogin, "dataEntrada")).' as '.valorArray($ultimoLogin, "horaSaida").'");$(".toast").addClass("show");
          })
        </script>';
      }        
    }
    
 } public function rodape(){ ?>
    <div class="row" id="rodapePrincipal" style="display: none;">
       <div class="col-lg-12 col-md-12" style="background-color: #324c57; color: white;"><br>
          <span><span class="visible-lg visible-md">&copy; Copyright <strong>Luzingu Luame LDA</strong>. Todos direitos reservados.</span>
        <div class="float-right d-sm-inline-block" style="margin-top:-30px;">
          <strong>Versão</strong> 5.0.0
        </div>
       </div>
      </div>

 <?php } } ?>