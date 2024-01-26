<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoMedioPorSemestre extends funcoesAuxiliares{
        public $idPMatricula="";
        public $aluno = array();
        public $art1="";
        public $art2="";
        public $modeloConsiderar="actual";
        public $disciplinas = array(); 

        public $cabecalhos10=array();
        public $cabecalhos11=array();
        public $cabecalhos12=array();
        public $cabecalhos13=array();

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
            <div style='margin-top: 30px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div><br/>
            ".$this->cabecalho()."<p style='".$this->bolder.$this->text_center."'>FICHA DE REGISTO DE DADOS BIOGRÁFICOS E ACADÉMICOS</p>
              <div id='fotoAluno'>
                <div style='margin-top: -110px; text-align: right; width: 100%;'>
                    <img src='../../../fotoUsuarios/".valorArray($this->sobreAluno, "fotoAluno")."' style='border:solid #428bca 1px; border-radius: 10px; width: 90px; height: 100px;'>
                </div>
             </div>
              <p style='".$this->text_center.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;CURSO: <strong>".$this->nomeCurso."</strong>&nbsp;&nbsp;&nbsp;&nbsp;PROCESSO N.º ".valorArray($this->sobreAluno, "numeroProcesso", "escola")."</p>
              <div style='border:solid black 2px; padding:4px;'>
                <p style='margin-bottom:7px; margin-top:0px;'>Nome: <strong>".valorArray($this->sobreAluno, "nomeAluno")."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno")."</p>

                <p style='margin-bottom:0px; margin-top:-5px;'>Natural de <strong>".valorArray($this->sobreAluno, "nomeMunicipio")."</strong>&nbsp;&nbsp;&nbsp;nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno"))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portador".$this->art2." do BI n.º ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "biAluno"), 10)."&nbsp;&nbsp;&nbsp;&nbsp;Morada: ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")]).", &nbsp;&nbsp;&nbsp;&nbsp;Telefone n.º ".valorArray($this->sobreAluno, "telefoneAluno")."</p>
              </div>";

               $notas =array();
                for($i=10; $i<=13; $i++){
                    $notas = array_merge($notas, $this->notasDeclaracao($i)); 
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

            //cabecalhos1 para as 10 e 11 actualmente
            //cabecalhos1 para as 12 e 13 actualmente
            //cabecalhos1 todas classes antigamente...

            $cabecalhos[] = array('titulo'=>"MT", "tituloDb"=>"mfd");

            $cabecalhos[] = array('titulo'=>"NE", "tituloDb"=>"exame");

            $cabecalhos[] = array('titulo'=>"CS", "tituloDb"=>"mf");

            $cabecalhos[] = array('titulo'=>"CFD", "tituloDb"=>"cf");
            $cabecalhos[] = array('titulo'=>"REC", "tituloDb"=>"recurso");

            $semestres[]="I";
            $semestres[]="II";

            $this->html .="<table style='".$this->tabela."width:100%;margin-top:10px;font-size:9pt;'>
            <tr style='".$this->corDanger."'>
                <td style='".$this->border().$this->bolder.$this->maiuscula.$this->text_center."' rowspan='3'>Disciplinas</td>";

            //Pegando os dados sobre a classe...
            for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                $this->dadosSobreAClasse($i);

                $this->html .="<td colspan='".(count($cabecalhos)*2)."' style='".$this->border()."font-size:10pt;padding:2px;'>
                <p style='margin-top:0px; margin-bottom:0px;font-size:8pt;".$this->maiuscula."'>ANO LECT. <strong>".$this->numAno."</strong>&nbsp;&nbsp;<strong>".$i."ª</strong> CLASSE</p>
                <p style='margin-top:0px; margin-bottom:0px;font-size:8pt;".$this->maiuscula."'>TURMA: <strong>".$this->turma."</strong>&nbsp;&nbsp;N.º <strong>".$this->numeroAnterior."</strong></p>
                </td>";
            }
            $this->html .="</tr><tr style='".$this->corDanger."'>";

            for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                foreach($semestres as $s){
                     $this->html .="<td colspan='".count($cabecalhos)."' style='".$this->border().$this->text_center.$this->bolder."font-size:8pt;padding:2px;'>".$s." SEMESTRE</td>";
                }
            }
            $this->html .="</tr>";

            $this->html .="<tr style='".$this->corDanger."'>";
            foreach($semestres as $s){
                for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                    foreach ($cabecalhos as $cab) {
                       $this->html .="<td style='".$this->border().$this->text_center."'>".$cab["titulo"]."</td>";
                    }
                }
            }
            $this->html .="</tr>";

            $semestres = ["I", "II"];
            foreach (distinct2($this->listaDisciplinas, "tipoDisciplina") as $tipo) {
                $this->html .="<tr><td style='".$this->bolder.$this->border().$this->text_justify."width:50px; font-size:10pt; border:none;' colspan='10'>".tipoDisciplina($tipo)."</td></tr>";

                foreach (array_filter($this->listaDisciplinas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html.="<tr><td style='".$this->border()."'>".$disciplina["nomeDisciplina"]."</td>";

                    for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                        foreach($semestres as $s){
                            foreach ($cabecalhos as $cab) {
                                $campo =$cab["tituloDb"];
                               $this->html .=$this->retornarNota($i, $disciplina["idPNomeDisciplina"], $campo, $s);
                            }
                        }
                    }
                    
                    $this->html .="</tr>";
                }
                
            }

            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Média</td>";

                //Pegando os dados sobre a classe...
                for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                    foreach($semestres as $s){

                        for($t=1; $t<=count($cabecalhos); $t++){
                            
                            if($t==(count($cabecalhos)-2)){
                                $mediaSemestre = $this->calculadorMediaPorSemestre($i, $s);
                                
                                $this->html .=$this->tratarVermelha($mediaSemestre, "font-weight:bolder;");
                            }else{
                                $this->html .="<td style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";
                            }
                        }
                    }
                    
                }
            $this->html .="</tr>

            <tr >
                <td style='".$this->border().$this->bolder."'>Situação do(a) aluno(a)</td>";

                //Pegando os dados sobre a classe...
                for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){

                    $this->html .="<td colspan='".(count($cabecalhos)*2)."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px; color:darkblue;'>Apto(a)
                    </td>";
                }
            $this->html .="</tr>";
            
            
            //Nota PC...
            $PC=0;
            $totClasses=0;
            for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                foreach($semestres as $s){
                    $PC +=$this->calculadorMediaPorSemestre($i, $s);
                }
            }
           
            $PC = $PC/((int)$this->duracaoCurso*2);
            
            $PC = number_format($PC, 0);
            
            $this->notaAptidaoEstagio($this->sobreAluno);
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Classificação Final do Plano Curricular (PC)</td>";

            for($i=1; $i<=($this->duracaoCurso*count($cabecalhos)*2-1); $i++){
                $campo =$cab["tituloDb"];
                $this->html .=$this->retornarNota("", "", "", "");
                               
            }
            $this->html .=$this->tratarVermelha($PC, "font-weight:bolder;");
            $this->html .="</tr>";
            
            
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Estágio Curricular Supervisionado (EC)</td>";

            for($i=1; $i<=($this->duracaoCurso*count($cabecalhos)*2-1); $i++){
                $campo =$cab["tituloDb"];
                $this->html .=$this->retornarNota("", "", "", "");
                               
            }
            $this->html .=$this->tratarVermelha($this->NEC, "font-weight:bolder;");
            $this->html .="</tr>";
            
            $MFC = ($PC*2+$this->NEC)/3;
            $MFC = number_format($MFC, 0);
            //Nota do Estágio...
            $this->html .="<tr><td style='".$this->border().$this->bolder."font-size:9pt;'>Classificação Final do Curso (2*PC+EC)/3</td>";

            for($i=1; $i<=($this->duracaoCurso*count($cabecalhos)*2-1); $i++){
                $this->html .=$this->retornarNota("", "", "", "");               
            }
            $this->html .=$this->tratarVermelha($MFC, "font-weight:bolder;");
            $this->html .="</tr>";
            
            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Assinatura do(a) Coordenador(a) do curso</td>";

            //Pegando os dados sobre a classe...
            for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                $this->html .="<td colspan='".(count($cabecalhos)*2)."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";
            }
            $this->html .="</tr>";
            
            $this->html .="<tr >
                <td style='".$this->border().$this->bolder."'>Assinatura do(a) Subdirector(a) Pedagógico(a)</td>";
            //Pegando os dados sobre a classe...
            for($i=10; $i<=(9+(int)$this->duracaoCurso); $i++){
                $this->html .="<td colspan='".(count($cabecalhos)*2)."' style='".$this->border().$this->text_center.$this->maiuscula."font-size:8pt;padding:2px;'></td>";
            }
            $this->html .="</tr>";

            $this->html .="

            </table></div><br/>";

               
               // $this->exibirTermo2();
            $this->html .="</body></html>";
            
            $this->exibir("", "Termo de Aproveitamento-".valorArray($this->sobreAluno, "nomeAluno"), "Termo de Aproveitamento-".valorArray($this->sobreAluno, "nomeAluno"), "A4", "landscape");
 
        }

        private function retornarNota($classe, $idPDisciplina, $campo, $semestre){
            $valor="";
            if($campo=="cf" && $this->continuidadeDisciplinaNestaClasse($classe, $semestre, $idPDisciplina)!="T"){
                $valor="";
            }else{
                foreach ($this->notas as $nota) {
                    if($nota["classePauta"]==$classe && $nota["idPautaDisciplina"]==$idPDisciplina && $nota["semestrePauta"]==$semestre){
                        $valor = nelson($nota, $campo);
                        if($valor==0){
                            $valor="";
                        }
                        break;
                    }
                }
            }
            return $this->tratarVermelha($valor);
        }

        private function  continuidadeDisciplinaNestaClasse($classe, $semestre, $idPDisciplina){
            $continuidade="";
            foreach ($this->notas as $todas) {
                if($todas["classeDisciplina"]==$classe && $todas["idPNomeDisciplina"]==$idPDisciplina && $todas["semestreDisciplina"]==$semestre){
                    $continuidade = $todas["continuidadeDisciplina"];
                    break;
                }
            }
            return $continuidade;
        }


        private function dadosSobreAClasse($classe){

            $reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["classeReconfirmacao=".$classe, "idReconfEscola=".$_SESSION['idEscolaLogada']], "nao", "dataReconf DESC");
 
            if(count($reconfirmacao)<=0){

                $this->observacaoF[$classe]="A";

                $dadosatraso = listarItensObjecto($this->sobreAluno, "dadosatraso", ["classeAnterior=".$classe, "idDEscola=".$_SESSION['idEscolaLogada']]);

                $this->numAno = valorArray($dadosatraso, "anoAnterior");
                $this->turma = valorArray($dadosatraso, "turmaAnterior");
                $this->numeroAnterior = valorArray($dadosatraso, "numeroAnterior");

            }else{
                $this->idPAno = valorArray($reconfirmacao, "idReconfAno");
                $this->numAno();

                $this->observacaoF[$classe]=valorArray($reconfirmacao, "observacaoF");

                $this->turma = valorArray($reconfirmacao, "designacaoTurma");

                 $i=0;
                foreach ($this->selectCondClasseCurso("array", "alunosmatriculados", ["idPMatricula", "nomeAluno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.nomeTurma"=>valorArray($reconfirmacao, "nomeTurma")], $classe, ["escola.idMatCurso"=>$this->idPCurso], ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1]) as $p) { 
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
        
        private function calculadorMediaPorSemestre($classe, $semestre){
            $totalDisc=0;
            $totalNotas=0;
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe && $nota["semestrePauta"]==$semestre){
                    if(nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!=""){
                        $nota["mf"]=nelson($nota, "recurso");
                    }
                    if(nelson($nota, "exameEspecial")!=NULL && nelson($nota, "exameEspecial")!=""){
                        $nota["mf"]=nelson($nota, "exameEspecial");
                    }
                    $nota["mf"] = number_format(nelson($nota, "mf"), 0);
                    $totalNotas +=nelson($nota, "mf");
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