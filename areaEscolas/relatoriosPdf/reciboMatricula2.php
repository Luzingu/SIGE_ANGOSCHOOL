<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class certificadoDisciplina extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Recibo de Matricula");  
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;

            $this->aluno = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$this->idPMatricula,"escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idAnoActual], ["escola", "reconfirmacoes"]);

            $this->aluno = $this->anexarTabela2($this->aluno, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
            $this->aluno = $this->anexarTabela2($this->aluno, "entidadesprimaria", "reconfirmacoes", "idPEntidade", "idReconfProfessor");
            $this->aluno = $this->anexarTabela2($this->aluno, "anolectivo", "reconfirmacoes", "idPAno", "idReconfAno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_paises", "idPPais", "paisNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_provincias", "idPProvincia", "provNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_municipios", "idPMunicipio", "municNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_comunas", "idPComuna", "comunaNascAluno");

            $this->numAno();
            $this->idPCurso = valorArray($this->aluno, "idMatCurso", "escola");
            $this->nomeCurso();

            if((valorArray($this->aluno, "idPEntidade")==$_SESSION["idUsuarioLogado"] || $this->verificacaoAcesso->verificarAcesso("", ["reconfirmados"], array(), ""))){
                
                $this->recibo();
                             
            }else{
                 $this->negarAcesso();
            } 

        }
        
        public function recibo(){
            
            if(valorArray($this->aluno, "sexoAluno")=="M"){
                $this->art1="o";
                $this->art2 ="";
            }else{
                $this->art1="a";
                $this->art2 ="a";
            }
             $periodo = valorArray($this->aluno, "periodoAluno", "escola");
             if($periodo=="reg"){
                $periodo="Regular";
             }else if($periodo=="pos"){
                $periodo="Pós-Laboral";
             }
                        
            $numero=0;

            $logotipo= $_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg";
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                $logotipo = $_SERVER['DOCUMENT_ROOT'].'/angoschool/Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");
            }
            
            $this->html .="
           <html style='margin:0px;'>
            <head>
                <title>Comprovatico de Matricula</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:1px;

                    }

                    .tabela2 tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:4px;

                    }
                </style>
            </head>
           <body style='margin:20px; margin-bottom:0px; margin-top:10px; padding-top:10px;'>".$this->fundoDocumento("../../")."
           
           <div style='border:solid black 2px; padding:5px;'>
            <div style='padding-top:20px;'><img src='".$logotipo."' style='height:120px; width:120px;'></div>
            <div style='margin-left:125px; margin-top:-200px;'>".$this->cabecalho("nao", $this->text_left)."
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."'>BOLETIM DE MATRÍCULA</p>
                <p style='".$this->bolder.$this->text_center." font-size:18pt;margin-bottom:0px;'>".$this->numAno."</p>
            </div>
           </div>
           <div style='border:solid black 2px; margin-top:10px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                <table class='tabela' style='width:100%; '>
                    <tr>
                        <td style='".$this->text_right." border:none;'></td><td colspan='2' style='border:none;'></td>

                        <td style='".$this->text_right."border:none;'>Data:</td><td><strong>".valorArray($this->aluno, "dataReconf", "reconfirmacoes")." ".valorArray($this->aluno, "horaReconf", "reconfirmacoes")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Nome:</td>
                        <td colspan='5'><strong>".valorArray($this->aluno, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->aluno, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º ".valorArray($this->aluno, "tipoDocumento").":</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "biAluno")."</strong></td>
                    </tr>

                    <tr>
                        <td style='".$this->text_right."'>Data de Nasc.:</td>
                        <td colspan='2'><strong>".dataExtensa(valorArray($this->aluno, "dataNascAluno"))."</strong></td>
                        <td style='".$this->text_right."'>País:</td>
                        <td><strong>".valorArray($this->aluno, "nomePais")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Província:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "nomeProvincia")."</strong></td>
                        <td style='".$this->text_right."'>Municipio:</td>
                        <td><strong>".valorArray($this->aluno, "nomeMunicipio")."</strong></td>
                    </tr>

                    <tr>
                        <td style='".$this->text_right."'>Pai:</td>
                        <td><strong>".valorArray($this->aluno, "paiAluno")."</strong></td>
                        <td style='".$this->text_right."'>Mãe:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "maeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Residência:</td>
                        <td><strong>".valorArray($this->sobreEscolaLogada, "nomeMunicipio")."</strong></td>
                        <td style='".$this->text_right."'>Bairro:</td>
                        <td colspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/</td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Trabalhas?</td>
                        <td></strong></td>
                        <td style='".$this->text_right."'>(Sim) Sector:</td>
                        <td colspan='2'></td>
                    </tr>
                </table>
            </div>

            <strong>Dados Académicos</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>";

                    if($this->tipoCurso=="tecnico"){
                        $this->html .="
                        <tr>
                        <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                            <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }else if($this->tipoCurso=="pedagogico"){
                        $this->html .="
                        <tr>
                        <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                            <td style='".$this->text_right."'>Opção:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }else{
                        $this->html .="
                        <tr>
                            <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }
                       $this->html .="<tr>
                            <td style='".$this->text_right."'>Classe:</td><td><strong>".classeExtensa($this, valorArray($this->aluno, "idMatCurso", "reconfirmacoes"), valorArray($this->aluno, "classeReconfirmacao", "reconfirmacoes"))."</strong></td>
                            <td style='".$this->text_right."'>Período:</td><td><strong>".$periodo."</strong></td>
                        </tr>
                        <tr>
                            <td style='".$this->text_right."'>N.º Interno:</td><td colspan='3'><strong>".valorArray($this->aluno, "numeroInterno")."</strong></td>
                        </tr>
                    </table>

                    <div style='width: 50%;'>";
                    if(valorArray($this->aluno, "generoEntidade")=="M"){
                        $this->html .=$this->porAssinatura("O Funcionário", valorArray($this->aluno, "nomeEntidade"));
                    }else{
                        $this->html .=$this->porAssinatura("A Funcionária", valorArray($this->aluno, "nomeEntidade"));
                    }

                    $this->html .= "
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("<span style='".$this->maiuscula."'>".$this->art1."</span> Alun".$this->art1, valorArray($this->aluno, "nomeAluno"), "", strlen(valorArray($this->aluno, "nomeAluno")))."
                    </div>";

                    

                $this->html .="<br/><br/></div>
                </div>";
           
           
           
           //Última Folha do recibo...

           $this->html .="<hr style='margin-top:15px; margin-bottom:-5px;'>
           <div style='margin-top:20px;'>".$this->fundoDocumento("../../")."
           <div style='border:solid black 2px; padding:5px; padding-bottom:0px;'>
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."margin-top:0px;'>COMPROVATIVO DE MATRÍCULA</p>
                <p style='".$this->bolder.$this->text_center." font-size:16pt;margin-bottom:0px;'>".$this->numAno."</p>
           </div>

           <div style='border:solid black 2px; margin-top:0px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                <table class='tabela2' style='width:100%; '>
                    <tr>
                        <td style='".$this->text_right." border:none;'></td><td colspan='2' style='border:none;'></td>

                        <td style='".$this->text_right."border:none;'>Data:</td><td><strong>".valorArray($this->aluno, "dataReconf", "reconfirmacoes")." ".valorArray($this->aluno, "horaReconf", "reconfirmacoes")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Nome Completo:</td>
                        <td colspan='5'><strong>".valorArray($this->aluno, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->aluno, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º BI/Cédula:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "biAluno")."</strong></td>
                    </tr>

                </table>
            </div>
            <strong>Dados Académicos</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>";

                   
                     if($this->tipoCurso=="tecnico"){
                        $this->html .="
                        <tr>
                        <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                            <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }else if($this->tipoCurso=="pedagogico"){
                        $this->html .="
                        <tr>
                        <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($this->aluno, "areaFormacaoCurso")."</strong></td>

                            <td style='".$this->text_right."'>Opção:</td><td><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }else{
                        $this->html .="
                        <tr>
                            <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".valorArray($this->aluno, "nomeCurso")."</strong></td>
                        </tr>";
                    }
                    
                       $this->html .="<tr>
                            <td style='".$this->text_right."'>Classe:</td><td><strong>".classeExtensa($this,valorArray($this->aluno, "idMatCurso", "reconfirmacoes"), valorArray($this->aluno, "classeReconfirmacao", "reconfirmacoes"))."</strong></td>
                            <td style='".$this->text_right."'>Período:</td><td><strong>".$periodo."</strong></td>
                        </tr>
                        <tr>
                            <td style='".$this->text_right."'>N.º Interno:</td><td colspan='3'><strong>".valorArray($this->aluno, "numeroInterno")."</strong></td>
                        </tr>
                    </table>";
                    if(valorArray($this->aluno, "generoEntidade")=="M"){
                        $this->html .=$this->porAssinatura("O Funcionário", valorArray($this->aluno, "nomeEntidade"));
                    }else{
                        $this->html .=$this->porAssinatura("A Funcionária", valorArray($this->aluno, "nomeEntidade"));
                    }

                $this->html .="<br/><br/></div></div></body></html>";

            $this->exibir("", "Recibo de Matricula ".dataExtensa(valorArray($this->aluno, "dataReconf"))."-".valorArray($this->aluno, "numeroInterno"));
        }
    }
    new certificadoDisciplina(__DIR__);
?>