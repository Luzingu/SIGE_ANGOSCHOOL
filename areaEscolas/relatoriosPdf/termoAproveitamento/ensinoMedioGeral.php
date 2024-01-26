<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoGeral extends funcoesAuxiliares{
        public $idPMatricula="";
        public $aluno = array();
        public $art1="";
        public $art2="";
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

            $this->html .="<html style='margin:0px; margin-left:20px; margin-right:20px;'>
            <head>
                <title>Termo de Aproveitamento</title>
                <style>
                    p{
                        font-size:10pt;
                    }
                    .assinaturaDG p{
                        font-size:12pt !important;   
                    }
                </style>
            </head>
            <body>

            <div>".$this->fundoDocumento("../../../", "horizontal").$this->cabecalho()."<p style='".$this->bolder.$this->text_center."'>TERMO DE APROVEITAMENTO</p>
              <div id='fotoAluno'>
                <div style='margin-top: -130px; text-align: right; width: 100%;'>
                    <img src='../../../fotoUsuarios/".valorArray($this->sobreAluno, "fotoAluno")."' style='border:solid #428bca 1px; border-radius: 10px; width: 120px; height: 120px;'>
                </div>
             </div>
              <p style='".$this->text_center.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."&nbsp;&nbsp;&nbsp;&nbsp;PROCESSO N.º ".valorArray($this->sobreAluno, "numeroProcesso", "escola")."</p>
              <div style='border:solid black 2px; padding:4px;'>
                <p style='margin-bottom:7px; margin-top:0px;'>Nome: <strong>".valorArray($this->sobreAluno, "nomeAluno")."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno")."</p>

                <p style='margin-bottom:0px; margin-top:0px;'>Natural de <strong>".valorArray($this->sobreAluno, "nomeMunicipio")."</strong>&nbsp;&nbsp;&nbsp;nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno"))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portador".$this->art2." do BI n.º ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "biAluno"), 10)."&nbsp;&nbsp;&nbsp;&nbsp;Morada: ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")]).", &nbsp;&nbsp;&nbsp;&nbsp;Telefone n.º ".valorArray($this->sobreAluno, "telefoneAluno")."</p>
              </div>";

            $notas =array();
            foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                $notas = array_merge($notas, $this->notasDeclaracao($classe["identificador"], $this->idPCurso));
            }
            $notas = ordenar($notas, "ordenacao ASC");

            $this->notas = array();
            foreach ($notas as $nota) {
                if(nelson($nota, "mf")>0 || nelson($nota, "recurso")>0){
                    $this->notas[]=$nota;
                }
            }

            $this->listaDisciplinas = array();
            foreach (distinct2($this->notas, "idPNomeDisciplina") as $idPNomeDisciplina) {
                $nomeDisc="";
                $tipo="";

                if($idPNomeDisciplina!=54){
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
            }

            $cabecalhos=array();

            $this->html .="<table style='".$this->tabela."width:100%;margin-top:10px;font-size:9pt; border:none;'>
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

                $this->html .="<tr><td style='".$this->bolder.$this->border().$this->text_justify."width:50px; font-size:10pt; border:none;'>".tipoDisciplina($tipo)."</td></tr>"; 

                foreach (array_filter($this->listaDisciplinas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html.="<tr><td style='".$this->border()."width:230px; font-size:10pt;'>".$disciplina["nomeDisciplina"]."</td>";

                    foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){
                        foreach($cabecalhos[$classe["identificador"]] as $cab){

                            $this->html .=$this->retornarNota($classe["identificador"], $disciplina["idPNomeDisciplina"], $cab["identUnicaDb"], $cab["notaMedia"], $cab["cd"]);
                        }
                    }

                    $this->html .="</tr>";
                }
            }

            $this->html .="<tr style='border:none;'>";
            if($_SESSION['idEscolaLogada']==11){
                $this->html .="<td style='border:none;'></td>";
                foreach(listarItensObjecto($this->sobreCursoAluno, "classes") as $classe){

                    $this->html .="<td colspan='".count($cabecalhos[$classe["identificador"]])."' style='height:100px; border:none; padding-top:0px;'>".$this->assinaturaDirigentes(8)."</td>";
                }
            }
            $this->html .="</tr></table>";
            if($_SESSION['idEscolaLogada']!=11){
                $this->html .=$this->assinaturaDirigentes(8);
            }
            $this->html .="</div></body></html>";
            
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
            foreach ($this->notas as $todas) {
                if($todas["classeDisciplina"]==$classe && $todas["idPNomeDisciplina"]==$idPDisciplina){
                    $continuidade = $todas->continuidadeDisciplina;
                    break;
                }
            }
            return $continuidade;
        }


        private function dadosSobreAClasse($classe){
            $reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["classeReconfirmacao=".$classe, "idMatCurso=".$this->idPCurso, "idReconfEscola=".$_SESSION['idEscolaLogada']], "nao", "dataReconf DESC");
 
            if(count($reconfirmacao)<=0){
                $this->observacaoF[$classe]="A";
                $dadosatraso = listarItensObjecto($this->sobreAluno, "dadosatraso", ["classeAnterior=".$classe, "idDEscola=".$_SESSION['idEscolaLogada']]);

                $this->idPAno = valorArray($dadosatraso, "anoAnterior");
                $this->turma = valorArray($dadosatraso, "turmaAnterior");
                $this->numeroAnterior = valorArray($dadosatraso, "numeroAnterior");
                $this->numAno();
            }else{
                $this->idPAno = valorArray($reconfirmacao, "idReconfAno");
                $this->numAno();

                $this->observacaoF[$classe]=valorArray($reconfirmacao, "observacaoF");

                $this->turma = valorArray($reconfirmacao, "designacaoTurma");

                 $i=0;
                foreach ($this->selectCondClasseCurso("array", "alunosmatriculados", ["idPMatricula", "nomeAluno"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.nomeTurma"=>valorArray($reconfirmacao, "nomeTurma")], $classe, ["reconfirmacoes.idMatCurso"=>$this->idPCurso], ["escola"], "", [], ["nomeAluno"=>1]) as $p) { 
                    $i++;
                    if($p["idPMatricula"]==$this->idPMatricula){
                        $this->numeroAnterior = $i;
                        break;
                    }
                }
            }
            $this->numeroAnterior = completarNumero($this->numeroAnterior);
        }
    }

?>