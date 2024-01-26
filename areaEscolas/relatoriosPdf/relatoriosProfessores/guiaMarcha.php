<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class relatorioTransferencia extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Guia de Marcha");

            $this->numeroGuiaMarcha = isset($_GET["numeroGuiaMarcha"])?$_GET["numeroGuiaMarcha"]:null;
            $this->pais = isset($_GET["pais"])?$_GET["pais"]:null;
            $this->provincia = isset($_GET["provincia"])?$_GET["provincia"]:null;
            $this->municipio = isset($_GET["municipio"])?$_GET["municipio"]:null;
            $this->comuna = isset($_GET["comuna"])?$_GET["comuna"]:null;
            $this->motivo = isset($_GET["motivo"])?$_GET["motivo"]:null;
            $this->assinante = isset($_GET["assinante"])?$_GET["assinante"]:null;
            $this->idPProfessor = isset($_GET["idPProfessor"])?$_GET["idPProfessor"]:null;

            $this->professor =$this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPProfessor, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
            $this->professor=$this->anexarTabela($this->professor, "escolas", "idPEscola", "idEntidadeEscola");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_provincias", "idPProvincia", "provNascEntidade");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_municipios", "idPMunicipio", "municNascEntidade");
            $this->professor=$this->anexarTabela($this->professor, "div_terit_comunas", "idPComuna", "comunaNascEntidade");

            $this->html="<html>
            <head>
                <title>Guia de Marcha</title>
            </head>
            <body>".$this->fundoDocumento("../../../");
 
            if($this->verificacaoAcesso->verificarAcesso("", "relatorioFuncionario", array(), "")){                                
                $this->modeloIMNE();
            }else{
              $this->negarAcesso();
            }
        }

         private function modeloIMNE(){
            if(valorArray($this->professor, "generoEntidade")=="M"){
                $art1="o";
                $art2 ="";
            }else{
                 $art1="a";
                $art2 ="a";
            }

            $sobrePais = $this->selectArray("div_terit_paises", ["idPPais"=>$this->pais]);
            $sobreProvincia = $this->selectArray("div_terit_provincias", [], ["idPProvincia"=>$this->provincia]);
            $sobreMunicipio = $this->selectArray("div_terit_municipios", [], ["idPMunicipio"=>$this->municipio]);
            $sobreComuna = $this->selectArray("div_terit_comunas", [], ["idPComuna"=>$this->comuna]);

            $destino="";
            if($this->pais==valorArray($this->professor, "pais")){

                if($this->provincia==valorArray($this->professor, "provincia")){
                    $destino = valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
                }else{
                    $destino = valorArray($sobreProvincia, "preposicaoProvincia2")." ".valorArray($sobreProvincia, "nomeProvincia").", no municipio ".valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
                }
            }else{
                $destino = valorArray($sobrePais, "nomePais").", na província ".valorArray($sobreProvincia, "preposicaoProvincia2")." ".valorArray($sobreProvincia, "nomeProvincia").", no municipio ".valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
            }

            $this->html .="<div class='cabecalho'>".$this->cabecalho()."</div>
            <p  style='margin-top:10px;".$this->maiuscula.$this->sublinhado.$this->bolder.$this->text_center."'>GUIA DE MARCHA N.º ".$this->numeroGuiaMarcha."/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", $this->dataSistema)[0]."</p>

            <p style='margin-top:25px;".$this->text_justify."line-height:25px;'>Por esta Direcção d".$this->art1Escola." <span>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</span>, se faz constar as autoridades a quem o conhecimento desta competir que segue viagem do ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->professor, "municipio")])." para ".$destino.", ".$art1." Senhor".$art2." <span style='".$this->vermelha.$this->bolder."'>".valorArray($this->professor, "nomeEntidade")."</span>, funcionário desta escola com as funções de ".valorArray($this->professor, "funcaoEnt", "escola").", que ali se desloca, ".$this->motivo.".</p>

                <div style='height: 25px;border-bottom: dashed black 1.2px; margin-top:-20px;'></div>
                <div style='height: 25px;border-bottom: dashed black 1.2px;'></div>
                <div style='height: 25px;border-bottom: dashed black 1.2px;'></div>

            <p cstyle='margin-top:45px;".$this->text_justify."line-height:25px;margin-top:50px;'>E, para que se não lhe ponha impedimento, mandei passar a presente Guia de Marcha que vai por mim assinada e autenticada com carimbo a óleo em uso nesta Escola.</p><br/>
            <p style='margin-top:-20px;".$this->bolder."'>Sem outro assunto, renovadas saudações.</p>
            <p  style='margin-top:10px;".$this->text_justify."'>".$this->rodape()."</p>
            <div style='margin-top:0px;'>".$this->assinaturaDirigentes($this->assinante)."</div>";
            
           $this->exibir("", "Guia de Marcha-".valorArray($this->professor, "nomeEntidade"));
        }
    }

new relatorioTransferencia(__DIR__);
    
    
  
?>