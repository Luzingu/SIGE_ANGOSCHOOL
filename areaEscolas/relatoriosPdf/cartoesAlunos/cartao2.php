<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class cartoesAlunos extends funcoesAuxiliares{
        private $precoCartao=0;
        function __construct($caminhoAbsoluto){            
            parent::__construct("Rel-Cartão de Aluno");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $this->idAlunosVisualizar = isset($_GET["idAlunosVisualizar"])?$_GET["idAlunosVisualizar"]:"";
            $this->html="<html style='margin:0px;'>
            <head>
                <title>Cartões de Estudantes</title>
                <style>
                    p{
                        font-size:10pt !important;
                    }
                </style>
            </head>
            <body style='margin:0px; margin-top:15px; margin-left:".((793-valorArray($this->sobreUsuarioLogado, "tamanhoCartEstudante"))/2)."px;'>";

            $this->nomeCurso();
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["impressaoPersonalizada"], [$this->classe, $this->idPCurso], "")){
                   
                $this->cartoesAlunosLiceu();
            }else{
              $this->negarAcesso();
            }
        }

         private function cartoesAlunosLiceu(){
            
            $idsVisualizar = array();
            foreach(explode(",", $this->idAlunosVisualizar) as $id){
                $idsVisualizar[]=(int)$id;
            }
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, $idsVisualizar, ["nomeAluno"]);


           $this->html .="<table style='margin-bottom: 10px; border: none; width:".valorArray($this->sobreUsuarioLogado, "tamanhoCartEstudante")."px;'>";
            
            $nomeAlunoUnicoExibir="";
             $n=0;
            foreach ($alunos as $aluno) {

                $nomeAlunoUnicoExibir=$aluno["nomeAluno"];
                $posssoTratar="nao";

                $n++;
                if($n%2==1){
                    $this->html .="<tr>";
                }

                $padding = valorArray($this->sobreUsuarioLogado, "alturaCartEstudante")-185;
                
                $this->html .="<td style='padding: 5px; padding-bottom:0px; width: 50%; border: none;'>
                    <div style='border: solid black 1px; border-radius:15px; padding: 5px; padding-top:".($padding*0.1)."px; padding-bottom:-10px; height:".valorArray($this->sobreUsuarioLogado, "alturaCartEstudante")."px;'>
                        <p style='".$this->bolder.$this->text_center."'>PAGAMENTOS ".$this->numAno."</p>
                        <table style='".$this->tabela."width:100%; font-size:7pt;'>
                            <tr>
                                <td style='".$this->border()." width:25%; height:20%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>SETEMBRO 2022</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>OUTUBRO 2022</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>NOVEMBRO 2022</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>DEZEMBRO 2022</td>
                            </tr>
                            <tr>
                                <td style='".$this->border()." width:25%; height:20%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>JANEIRO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>FEVEREIRO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>MARÇO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>ABRIL 2023</td>
                            </tr>
                            <tr>
                                <td style='".$this->border()." width:25%; height:20%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>MAIO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>JUNHO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>JULHO 2023</td>
                                <td style='".$this->border()." width:25%; vertical-align: top !important; padding-top:4px;".$this->text_center."'>AGOSTO 2023</td>
                            </tr>
                        </table>
                    </div>
                </td>";

                //Adicionando um td para evitar exceder o tamanho...
                if(count($alunos)<=1){
                    $this->html .="<td></td>";
                }
                if($n%2==0 || ($n%2==1 && $n==count($alunos))){
                    $this->html .="</tr>";
                }    
            }
            $this->exibir("", "Cartão de Estudante-".$nomeAlunoUnicoExibir."-".$this->numAno);
        }


        
    }

new cartoesAlunos(__DIR__);
    
    
  
?>