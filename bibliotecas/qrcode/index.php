<?php       
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';

        QRcode::png('NOME DO ALUNO: AFONSO LUZINGU
CURSO/OPÇÃO: CIÊNCIAS HUMANAS
NÚMERO DA PAUTA: 12/2021
MÉDIA FINAL DO CURSO: 15
CURSO: CIÊNCIAS ECONÓMICAS E JURÍDICA
LOCALIZAÇÃO: LICEU DO SOYO
BAIRRO:BUNDILA
KUKALAKIAKU', $filename, "L", 4, 2);
        
    //display generated file
    echo '<div style="margin-top:60px;"><img src="'.$PNG_WEB_DIR.basename($filename).'"/></div>';  
    