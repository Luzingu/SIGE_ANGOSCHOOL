<?php

include_once "manipulacaoDadosMae.php";
$manipulador = new manipulacaoDadosMae();

function recursiveCopy($source, $destination, $manipulador) {
    if (is_dir($source)) {

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true); // Crie o diretório de destino, se ele não existir
        }

        $directory = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $directorio = DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            $directorio = substr($directorio, 1, strlen($directorio));
            $directorio = str_replace("\\", "/", $directorio);

            $arrayExplodido =  explode("/", $directorio);
            $pastaPrincipal="";
            if(count($arrayExplodido)>1){
                $pastaPrincipal = $arrayExplodido[0];
            }
            $nomeArquivo = $arrayExplodido[(count($arrayExplodido)-1)];

            if($nomeArquivo!="manipulacaoDadosMae.php" && $nomeArquivo!="downloadArquivos.php" && $nomeArquivo!="copiadorArquivos.php" && $pastaPrincipal!="vendor" && $pastaPrincipal!="solicitBackup" && $pastaPrincipal!="areaAdministrador" && $pastaPrincipal!="areaDireccaoProvincial" && $pastaPrincipal!="areaEntretenimento" && $pastaPrincipal!="areaInterConexao" && $pastaPrincipal!="bibliotecas" && $pastaPrincipal!="Ficheiros" && $pastaPrincipal!="fotoUsuarios" && $pastaPrincipal!="login" && $pastaPrincipal!=".git"){

                if($item->isDir()){
                    $dir = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }
                }else{

                    $array = $manipulador->selectArray("menus", ["somenteOnline"], ["linkMenu"=>new \MongoDB\BSON\Regex($directorio)]);
                    $somenteOnline="";
                    if(count($array)>0){
                        $somenteOnline=valorArray($array, "somenteOnline");
                    }else{
                        $array = $manipulador->selectArray("menus", ["subMenus.somenteOnline"], ["subMenus.linkSubMenu"=>new \MongoDB\BSON\Regex($directorio)], ["subMenus"]);
                         $somenteOnline=valorArray($array, "somenteOnline", "subMenus");
                    }
                    if($somenteOnline=="sim"){
                        $directorioExcluir="";
                        $cont=0;
                        foreach($arrayExplodido as $p){
                            $cont++;
                            if($cont!=count($arrayExplodido)){
                                if($directorioExcluir!=""){
                                    $directorioExcluir .="/";
                                }
                                $directorioExcluir .=$p;
                            }
                        }

                        $directorioExcluir = $destination.DIRECTORY_SEPARATOR.$directorioExcluir;
                        if (is_dir($directorioExcluir)) {
                        
                            $files = scandir($directorioExcluir);
                            foreach ($files as $arquivo) {
                                if ($arquivo != "." && $arquivo != "..") {
                                    $path = $directorioExcluir.DIRECTORY_SEPARATOR . $arquivo;
                                    if (is_dir($path)) {
                                        deleteDirectory($path);
                                    } else {
                                        unlink($path);
                                    }
                                }
                            }                        
                            rmdir($dir);
                        }
                    }else{
                        $file = $destination. DIRECTORY_SEPARATOR.$iterator->getSubPathName();   
                        copy($item, $file);
                    }
                }
            }
        }
    }
}
$sourceDirectory = 'C:\xampp\htdocs\angoschool'; // Substitua pelo diretório de origem real
$destinationDirectory = 'C:\xampp\htdocs\AngoSchoolOffline'; // Substitua pelo diretório de destino real

recursiveCopy($sourceDirectory, $destinationDirectory, $manipulador);
?>
