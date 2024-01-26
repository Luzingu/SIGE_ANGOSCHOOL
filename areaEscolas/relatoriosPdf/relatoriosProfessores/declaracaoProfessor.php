<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class relatorioTransferencia extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Declaração do Professor");

            $this->motivoDeclaracao = isset($_GET["motivoDeclaracao"])?$_GET["motivoDeclaracao"]:null;
            $this->idPProfessor = isset($_GET["idPProfessor"])?$_GET["idPProfessor"]:null;
            $this->assinante = isset($_GET["assinante"])?$_GET["assinante"]:null;
            $this->numeroDeclaracao = isset($_GET["numeroDeclaracao"])?$_GET["numeroDeclaracao"]:null;
            $this->declVencimento = isset($_GET["declVencimento"])?$_GET["declVencimento"]:null;

            $this->professor =$this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPProfessor, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
            $this->professor=$this->anexarTabela($this->professor, "escolas", "idPEscola", "idEntidadeEscola");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_provincias", "idPProvincia", "provNascEntidade");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_municipios", "idPMunicipio", "municNascEntidade");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_comunas", "idPComuna", "comunaNascEntidade"); 

            $this->html="<html style='margin-left:70px;margin-right:70px;'>
            <head>
                <title>Declaração</title>
            </head>
            <body>".$this->fundoDocumento("../../../");

            if($this->verificacaoAcesso->verificarAcesso("", "relatorioFuncionario", array(), "")){ 
                
             $this->modeloIPAG(); 
            }else{
              $this->negarAcesso();
            }            
        }

        private function modeloIPAG(){
            if(valorArray($this->professor, "generoEntidade")=="M"){
                $art1="o";
                $art2 ="";
            }else{
                 $art1="a";
                $art2 ="a";
            }
            $labelValorAuferido="";
            if($this->declVencimento=="sim"){
              $salario = valorArray($this->professor, "valorAuferidoNaEducacao");
              if($salario!="" && $salario!=NULL){
               $salario = number_format($salario, 2, ",", ".");
              }

             $labelValorAuferido ="<p style='margin-top:20px;".$this->text_justify."line-height:25px;'>Aufere o salário líquido mensal no valor de <strong>Kzs ".$salario."</strong> que é pago através da sua conta bancária n.º <strong>".valorArray($this->professor, "numeroContaBancaria", "escola")."</strong>, do ".valorArray($this->professor, "nomeBanco", "escola").".</p>";
            } 

            $this->html .=$this->cabecalho()."
            <p  style='margin-top:20px; font-size:14pt !important;".$this->bolder.$this->text_center.$this->maiuscula."'>DECLARAÇÃO DE SERVIÇO N.º ".$this->numeroDeclaracao."/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", $this->dataSistema)[0]."</p>
 
            <p style='margin-top:10px;".$this->text_justify."line-height:30px;'>Para os devidos efeitos e tidos por conveniência, declara que ".$art1." Senhor".$art2." <span style='".$this->bolder."'>".valorArray($this->professor, "nomeEntidade")."</span>, filh".$art1." de ".valorArray($this->professor, "paiEntidade")." e de ".valorArray($this->professor, "maeEntidade").", natural ".valorArray($this->professor, "preposicaoComuna2")." ".valorArray($this->professor, "nomeComuna").", municipio ".valorArray($this->professor, "preposicaoMunicipio2")." ".valorArray($this->professor, "nomeMunicipio").", província ".valorArray($this->professor, "preposicaoProvincia2")." ".valorArray($this->professor, "nomeProvincia").", nascid".$art1." aos ".dataExtensa(valorArray($this->professor, "dataNascEntidade")).", portador".$art2." do BI nº ".valorArray($this->professor, "biEntidade").", passsado pela Direcção Nacional de Identificação de Luanda, aos ".dataExtensa(valorArray($this->professor, "dataEBIEntidade")).", é funcionário efectivo desta instituição, onde excerce as suas funções de ".valorArray($this->professor, "funcaoEnt", "escola")." e com a categoria de ".valorArray($this->professor, "categoriaEntidade").", desde ".dataExtensa(valorArray($this->professor, "dataInicioFuncoesEntidade", "escola")).", colocad".$art1." no ".valorArray($this->sobreUsuarioLogado, "nomeEscola")." como Agente nº <strong>".valorArray($this->professor, "numeroAgenteEntidade")."</strong>, vinculad".$art1." ao contrato de trabalho por tempo indeterminado.</p>
            ".$labelValorAuferido."

            <p style='margin-top:10px; line-height:30px;".$this->bolder."'>OBS: <span style='".$this->sublinhado."'>Esta Declaração destina-se para efeito de <span style='".$this->sublinhado."'>".$this->motivoDeclaracao."</span>.</span></p>

            <p style='margin-top:15px;".$this->text_justify." line-height:30px;'>Por ser verdade e me ter soliscitado, mandei a presente Declaração que vai por mim devidamente assinada e autenticada com carimbo a óleo em uso nesta instituição.</p>

            <p style='margin-top:25px;".$this->bolder."line-height:30px;'><span class='maiuscula'>".$this->rodape().".</p>
            <div style='margin-top:20px;".$this->text_center."'>".$this->assinaturaDirigentes($this->assinante)."</div>";
            
           $this->exibir("", "Declaração de Serviço-".valorArray($this->professor, "nomeEntidade"));
        }
    }

new relatorioTransferencia(__DIR__);
    
    
  
?>