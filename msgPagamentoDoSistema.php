<?php 
    function verificacaoPrazoPagamentoInstituicoes($db, $saudacao, $nomeUsuario){
        $retorno = "server";
        $mesesCitar="";

        $sobreContrato = $db->selectArray("escolas", ["contrato.tipoPagamento", "contrato.fimPrazoPosPago", "contrato.mesesConsecutivosParaBloquear", "contrato.inicioPrazoPosPago", "contrato.valorPagoPor15Dias", "contrato.saldoParaPagamentoPosPago"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["contrato"]);

        if(valorArray($sobreContrato, "tipoPagamento", "contrato")=="nao"){
            $retorno .="V";
        }else if(valorArray($sobreContrato, "tipoPagamento", "contrato")=="pos"){

            if(valorArray($sobreContrato, "fimPrazoPosPago", "contrato")=="" || valorArray($sobreContrato, "fimPrazoPosPago", "contrato")==NULL){
                $retorno .="V";
            }else{
                $diasPrazo = calcularDiferencaEntreDatas(valorArray($sobreContrato, "fimPrazoPosPago", "contrato"), $db->dataSistema);

                if($diasPrazo>=1 && $diasPrazo<=20){

                    if(seOficialEscolar()){
                                                                
                            $retorno .="<i class='fa fa-warning fa-4x'></i>&nbsp;&nbsp;&nbsp;<i class='fa fa-warning fa-4x text-danger'></i>&nbsp;&nbsp;&nbsp;<i class='fa fa-warning fa-4x'></i><br/><br/>Senhor(a) <strong>".$nomeUsuario."</strong>, ";

                            if($diasPrazo==0){
                                $retorno .="<strong class='text-danger' style='font-size:20pt;'>HOJE</strong> é o último dia";
                            }else if($diasPrazo==1){
                                $retorno .="<strong class='text-danger' style='font-size:20pt;'>AMANHÃ</strong> será o último dia";
                            }else{
                                $retorno .="faltam apenas <strong class='text-danger' style='font-size:20pt;'>".$diasPrazo." DIAS </strong>";
                            }
                            $retorno .=" do tempo máximo permitido para concluir o pagamento de todos meses (em dívida) do Sistema.";
                            
                            $retorno .="<br/>Devem concluir o pagamento, antes do término do prazo, para que todos alunos e professores d".$db->art1Escola." <strong>".valorArray($db->sobreUsuarioLogado, "nomeEscola")."</strong> continuem a usufruir dos serviços do AngoSchool sem nenhuma interrupção.";
                    }else{
                        $retorno .="V";
                    }
                }else if($diasPrazo<=0){
                    if(seOficialEscolar()){                            
                        $retorno .=$saudacao."senhor(a) <strong>".$nomeUsuario."</strong>. Informamos que findou o limite máximo permitido para completar o pagamento do Sistema.";

                        $retorno .="<br/>Devem efectuar (completar) o pagamento dos meses em atraso para que todos alunos e professores d".$db->art1Escola." ".valorArray($db->sobreUsuarioLogado, "nomeEscola")." usufruam dos serviços do AngoSchool sem nenhuma interrupção.";
                    }else{
                       $retorno .=$saudacao."senhor(a) ".$nomeUsuario.". Informamos que terminou o prazo de validade para o uso do Sistema.<br/>Por favor, contacte a direcção  d".$db->art1Escola." ".valorArray($db->sobreUsuarioLogado, "nomeEscola")." para ter mais informações sobre o ocorrido."; 
                    }
                }
            }
        }
        echo $retorno;
    }

