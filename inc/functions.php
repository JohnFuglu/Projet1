<?php
session_start();
/*teste si le fichier existe dans le dossier original*/
function fileExists(string $dir):bool{
    $_SESSION['resolution']=$_POST['radio'];
    $dirOriginals= scandir($dir);
        if($dirOriginals){//si il y a qqchose dans le dossier
            foreach($dirOriginals as $f){
                if(str_contains($f, $_SESSION['nomFichier'])){
                    list($width,$height)=getSize(ORIGINALS_DIR.'/'.$_SESSION['nomFichier'].'.'.$_SESSION['extension']);
                    $_SESSION['imgSrc']=ORIGINALS_DIR.'/'.$f;
                    return true; 
                }
            }
            return false;
        }   
    }
    
function fileIsResized(string $dir,string $type):bool{
    $dirRes= scandir($dir);
        if($dirRes){//si il y a qqchose dans le dossier
            foreach($dirRes as $f){
                if($f !== '.'&&$f!== '..'){
                    $needle=$_SESSION['nomFichier'];
                    if(strpos($f,$needle) && str_contains($f,$type)){
                        $path='/opt/lampp/htdocs/Projet1/Assets/resized/'.$f;
                        list($width,$height)= getimagesize($path);
                        $_SESSION['resSizeH']=$height;
                        $_SESSION['resSizeW']=$width;
                        $_SESSION['resizedImage']='/Projet1/Assets/resized/'.$f;
                    return true;
                    }       
                   
                    }
                }
            }
             echo "fichier non présent dans ".$dir."!";
                    echo'<br>';
            return false;
}
function fileNameValidation($st):bool{
    $test=explode('.', $st);
    $t1= filter_var($test[0],FILTER_SANITIZE_SPECIAL_CHARS);
    $t2=$test[1];
    $_SESSION['nomFichier']=$t1;
    return checkExtension($t2);
}
function checkExtension($st):bool{
    strtolower($st);
            switch ($st){
            case 'png':
                    $_SESSION['extension']=$st;
                    return true;
               
            case 'jpeg':
                     $_SESSION['extension']=$st;
                    return true;
              
            case 'jpg':
                    $_SESSION['extension']=$st;
                    return true;
             
            case 'gif':
                     $_SESSION['extension']=$st;
                    return true;
              
            default:
                return false;
            }
}

//function FileInfo pour vérif image ...
function getImageName($data):string {
    $tmp= explode($data);
    return count($tmp-1); 
}
function resizeWidth(int $width){
    $perc=($width/$_SESSION['origWidth'])*100;
    $h=($_SESSION['origHeight']*$perc)/100;
    resize([$width,$h],$_SESSION['imgSrc']);
}
function resizeHeight(int $height){
    $perc=($height/$_SESSION['origHeight'])*100;
    $w=($_SESSION['origWidth']*$perc)/100;
    resize([$w,$height],$_SESSION['imgSrc']);
}

function getSize($path){
    list($width,$height)= getimagesize(realpath($path));
    $_SESSION['origHeight']=$height;
    $_SESSION['origWidth']=$width;
    $_SESSION['imgSrc']=$path;
}

function homothetie(string $path,float $rapport){
    if($_SESSION['resolution']=='pourcentage'){
            $vals=[($_SESSION['origWidth']*$rapport)/100,($_SESSION['origHeight']*$rapport)/100];
    }else {
    //$vals=[$_SESSION['origWidth']*$rapport,$_SESSION['origHeight']*$rapport];
//}
    }
    resize($vals,$_SESSION['imgSrc']);
}

function resize(array $hAndw,string $path){
    $original=  imagecreatefromjpeg($path);
    $destImage = imagecreatetruecolor($hAndw[0], $hAndw[1]);
    imagecopyresampled($destImage, $original,0,0,0,0,$hAndw[0], $hAndw[1],$_SESSION['origWidth'],$_SESSION['origHeight']);
    $newfile= RESIZE_DIR.'/'.$_SESSION['resolution'].'_'.$_SESSION['nomFichier'].'.'.$_SESSION['extension'];
    imagejpeg($destImage,$newfile);
    $_SESSION['resizedImage']= $newfile;
    imagedestroy($destImage);
    imagedestroy($original);
}
function resizeWitdhAndHeight(int $newHeight,int $newWidth, string $path){
    resize($ar=[$newHeight,$newWidth], $path);

}
?>
