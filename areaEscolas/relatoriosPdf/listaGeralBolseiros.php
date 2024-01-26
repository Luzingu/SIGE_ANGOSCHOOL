<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class listaTurmas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->nomeTurmaAluno="";
            $this->designacaoTurmaAluno ="";
            $this->perioTurmaAluno="";
            $this->numSalaTurmaAluno ="";
            parent::__construct("Rel-Lista dos Alunos Reconfirmados");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            
            
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", "reconfirmados", array(), "")){                                
                $this->visualizar();
            }else{
              $this->negarAcesso();
            }

        }

         private function visualizar(){

            $arrayCabecalho=array();
            foreach($this->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>array('$nin'=>array(9, 8, 1))], [], "", [], ["idPTipoEmolumento"=>1]) as $a){

                $array =  listarItensObjecto($this->sobreEscolaLogada, "emolumentos", ["idTipoEmolumento=".$a["idPTipoEmolumento"], "valor>0"]);
                if(count($array)>0){
                    $arrayCabecalho[]=array("idPTipoEmolumento"=>$a["idPTipoEmolumento"], "codigoEmolumento"=>$a["codigo"], "mes"=>"", "designacaoEmolumento"=>$a["designacaoEmolumento"]);
                }
            }
            foreach($this->mesesAnoLectivo as $mes){
                $array =  listarItensObjecto($this->sobreEscolaLogada, "emolumentos", ["idTipoEmolumento=1", "mes=".$mes, "valor>0"]);
                if(count($array)>0){
                    $arrayCabecalho[]=array("idPTipoEmolumento"=>1, "codigoEmolumento"=>"propina", "mes"=>$mes, "designacaoEmolumento"=>$mes);
                }
            }

            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Lista geral dos alunos reconfirmados</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:16pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:40px; margin-right:40px;'>
            ".$this->fundoDocumento("../../", "horizontal").$this->cabecalho()."
            <p style='".$this->bolder.$this->text_center."'>LISTA GERAL DOS BOLSEIROS - ".$this->numAno."</p>

            <table style='".$this->tabela." width:100%;'>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."' rowspan='2'>N.º</td>
                <td style='".$this->border()."' rowspan='2'>NOME COMPLETO</td>
                <td style='".$this->border()."' rowspan='2'>IDADE</td>
                <td style='".$this->border()."' rowspan='2'>CURSO</td>
                <td style='".$this->border()."' colspan='".count($arrayCabecalho)."'>VALOR DOS EMOLUMENTOS</td>
            </tr>
            <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>";
            foreach($arrayCabecalho as $a){
                $this->html .="<td style='".$this->border()."'>".$a["designacaoEmolumento"]."</td>";
            }
            $this->html .="</tr>"; 

            $i=0;
            $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "dataNascAluno", "sexoAluno", "reconfirmacoes.idMatCurso", "escola.beneficiosDaBolsa", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idMatCurso"=>$this->idPCurso, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.seBolseiro"=>"V"], ["reconfirmacoes", "escola"], "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $this->idPCurso));

            $alunos = $this->anexarTabela2($alunos, "nomecursos", "reconfirmacoes", "idPNomeCurso", "idMatCurso");

            foreach($alunos as $a){

                $i++;
                $this->html .="
                <tr style='"."'>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a->nomeAluno."</td>
                    <td style='".$this->border().$this->text_center."'>".calcularIdade($this->ano, $a["dataNascAluno"] )."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["abrevCurso"]."</td>";
                foreach($arrayCabecalho as $cab){
                    $this->html .="<td style='".$this->border().$this->text_center."'>".number_format($this->preco($cab["codigoEmolumento"], $a["reconfirmacoes"]["classeReconfirmacao"], $a["reconfirmacoes"]["idMatCurso"], $cab["mes"], $a), 0, ",", ".")."</td>";
                }

                $this->html .="</tr>";
            }
            

            
            $this->html .="</table>
            </div><div>
            <p style='padding-left:10px; padding-right:10px;'>".$this->rodape().".</p>
            <div>".$this->assinaturaDirigentes(7)."</div>";

            $this->exibir("", "Lita geral dos bolseiros-".$this->numAno, "", "A3", "landscape"); 
        }

        private function dadosDeTurma($idPCurso, $classe, $turma){
            foreach ($this->sobreTurma as $l){
                if(($l["idPNomeCurso"]==$idPCurso || $classe<=9) && $l["classe"]==$classe && $l["nomeTurma"]==$turma){

                    $this->perioTurmaAluno=$l["periodoT"];
                    $this->numSalaTurmaAluno =$l["numeroSalaTurma"];
                    break;
                }
            }
        }
    }

new listaTurmas(__DIR__);
    
    
  
?>