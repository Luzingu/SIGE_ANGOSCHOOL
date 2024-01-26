<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoMedioPedagogico extends funcoesAuxiliares{
        public $idPMatricula="";
        public $aluno = array();
        public $art1="";
        public $art2="";
        public $modeloConsiderar="actual";
        public $disciplinas = array(); 

        public $resultPauta ="definitivo";

        function __construct($caminhoAbsoluto){
            $this->observacaoF[10]="";
            $this->observacaoF[11]="";
            $this->observacaoF[12]="";
            $this->observacaoF[13]="";
            $this->numeroAnterior=0;
            parent::__construct(); 
        }


        public function exibirTermo(){
           $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
           $this->classe = valorArray($this->sobreAluno, "classeActualAluno", "escola");
            $this->nomeCurso();

            $this->html .="<html style='margin:20px;'>
            <head>
                <title>Termo de Aproveitamento</title>
                <style>
                    p{
                        font-size:12pt;
                    }
                    .assinaturaDG p{
                        font-size:12pt !important;   
                    }
                </style>
            </head>
            <body><div>".$this->fundoDocumento("../../../", "horizontal");
            
            $this->html .=$this->cabecalho()."<p style='".$this->bolder.$this->text_center."margin-bottom: 0px;'>TERMO DE APROVEITAMENTO</p>";
            
            
             $this->html .="
              <div id='fotoAluno'>
                <div style='margin-top: -110px; text-align: right; width: 100%;'>
                    <img src='../../../fotoUsuarios/".valorArray($this->sobreAluno, "fotoAluno")."' style='border:solid #428bca 1px; border-radius: 10px; width: 90px; height: 100px;'>
                </div>
             </div>
              <p style='".$this->text_center.$this->maiuscula." margin-top:-80px;' >CURSO: <strong>".$this->areaFormacaoCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;OPÇÃO: <strong>".$this->nomeCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;PROCESSO N.º ".valorArray($this->sobreAluno, "numeroProcesso", "escola")."</p>
              
              <div style='border:solid black 2px; padding:4px;'>
                <p style='margin-bottom:7px; margin-top:0px;'>Nome: <strong>".valorArray($this->sobreAluno, "nomeAluno")."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno")."</p>

                <p style='margin-bottom:0px; margin-top:0px;'>Natural de <strong>".valorArray($this->sobreAluno, "nomeMunicipio")."</strong>&nbsp;&nbsp;&nbsp;nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno"))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portador".$this->art2." do BI n.º ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "biAluno"), 10)."&nbsp;&nbsp;&nbsp;&nbsp;Morada: ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")]).", &nbsp;&nbsp;&nbsp;&nbsp;Telefone n.º ".valorArray($this->sobreAluno, "telefoneAluno")."</p>
              </div>

              ";

            $notas =array();
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                $notas = array_merge($notas, $this->notasDeclaracao($classe["identificador"], $this->idPCurso));
            }
            $notas = ordenar($notas, "ordenacao ASC");

            $this->notas = array();
            $totalDisc=0;
            $totalNotas=0;
            foreach ($notas as $nota) {    
                if(nelson($nota, "mf")>0 || nelson($nota, "recurso")>0){
                    $this->notas[]=$nota;
                    $totalDisc++;
                    $totalNotas += floatval(nelson($nota, "mf"));
                }
            }
            if($totalDisc==0){
                $PC=0;
            }else{
                $PC = number_format($totalNotas/$totalDisc, 0);
            }

            $this->listaDisciplinas = array();
            foreach (distinct2($this->notas, "idPNomeDisciplina") as $idPNomeDisciplina) {
                $nomeDisc="";
                $tipo="";
                foreach (array_filter($this->notas, function($mamale) use ($idPNomeDisciplina){
                        return $mamale["idPNomeDisciplina"]==$idPNomeDisciplina;}) as $disciplina){

                    $nomeDisc = $disciplina["abreviacaoDisciplina1"];
                    $tipo = $disciplina["tipoDisciplina"];
                    if($idPNomeDisciplina==20 || $idPNomeDisciplina==21 || $idPNomeDisciplina==22 || $idPNomeDisciplina==23){
                        $nomeDisc="Língua Estrangeira";
                    }
                    break;
                }
                $this->listaDisciplinas[] = array("idPNomeDisciplina"=>$idPNomeDisciplina, "nomeDisciplina"=>$nomeDisc, "tipoDisciplina"=>$tipo);
            }

            $cabecalhos=array();
            $this->html .="<table style='".$this->tabela."width:100%;margin-top:10px;font-size:8pt;'>
            <tr style='".$this->corDanger."'>
                <td style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."' rowspan='2'>Disciplinas</td>";

                //Pegando os dados sobre a classe...
                foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                    $this->dadosSobreAClasse($classe["identificador"]);

                    $cabecalhos[$classe["identificador"]]=$this->cabecalhoTermpAproveitamento($this->idPAno, $this->idPCurso, $classe["identificador"]);

                    $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='".$this->border()."font-size:9pt;padding:2px;'>
                    <p style='margin-top:0px; margin-bottom:0px;font-size:9pt;".$this->maiuscula."'>ANO LECTIVO <strong>".$this->numAno."</strong>&nbsp;&nbsp;<strong>".$classe["designacao"]."</strong></p>
                    <p style='margin-top:0px; margin-bottom:0px;font-size:9pt;".$this->maiuscula."'>TURMA: <strong>".$this->turma."</strong>&nbsp;&nbsp;N.º <strong>".$this->numeroAnterior."</strong></p>
                    </td>";
                }
            $this->html .="</tr><tr style='".$this->corDanger."'>";
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                foreach($cabecalhos[$classe["identificador"]] as $cab){

                    $this->html .="<td style='".$this->border().$this->text_center."'>".$cab["designacao2"]."</td>";
                }
            }
            $this->html .="</tr>"; 

           foreach (distinct2($this->listaDisciplinas, "tipoDisciplina") as $tipo) {

                $this->html .="<tr><td style='".$this->bolder.$this->border().$this->text_justify."font-size:10pt; border:none;'>".tipoDisciplina($tipo)."</td></tr>"; 

                $contadorLinha=0;
                foreach (array_filter($this->listaDisciplinas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html.="<tr><td style='".$this->border()."font-size:10pt;'>".$disciplina["nomeDisciplina"]."</td>";

                    foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                        foreach($cabecalhos[$classe["identificador"]] as $cab){
                            $this->html .=$this->retornarNota($classe["identificador"], $disciplina["idPNomeDisciplina"], $cab["identUnicaDb"], $cab["notaMedia"], $cab["cd"]);
                        }
                    }
                    $this->html .="</tr>";
                }
            }

            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Média Anual (MA)</td>";

            $ultimaClasse = $this->ultimaClasse($this->idPCurso);
            $labelLamborne="";
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                if($classe["identificador"]!=$ultimaClasse){
                    foreach($cabecalhos[$classe["identificador"]] as $cab){
                        $labelLamborne .=$this->retornarNota("", "", $cab["identUnicaDb"], 10);
                    }
                }
            }
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                if($classe["identificador"]==$ultimaClasse){

                    for($i=0; $i<(count($cabecalhos[$classe["identificador"]])-1); $i++) {
                        $labelLamborne .=$this->retornarNota("", "", $cab["identUnicaDb"], 10);
                    }
                    break;
                }
            }
            $this->html .=$labelLamborne.$this->tratarVermelha($PC, "font-weight:bolder;", 10)."</tr>";
            
            //Nota da PAP...
            $this->notaAptidaoEstagio($this->sobreAluno);
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Prova de Aptidão Profissional (PAP)</td>";

            $this->html .=$labelLamborne.$this->tratarVermelha($this->PAP, "font-weight:bolder;", 10)."</tr>";
            
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Nota de Estágio Curricular (NEC)</td>";

            $this->html .=$labelLamborne.$this->tratarVermelha($this->NEC, "font-weight:bolder;", 10)."</tr>";
            
            $MFC = ($PC*4+$this->PAP+$this->NEC)/6;
            $MFC = number_format($MFC, 0);
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Média Final do Curso - MFC (4*MA+PAP+NEC)/6</td>".$labelLamborne.$this->tratarVermelha($MFC, "font-weight:bolder;", 10)."</tr>";

            $this->html .="
            </table></div><br/>".$this->assinaturaDirigentes(8);
            $this->html .="</body></html>";
            
            $this->exibir("", "Termo de Aproveitamento-".valorArray($this->sobreAluno, "nomeAluno"), "", "A4", "landscape");

        }

        private function retornarNota($classe, $idPDisciplina, $campo, $notaMedia, $cd=0){
            $valor="";
            if($campo=="notaFinal" && $this->continuidadeDisciplinaNestaClasse($classe, $idPDisciplina)!="T"){
                $valor="";
            }else{
                foreach ($this->notas as $nota) {
                    if($nota["classePauta"]==$classe && $nota["idPautaDisciplina"]==$idPDisciplina){
                        $valor = nelson($nota, $campo);
                        break;
                    }
                }
            }
            return $this->tratarVermelha($valor, "", $notaMedia, $cd);
        }

        private function  continuidadeDisciplinaNestaClasse($classe, $idPDisciplina){
            $continuidade="";
            foreach ($this->todasDisciplinas as $todas) {
                if($todas["classeDisciplina"]==$classe && $todas["idPNomeDisciplina"]==$idPDisciplina){
                    $continuidade = $todas["continuidadeDisciplina"];
                    break;
                }
            }
            return $continuidade;
        }


        private function dadosSobreAClasse($classe){

            $reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["classeReconfirmacao=".$classe, "idMatCurso=".$this->idPCurso, "idReconfEscola=".$_SESSION['idEscolaLogada']], "nao", "dataReconf DESC");
 
            if(count($reconfirmacao)<=0){

                $this->observacaoF[$classe]="A";

                $dadosatraso = listarItensObjecto($this->sobreAluno, "dadosatraso", ["classeAnterior=".$classe, "idCurso=".$this->idPCurso, "idDEscola=".$_SESSION['idEscolaLogada']]);

                $this->numAno = $this->selectUmElemento("anolectivo", "numAno", ["idPAno"=>valorArray($dadosatraso, "anoAnterior")]);
                $this->turma = valorArray($dadosatraso, "turmaAnterior");
                $this->numeroAnterior = valorArray($dadosatraso, "numeroAnterior");

            }else{
                $this->idPAno = valorArray($reconfirmacao, "idReconfAno");
                $this->numAno();

                $this->observacaoF[$classe]=valorArray($reconfirmacao, "observacaoF");

                $this->turma = valorArray($reconfirmacao, "designacaoTurma");

                 $i=0;
                foreach ($this->selectCondClasseCurso("array", "alunosmatriculados", ["idPMatricula", "nomeAluno"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.idMatCurso"=>valorArray($reconfirmacao, "nomeTurma")], $classe, ["escola.idMatCurso"=>$this->idPCurso], ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1]) as $p) { 
                    $i++;
                    if($p["idPMatricula"]==$this->idPMatricula){
                        $this->numeroAnterior = $i;
                        break;
                    }
                }
            }

            if((int)explode("/", $this->numAno)[0]<2020){
                $this->modeloConsiderar="antigo";
            }else{
                $this->modeloConsiderar="actual";
            }
            $this->numeroAnterior = completarNumero($this->numeroAnterior);
        }
    }

?>