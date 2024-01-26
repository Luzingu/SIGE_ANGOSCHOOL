<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
     
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoMedioTecnico extends funcoesAuxiliares{
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
            $this->nomeCurso();

            $this->html .="<html style='margin:20px; margin-left:20px; margin-right:20px;'>
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
            <body>

            <div>".$this->fundoDocumento("../../../", "horizontal")."

            <div style='position: absolute;' class='assinaturaDG'>
            <div style='margin-top: 30px; width:250px;'>";

            if($_SESSION['idEscolaLogada']==30){
                $this->html .=$this->porAssinatura("O Director", "Lic. Augusto Alexandre", "", 12);
            }else{
                $this->html .=$this->assinaturaDirigentes(7, "", "", "", "nao");
            }

            $this->html .="</div></div><br/>
            ".$this->cabecalho()."<p style='".$this->bolder.$this->text_center."'>FICHA DE REGISTO DE DADOS BIOGRÁFICOS E ACADÉMICOS</p>
              <div id='fotoAluno'>
                <div style='margin-top: -110px; text-align: right; width: 100%;'>
                    <img src='../../../fotoUsuarios/".valorArray($this->sobreAluno, "fotoAluno")."' style='border:solid #428bca 1px; border-radius: 10px; width: 90px; height: 100px;'>
                </div>
             </div>
              <p style='".$this->text_center.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;CURSO: <strong>".$this->nomeCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;PROCESSO N.º ".valorArray($this->sobreAluno, "numeroProcesso", "escola")."</p>
              <div style='border:solid black 2px; padding:4px;'>
                <p style='margin-bottom:7px; margin-top:0px;'>Nome: <strong>".valorArray($this->sobreAluno, "nomeAluno")."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno")."</p>

                <p style='margin-bottom:0px; margin-top:-5px;'>Natural de <strong>".valorArray($this->sobreAluno, "nomeComuna")."</strong>&nbsp;&nbsp;&nbsp;nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno"))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portador".$this->art2." do BI n.º ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "biAluno"), 10)."&nbsp;&nbsp;&nbsp;&nbsp;Morada: ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")]).", &nbsp;&nbsp;&nbsp;&nbsp;Telefone n.º ".valorArray($this->sobreAluno, "telefoneAluno")."</p>
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
                    if(nelson($nota, "cf")>0){

                        $totalDisc++;
                        if(nelson($nota, "recurso")>0){
                            $totalNotas += floatval(nelson($nota, "recurso"));
                        }else{
                            $totalNotas += floatval(nelson($nota, "cf"));
                        }
                    }
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

            $this->html .="<table style='".$this->tabela."width:100%;margin-top:10px;font-size:9pt;'>
            <tr style='".$this->corDanger."'>
                <td style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."' rowspan='2'>Disciplinas</td>";

                //Pegando os dados sobre a classe...
                foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                    $this->dadosSobreAClasse($classe["identificador"]);
                    $cabecalhos[$classe["identificador"]]=$this->cabecalhoTermpAproveitamento($this->idPAno, $this->idPCurso, $classe["identificador"]);

                    $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='".$this->border()."font-size:8pt;padding:2px;'>
                    <p style='margin-top:0px; margin-bottom:0px;font-size:8pt;".$this->maiuscula."'>ANO LECT. <strong>".$this->numAno."</strong>&nbsp;&nbsp;<strong>".$classe["designacao"]."</strong></p>
                    <p style='margin-top:0px; margin-bottom:0px;font-size:8pt;".$this->maiuscula."'>TURMA: <strong>".$this->turma."</strong>&nbsp;&nbsp;N.º <strong>".$this->numeroAnterior."</strong></p>
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
                $this->html .="<tr><td style='".$this->bolder.$this->border().$this->text_justify."width:50px; font-size:10pt; border:none;' colspan='10'>".tipoDisciplina($tipo)."</td></tr>"; 
                foreach (array_filter($this->listaDisciplinas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html.="<tr><td style='".$this->border()."'>".$disciplina["nomeDisciplina"]."</td>";
                    foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                        foreach($cabecalhos[$classe["identificador"]] as $cab){
                            $this->html .=$this->retornarNota($classe["identificador"], $disciplina["idPNomeDisciplina"], $cab["identUnicaDb"], $cab["notaMedia"], $cab["cd"]);
                        }
                    }
                    $this->html .="</tr>";
                }
            }

            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Média</td>";

                //Pegando os dados sobre a classe...
                foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                    for($t=1; $t<=count($cabecalhos[$classe["identificador"]]); $t++){
                        
                        if($t==(count($cabecalhos[$classe["identificador"]])-2)){
                            $mediaClasse = $this->calculadorMediaPorClasse($classe["identificador"]);

                            $this->html .=$this->tratarVermelha($mediaClasse, "font-weight:bolder;", 10);
                        }else{
                            $this->html .="<td style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";
                        }
                    }
                    
                }
            $this->html .="</tr>";
            
            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Situação do(a) aluno(a)</td>";

                //Pegando os dados sobre a classe...
                foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                    $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px; color:darkblue;'>Apto(a)
                    </td>";
                }
            $this->html .="</tr>";
            
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Classificação Final do Plano Curricular (PC)</td>";

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
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Classificação da Prova de Aptidão Profissional (PAP)</td>";

            $this->html .=$labelLamborne.$this->tratarVermelha($this->PAP, "font-weight:bolder;", 10)."</tr>";
            
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Estágio Curricular Supervisionado (EC)</td>";

            $this->html .=$labelLamborne.$this->tratarVermelha($this->NEC, "font-weight:bolder;", 10)."</tr>";
            
            $MFC = ($PC*4+$this->PAP+$this->NEC)/6;
            $MFC = number_format($MFC, 0);
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Classificação Final do Curso (4*PC+PAP+EC)/6</td>".$labelLamborne.$this->tratarVermelha($MFC, "font-weight:bolder;", 10)."</tr>";
            
            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Assinatura do(a) Coordenador(a) do curso</td>";

            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";   
            }
            $this->html .="</tr>
            <tr><td style='".$this->border().$this->bolder."'>Assinatura do(a) Subdirector(a) Pedagógico(a)</td>";
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";   
            }
            $this->html .="</tr></table></div><br/></body></html>";
            
            $this->exibir("", "Termo de Aproveitamento-".valorArray($this->sobreAluno, "nomeAluno"), "Termo de Aproveitamento-".valorArray($this->sobreAluno, "nomeAluno"), "A4", "landscape");
 
        }

        private function retornarNota($classe, $idPDisciplina, $campo, $notaMedia, $cd = 0){
            $valor="";
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe && $nota["idPautaDisciplina"]==$idPDisciplina){
                    $valor = nelson($nota, $campo);
                    if($valor==0){
                        $valor="";
                    }
                    break;
                }
            }
            return $this->tratarVermelha($valor, "", $notaMedia, $cd);
        }

        private function  continuidadeDisciplinaNestaClasse($classe, $idPDisciplina){
            $continuidade="";
            foreach ($this->notas as $todas) {
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
                foreach ($this->selectCondClasseCurso("array", "alunosmatriculados", ["idPMatricula", "nomeAluno"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.nomeTurma"=>valorArray($reconfirmacao, "nomeTurma")], $classe, ["reconfirmacoes.idMatCurso"=>$this->idPCurso], ["reconfirmacoes"], "", [], ["nomeAluno"=>1]) as $p) { 
                    $i++;
                    if($p["idPMatricula"]==$this->idPMatricula){
                        $this->numeroAnterior = $i;
                        break;
                    }
                }
            }
            $this->numeroAnterior = completarNumero($this->numeroAnterior);
        }
        
        private function calculadorMediaPorClasse($classe){
            $totalDisc=0;
            $totalNotas=0;
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe){
                    if(nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!=""){
                        $nota["mf"]=nelson($nota, "recurso");
                    }
                    if(nelson($nota, "exameEspecial")!=NULL && nelson($nota, "exameEspecial")!=""){
                        $nota["mf"]=nelson($nota, "exameEspecial");
                    }
                    $nota["mf"] = number_format(nelson($nota, "mf"), 0);
                    $totalNotas +=$nota["mf"];
                    $totalDisc++;
                }
            }
            if($totalNotas<=0){
                return 0;
            }else{
                return number_format(($totalNotas/$totalDisc), 0);
            }
        }
    }

?>