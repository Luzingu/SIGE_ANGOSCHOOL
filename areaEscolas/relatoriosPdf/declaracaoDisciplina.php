<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class certificadoDisciplina extends funcoesAuxiliares{
              
        public $efeitoDeclaracao="";
        public $numeroDeclaracao="";
        public $idPMatricula="";
        public $art1="";
        public $art2="";

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Declaração da Disciplina");  

            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";
            $this->idPNomeDisciplina = isset($_GET["idPNomeDisciplina"])?$_GET["idPNomeDisciplina"]:"";

            $this->sobreAluno($this->idPMatricula);
            $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_provincias", "idPProvincia", "provNascAluno");
             $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_municipios", "idPMunicipio", "municNascAluno");
             $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_comunas", "idPComuna", "comunaNascAluno");

           if(valorArray($this->sobreAluno, "sexoAluno")=="M"){
                $this->art1="o";
                $this->art2 ="";
            }else{
                $this->art1="a";
                $this->art2 ="a";
            }


           $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");

           if($this->verificacaoAcesso->verificarAcesso("", "relatorioAluno", array(), "") && valorArray($this->sobreAluno, "classeActualAluno", "escola")>=12){
                $this->certificado();
           }else{
                $this->negarAcesso();
           }

        }

        public function certificado(){
            $this->nomeDisciplina = $this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$this->idPNomeDisciplina]);

            $this->nomeCurso();
            $this->notas = listarItensObjecto($this->sobreAluno, "pautas", ["idPautaDisciplina=".$this->idPNomeDisciplina, "classePauta>=10", "classePauta<=13", "idPautaCurso=".$this->idPCurso]);

            $totalDisc=0;
            $totalNotas=0;
            $PC=0;
            foreach ($this->notas as $nota) {
                if($nota["recurso"]!=NULL || $nota["recurso"]!=""){
                    $nota["mf"]=$nota["recurso"];
                }
                $nota["mf"] = number_format(floatval(nelson($nota, "mf")), 0);
                $totalNotas +=$nota["mf"];
                $totalDisc++;
            }
            if($totalDisc==0){
                $PC=0;
            }else{
                $PC = number_format($totalNotas/$totalDisc, 0);
            }

            $this->html .="<html style='margin:0px;'>
            <head>
                <title>Certificado de ".$this->nomeDisciplina."</title>
                <style>
                    table tr td{
                        padding:2px;
                    }
                    p{
                        font-size:13pt;
                    }
                </style>
            </head>
            <body>
            <div style='border:double black 5px; margin:45px; padding-top:-15px; margin-bottom:-100px; height:1015px;'>".$this->fundoDocumento("../../");

            $this->html .="<div class='cabecalho'>".$this->cabecalho()."
                      <p  style='font-size: 15pt; margin-top:5px;".$this->text_center.$this->bolder.$this->maiuscula."'>CERTIFICADO DE ".$this->nomeDisciplina."</p>
                </div>

                <div style='margin-top:-20px; padding:10px;'>
                    <p style='line-height: 27px;".$this->text_justify."'><span style='".$this->bolder."'><i>".$this->nomeDirigente("Director")."</i></span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola").", certifica que <span style='".$this->bolder.$this->vermelha."'><i>".valorArray($this->sobreAluno, "nomeAluno")."</i></span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", portador".$this->art2." do Bilhete de Identidade n.º ".valorArray($this->sobreAluno, "biAluno").", passado pela  Direcção Nacional de Identificação de Luanda, aos ".dataExtensa(valorArray($this->sobreAluno, "dataEBIAluno")).", concluiu com êxito a Formação de <strong>".$this->nomeDisciplina."</strong>, ao abrigo do Despacho nº 214-A/2010 de 5 de Novembro do Ministério da Educação, com a média final de <strong>".$PC." (".primeiraLetraMaiuscula($this->retornarNotaExtensa($PC)).") valores</strong>, obtido durante ";
                    if(count($this->notas)==1){
                        $this->html .="um ano";
                    }else{
                        $this->html .=$this->retornarNotaExtensa(count($this->notas))." anos";
                    }

                    $this->html .=" e com as seguintes classificações por classes:</p>
                    <div style='width:100%; margin-top:25px;'>
                <table style='width:100%; font-size:12pt;".$this->tabela."'>";


                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td>";
                for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                    $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".classeExtensa($this, valorArray($this->sobreAluno, "idMatCurso", "escola"), $i)."</td>";
                }
                $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média Final</td></tr>";

                $this->html .="<tr><td style='".$this->border()."'>".valorArray($this->notas, "nomeDisciplina")."</td>";
                for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                    $this->html .=$this->retornarNotas($this->retornarNotaPorClasse($i));
                }
                $this->html .=$this->retornarNotas($PC)."</tr>";

                $this->html .="<tr><td style='".$this->border()."'>Total de horas anuais</td>";

                $contador=0;
                for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                    $this->html .="<td style='".$this->border().$this->text_center."'>";
                    if(intval($this->retornarNotaPorClasse($i))>0){
                        $contador+=60;
                        $this->html .="60";
                    }
                    $this->html .="</td>";
                }
                $this->html .="<td style='".$this->border().$this->text_center."'>".$contador."</td></tr>";

            $this->html .="</table></div>
                <p style='line-height: 25px;".$this->text_justify." margin-top:25px;'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no livro de termos n.º ".valorArray($this->sobreAluno, "numeroLivroRegistro").", folha n.º ".completarNumero(valorArray($this->sobreAluno, "numeroFolha")).", assinado por mim e autenticado com o carimbo a óleo em uso neste estabelecimento de ensino.</p>
                <p style='line-height: 25px;".$this->maiuscula.$this->text_justify."'>".$this->rodape().".</p>

                    <div style='margin-top: 0px;".$this->text_center."'>
                        <div style='width: 50%;'>
                            <p>Conferido por</p>
                            <p style='".$this->miniParagrafo.$this->text_center."margin-top:-10px;'>_______________________________</p>
                            <p style='".$this->miniParagrafo.$this->text_center."'>".$this->nomeDirigente("Pedagógico", 30, "", "", "")."</p>
                            <p style='".$this->miniParagrafo.$this->text_center."'>(Subdirector Pedagógico)</p>
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7)."</div>
                        </div>
                    </div>
            </div>
        </body>
        </html>";

        $this->exibir("", "Certificado de ".valorArray($this->notas, "nomeDisciplina")."-".valorArray($this->sobreAluno, "nomeAluno"));
        }

        private function retornarNotaPorClasse($classe){
            $retorno="";
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe){
                    $retorno = $nota["mf"];
                    break;
                }
            }
           return $retorno;   
        }
    }
    new certificadoDisciplina(__DIR__);
?>