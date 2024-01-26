<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

            parent::__construct("Rel-Mapa de Control de Professores por Disciplina");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";

            $this->html="<html>
            <head>
                <style>
                    table tr td{
                        padding:3px;
                    }
                </style>
            </head>
            <body>";


            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica"])){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

           $this->html .="<div class='cabecalho'>
            <div><div style='margin-top:20px; width:400px; position:absolute;".$this->maiuscula."'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho()."
            </div>
            </div><br/>
            <p  style='".$this->text_center.$this->bolder."'>MAPA DE CONTROLO DE PROFESSORES POR DISCIPLINA, NACIONALIDADE E CARGA HORÁRIA</p>";

            $this->html .="<table style='width:100%;".$this->tabela."'>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->text_center.$this->border()."' rowspan='3'>Disciplina que Lecciona</td><td colspan='10' style='".$this->text_center.$this->border()."'>Grau Acadêmico</td><td style='".$this->text_center.$this->border()."' colspan='3' rowspan='2'>Sub-Total</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Nacionalidade</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Tempos/<br/>Semana</td><td colspan='2' style='".$this->text_center.$this->border()."' rowspan='2'>Condição</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Escola que<br/>pertence</td>
                </tr>
                <tr style='".$this->corDanger.$this->bolder."'><td style='".$this->text_center.$this->border()."' colspan='2'>Médio</td><td style='".$this->text_center.$this->border()."' colspan='2'>Bacharel</td><td style='".$this->text_center.$this->border()."' colspan='2'>Licenciado</td><td style='".$this->text_center.$this->border()."' colspan='2'>Mestre</td><td style='".$this->text_center.$this->border()."' colspan='2'>Doutor</td></tr>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td><td style='".$this->text_center.$this->border()."'>MF</td><td style='".$this->text_center.$this->border()."'>Efectivo</td><td style='".$this->text_center.$this->border()."'>Colabo<br/>rador</td>
                </tr>";

            $nomeDisciplinas = $this->selectArray("nomedisciplinas", "*");
            $listaDisciplinas = array();
            foreach ($this->selectArray("disciplinas LEFT JOIN nomedisciplinas ON idPNomeDisciplina=idFNomeDisciplina LEFT JOIN escolas ON idPEscola=idDiscEscola", "DISTINCT idPNomeDisciplina", "estadoDisciplina=:estadoDisciplina AND provincia=:provincia", ["A", valorArray($this->sobreUsuarioLogado, "provincia")], "ordenacao ASC") as $disciplinas) {
                $nomeSeleccionado="";
                foreach ($nomeDisciplinas as $nome) {
                    if($nome->idPNomeDisciplina==$disciplinas->idPNomeDisciplina){
                        $nomeSeleccionado = $nome->nomeDisciplina;
                        break;
                    }
                }
                $listaDisciplinas [] = array("idPNomeDisciplina"=>$disciplinas->idPNomeDisciplina, "nomeDisciplina"=>$nomeSeleccionado);
            }
            $cargos[]="Médio";
            $cargos[]="Bacharel";
            $cargos[]="Licenciado";
            $cargos[]="Mestre";
            $cargos[]="Doutor";

            $i=0;
            foreach ($listaDisciplinas as $lista) {
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->border()."'>".$lista["nomeDisciplina"]."</td>";

                foreach ($cargos as $c) {

                   $this->numeroProfessores($lista["idPNomeDisciplina"], $c, "M", "idDivEntidade");
                    $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                    $this->numeroProfessores($lista["idPNomeDisciplina"], $c, "F", "idDivEntidade");
                    $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                }

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "M", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "F", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "paisNascEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->acumulador."</td>";

                 $this->tempoSemanal($lista["idPNomeDisciplina"]);
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->contador."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "naturezaVinc");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->acumulador."</td>";

                $this->html .="<td style='".$this->text_center.$this->border()."'></td>";

                $this->html .="<td style='".$this->maiuscula.$this->text_center.$this->border()."'>".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")." ".valorArray($this->sobreUsuarioLogado, "municipio")."</td>";


                $this->html .="</tr>";
            }



            $this->html .="</table><div style='margin-top:20px;'><p style='".$this->maiuscula."' style='font-size:16pt;'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes(["Pedagógico", "Administrativo", "Chefe da Secretaria"])."</div><div>";
            

            $this->exibir("", "Mapa de Controlo de Professores por Disciplina, Nacionalidade e Carga Horária-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }
        private function numeroProfessores($idPNomeDisciplina, $nivel, $genero, $campo){
            $this->contador=0;
            $this->acumulador="";

            $condicao="";
            if($nivel!=""){
                $condicao .=" AND nivelAcademicoEntidade='".$nivel."'";
            }
            if($idPNomeDisciplina!=""){
                $condicao .=" AND idDivDisciplina=".$idPNomeDisciplina;
            }

            if($genero!=""){
                $condicao .=" AND generoEntidade='".$genero."'";
            }

            foreach ($this->selectArray("divisaoprofessores LEFT JOIN entidadesprimaria ON idPEntidade=idDivEntidade LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "DISTINCT ".$campo, "provincia=:provincia AND idEntidadeEscola=idDivEscola AND idDivEntidade IS NOT NULL AND idDivAno=:idDivAno AND estadoActividadeEntidade=:estadoActividadeEntidade".$condicao, [valorArray($this->sobreUsuarioLogado, "provincia"), $this->idPAno, "A"]) as $divisao) {
                $this->contador++;
                if($this->acumulador==""){
                    $this->acumulador .= retornarNacionalidade($divisao->$campo);
                }else if($this->acumulador==""){
                    $this->acumulador .= ", ".retornarNacionalidade($divisao->$campo);
                }
            }
        }

        private function tempoSemanal($idPNomeDisciplina){
            $this->contador=0;
            $this->acumulador="";

            foreach ($this->selectArray("horario LEFT JOIN escolas ON idPEscola=idHorEscola", "*", "provincia=:provincia AND idHorAno=:idHorAno AND idHorDisc=:idHorDisc", [valorArray($this->sobreUsuarioLogado, "provincia"), $this->idPAno, $idPNomeDisciplina]) as $divisao) {

                $this->contador++;
                
            }
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>