<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->grupo = isset($_GET["grupo"])?$_GET["grupo"]:0;
            parent::__construct("Rel-Lista de Grupos");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();


            if($this->verificacaoAcesso->verificarAcesso("", ["gestorVagas"], [], "")){
                $this->listaInscritos();              
            }else{
                 $this->negarAcesso();
            }
            
        }

         private function listaInscritos(){
            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"inscritos", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"inscritos", "sexo"=>"F");
            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"testados", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"testados", "sexo"=>"F");

            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"naoTestados", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"naoTestados", "sexo"=>"F");

            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"admitidos", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"admitidos", "sexo"=>"F");


            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"naoAdmitidosCom+", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"naoAdmitidosCom+", "sexo"=>"F");
            $cabecalho[] = array("titulo"=>"MF", "tituloDb"=>"naoAdmitidosCom-", "sexo"=>"TOT");
            $cabecalho[] = array("titulo"=>"F", "tituloDb"=>"naoAdmitidosCom-", "sexo"=>"F");

             $this->conDb("inscricao");
            $this->listaDados = $this->selectArray("alunos", [], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION["idEscolaLogada"]], ["inscricao"]);

            
            $this->html .="<html>
            <head>
                <title>Lista dos Inscritos</title>
            </head>
            <body>".$this->fundoDocumento("../../../", "landscape").$this->cabecalho()."             
            <p style='".$this->text_center.$this->bolder.$this->sublinhado."'>MAPA DE CONTROLO DE INSCRIÇÃO DE CANDIDATURAS DO ANO LECTIVO - ".$this->numAno."</p>"; 
            
            $this->conDb();

            $cursos = $this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [] , ["nomeCurso"=>1]);

            $this->html .="
            <table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."' rowspan='3'>N.º</td><td style='".$this->border()."' rowspan='3'>ESPECIALIDADE</td><td style='".$this->border()."' colspan='2' rowspan='2'>INSCRITOS</td><td style='".$this->border()."' colspan='2' rowspan='2'>TESTADOS</td><td style='".$this->border()."' colspan='2' rowspan='2'>NÃO TESTADOS</td><td style='".$this->border()."' colspan='2' rowspan='2'>ADMITIDOS</td><td style='".$this->border()."' colspan='4'>NÃO ADMITIDOS</td><td rowspan='3' style='".$this->border()."'>OBSERVAÇÃO</td>
            </tr>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."' colspan='2'>COM POSITIVA</td>
                <td style='".$this->border()."' colspan='2'>NÃO NEGATIVA</td>
            </tr>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>";
            foreach($cabecalho as $a){
                $this->html .="<td style='".$this->border()."'>".$a["titulo"]."</td>";
            }
            $this->html .="</tr>";
            $i=0;
            foreach($cursos as $a){
                $i++;
                $this->html .="<tr><td style='".$this->border().$this->text_center."'>".$i."</td><td style='".$this->border().$this->maiuscula." width:250px;'>".$a["nomeCurso"]."</td>";
                foreach($cabecalho as $c){
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contador($c["tituloDb"], $c["sexo"], $a["idPNomeCurso"])."</td>";
                }
                $this->html .="<td style='".$this->border()."'></td></tr>";
            }

            $this->html .="<tr style='".$this->bolder."'><td style='".$this->border().$this->maiuscula.$this->text_center."' colspan='2'>TOTAL GERAL</td>";
            foreach($cabecalho as $c){
                $this->html .="<td style='".$this->border().$this->text_center."'>".$this->contador($c["tituloDb"], $c["sexo"], "TOT")."</td>";
            }
            $this->html .="<td style='".$this->border()."'></td></tr>";
            

            $this->html .="</table>
            <p style='".$this->maiuscula."'>".$this->rodape()."</p>
            <div>".$this->assinaturaDirigentes(7)."</div>";

            $this->exibir("", "Estatística dos Inscritos-".$this->numAnoActual, "", "A4", "landscape");
        }

        public function contador($tituloDb="", $sexo="", $idPCurso=""){
            $contador=0;
            foreach($this->listaDados as $a){
                if($tituloDb=="inscritos"){
                    if(seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }else if($tituloDb=="testados"){
                    if((nelson($a, "notaExame1", "inscricao")!=NULL && nelson($a, "notaExame1", "inscricao")!="") && seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }else if($tituloDb=="naoTestados"){
                    if((nelson($a, "notaExame1", "inscricao")==NULL || nelson($a, "notaExame1", "inscricao")=="") && seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }else if($tituloDb=="admitidos"){
                    if((nelson($a, "notaExame1", "inscricao")!=NULL && nelson($a, "notaExame1", "inscricao")!="") && nelson($a, "obsApuramento", "inscricao")=="A" && seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }else if($tituloDb=="naoAdmitidosCom+"){
                    if((nelson($a, "notaExame1", "inscricao")!=NULL && nelson($a, "notaExame1", "inscricao")!="") && (nelson($a, "obsApuramento", "inscricao")!="A" || nelson($a, "obsApuramento", "inscricao")==NULL) && nelson($a, "mediaExames", "inscricao")>=10 && seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }else if($tituloDb=="naoAdmitidosCom-"){
                    if((nelson($a, "notaExame1", "inscricao")!=NULL && nelson($a, "notaExame1", "inscricao")!="") && (nelson($a, "obsApuramento", "inscricao")!="A" || nelson($a, "obsApuramento", "inscricao")==NULL) && nelson($a, "mediaExames", "inscricao")<10 && seComparador($sexo, $a["sexoAluno"]) && seComparador($idPCurso, $a["inscricao"]["idInscricaoCurso"])){
                        $contador++;
                    }
                }
                
            }
            return completarNumero($contador);
        }
    }

new lista(__DIR__);
    
    
  
?>