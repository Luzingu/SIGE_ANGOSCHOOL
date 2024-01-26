<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaOrganizacaoFuncionarios extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Orgização de Funcionários");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
             if($this->tamanhoFolha!="A0" || $this->tamanhoFolha!="A1" || $this->tamanhoFolha!="A2"){
                $this->tamanhoFolha="A2";
            }
            $this->html="<html style='margin:15px;'>
            <head>
                <title>Mapa de Orgização dos Funcionários</title>
                <style>
                  table tr td{
                    padding:3px;
                  }
                </style>
            </head>
            <body>";

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){
              $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
           
           $sobreEscola = $this->selectArray("escolas",[], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
           $sobreEscola = $this->anexarTabela($sobreEscola, "div_terit_provincias", "idPProvincia", "provincia");
           $sobreEscola = $this->anexarTabela($sobreEscola, "div_terit_municipios", "idPMunicipio", "municipio");
           $sobreEscola = $this->anexarTabela($sobreEscola, "div_terit_comunas", "idPComuna", "comuna");

           $listaFuncionarios = $this->entidades([], "", "V");

            $this->html .="            
              <div style='page-break-after: always;'><div><div style='margin-top:20px; margin-left:50px; width:400px; position:absolute;' style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

            $this->html .="<p style='".$this->text_center.$this->bolder.$this->maiuscula."'>MAPA DE ORGANIZAÇÃO DOS FUNCIONÁRIOS</p><br/>
              <div style='width:50%;'>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>PROVÍNCIA: <strong>".valorArray($sobreEscola, "nomeProvincia")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>MUNICIPIO: <strong>".valorArray($sobreEscola, "nomeMunicipio")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>COMUNA/DISTRITO: <strong>".valorArray($sobreEscola, "nomeComuna")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>UNIDADE ORÇAMENTAL: <strong>DIRECÇÃO PROVINCIAL DA EDUCAÇÃO DO ZAIRE</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>UNIDADE PAGADORA: <strong>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>CÓDIGO DO ORGANISMO: <strong>".valorArray($sobreEscola, "codOrganismo")."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>NÚMERO DE SALAS: <strong>".completarNumero(valorArray($sobreEscola, "numeroSalas"))."</strong></p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>NÚMERO DE FUNCIONÁRIOS: <strong>".completarNumero(count($listaFuncionarios))."</strong></p>
              </div>
              <div style='width:50%; margin-top:-300px; margin-left:50%; width:50%;'>
                <p style='".$this->maiuscula."'>NOME DO DIRECTOR PROVINCIAL: JOSÉ LUÍS AMÉLIA</p>";
                $this->nomeDirigente("Director");
                if($this->sexoDirigente=="M"){
                  $this->html .="<p style='".$this->maiuscula."'>NOME DO DIRECTOR DA ESCOLA: <strong>".$this->nomeDirigente."</strong></p>";
                }else{
                  $this->html .="<p style='".$this->maiuscula."'>NOME DA DIRECTORA DA ESCOLA: <strong>".$this->nomeDirigente."</strong></p>";
                }

                $this->nomeDirigente("Administrativo");
                if($this->sexoDirigente=="M"){
                  $this->html .="<p style='".$this->maiuscula."'>NOME DO SUB-DIRECTOR ADMINISTRATIV0 DA ESCOLA: <strong>".$this->nomeDirigente."</strong></p>";
                }else{
                  $this->html .="<p style='".$this->maiuscula."'>NOME DA SUB-DIRECTORA ADMINISTRATIVA: <strong>".$this->nomeDirigente."</strong></p>";
                }

                $this->nomeDirigente("Pedagógico");
                if($this->sexoDirigente=="M"){
                  $this->html .="<p style='".$this->maiuscula."'>NOME DO SUB-DIRECTOR PEDAGÓGICO DA ESCOLA: <strong>".$this->nomeDirigente."</strong></p>";
                }else{
                  $this->html .="<p style='".$this->maiuscula."'>NOME DA SUB-DIRECTORA PEDAGÓGICA: <strong>".$this->nomeDirigente."</strong></p>";
                }
              $this->html .="</div>";

              $cabecalho[] = array('titulo'=>"Nº", "tituloDb"=>"nº");
              $cabecalho[] = array('titulo'=>"Nº de<br>Agente", "tituloDb"=>"numeroAgenteEntidade");
              $cabecalho[] = array('titulo'=>"Foto Tipo Passe", "tituloDb"=>"foto");
              $cabecalho[] = array('titulo'=>"Nome Completo do Professor", "tituloDb"=>"nomeEntidade");
              $cabecalho[] = array('titulo'=>"Categoria", "tituloDb"=>"categoriaEntidade");
              $cabecalho[] = array('titulo'=>"Função", "tituloDb"=>"funcaoEnt");
              $cabecalho[] = array('titulo'=>"Data Inicio de<br/>Funções", "tituloDb"=>"dataInicioFuncoesEntidade");
              $cabecalho[] = array('titulo'=>"Nº do BI", "tituloDb"=>"biEntidade");
              $cabecalho[] = array('titulo'=>"Curso que Fez no Ensino Médio", "tituloDb"=>"cursoEnsinoMedio");
              $cabecalho[] = array('titulo'=>"Escola Onde Fez Ensino Médio", "tituloDb"=>"escolaEnsinoMedio");
              $cabecalho[] = array('titulo'=>"Curso que Fez na Licenciatura", "tituloDb"=>"cursoLicenciatura");
              $cabecalho[] = array('titulo'=>"Escola Onde Fez a Licenciatura", "tituloDb"=>"escolaLicenciatura");
             $cabecalho[] = array('titulo'=>"Curso que Fez no Pós-Graduação", "tituloDb"=>"cursoMestrado");
              $cabecalho[] = array('titulo'=>"Escola Onde a Pós-Graduação", "tituloDb"=>"escolaMestrado");
              $cabecalho[] = array('titulo'=>"Disciplina(s) que Lecciona", "tituloDb"=>"discLeciona");
              $cabecalho[] = array('titulo'=>"Classe(s) que Lecciona", "tituloDb"=>"classeLeciona");
              $cabecalho[] = array('titulo'=>"Turma(s) que Lecciona", "tituloDb"=>"turmaLeciona");
              $cabecalho[] = array('titulo'=>"Nº do NIF", "tituloDb"=>"numeroContribuinte");
              $cabecalho[] = array('titulo'=>"Telefone", "tituloDb"=>"numeroTelefoneEntidade");
              $cabecalho[] = array('titulo'=>"E-mail", "tituloDb"=>"emailEntidade");



            $this->html .="<table style='".$this->tabela."width:100%;margin-top:60px;font-size:11pt;'>
            <tr style='".$this->corDanger."'>";
            foreach ($cabecalho as $cab) {
                $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>";


            $i=0;
            foreach ($listaFuncionarios as $profs) {

              $divisaoProfessores = $this->selectArray("divisaoprofessores", [], ["idDivEntidade"=>$profs["idPEntidade"], "idDivEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idPAno]);

                $i++;
               if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

               foreach ($cabecalho as $cab) {
                  $valDado=""; 
                  if($cab["tituloDb"]=="nº"){
                    $valDado=completarNumero($i);
                  }else if($cab["tituloDb"]=="foto"){
                    $valDado="<img src='../../../fotoUsuarios/".$profs->fotoEntidade."' style='border:solid #428bca 1px; border-radius: 10px; width: 100px; height: 110px;'><br/>";

                  }else if($cab["tituloDb"]=="dataInicioFuncoesEntidade"){
                    $valDado=converterData($profs["escola"]["dataInicioFuncoesEntidade"]);
                  }else if($cab["tituloDb"]=="discLeciona") {
                      $disc="";
                      foreach(distinct2($divisaoProfessores, "abreviacaoDisciplina1") as $disciplina){
                        if($disc!=""){
                          $disc .=", ";
                        }
                        $disc .=$disciplina;
                      }
                      $valDado = $disc;
                  }else if($cab["tituloDb"]=="classeLeciona") {
                    $classe="";
                    foreach(distinct2($divisaoProfessores, "classe") as $classe){
                      if($classe!=""){
                        $classe .="";
                      }
                      $classe .=$classe;
                    }
                    $valDado = $classe;
                  }else if($cab["tituloDb"]=="turmaLeciona"){
                    $tur="";
                    foreach(distinct2($divisaoProfessores, "classe") as $classe){
                      if($classe>=10){
                        foreach(distinct2($divisaoProfessores, "idPNomeCurso") as $curso){
                          
                          foreach(distinct2(condicionadorArray($divisaoProfessores, ["classe=".$classe, "idPNomeCurso=".$curso]), "designacaoTurmaDiv") as $turma){
                            if($tur!=""){
                              $tur .=", ";
                            }
                            $tur = $this->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$curso]).$classe.$turma;
                          }
                        }
                      }else{
                        foreach(distinct2(condicionadorArray($divisaoProfessores, ["classe=".$classe]), "designacaoTurmaDiv") as $turma){
                          $tur = $classe.$turma;
                        }
                      }
                    }
                    $valDado = $tur;
                  }else{
                    $tDb = $cab["tituloDb"];
                    $valDado = "";
                    if(isset($profs[$tDb])){
                      $valDado = $profs[$tDb];
                    }else if(isset($profs["escola"][$tDb])){
                      $valDado = $profs["escola"][$tDb];
                    }
                  }
                  $this->html .="<td style='".$this->border().$this->text_center."'>".$valDado."</td>";
                }

               $this->html .="</tr>";
            }
            $this->html .="</table>";

            $this->html .="<div style='margin-top:30px;'><p  style='font-size:16pt;".$this->maiuscula.$this->text_center."'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula.$this->text_center."'>".$this->assinaturaDirigentes("Administrativo")."</div><div>

          </div></div></div>";

            $this->exibir("", "Mapa de Orgização dos Funcionários-".$this->numAno, "", $this->tamanhoFolha, "landscape");

            
        }
    }

new mapaOrganizacaoFuncionarios(__DIR__);
?>