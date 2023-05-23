<?php
session_start();
//variables de session
$extension;
$chemin;
$nomFichier;
//TODO si le nom du fichier est présent dans resized
//avec  low, miniature, high. Retourner celui qui correspond
//sinon faire la procédure actuelle de resize
function fileExists(string $dir):bool{
    $dirOriginals= scandir($dir);
        if($dirOriginals){//si il y a qqchose dans le dossier
            foreach($dirOriginals as $f){
                if(str_contains($f, $_SESSION['nomFichier'])){
                    list($width,$height)=getSize(ORIGINALS_DIR.'/'.$_SESSION['nomFichier'].'.'.$_SESSION['extension']);
                    return true; 
                }
            }
                    echo "fichier non présent dans ".$dir."!";
            return false;
        }   
    }//TODO Enlever les . et .. des  boucles
function fileIsResized(string $dir,string $type):bool{
    $dirRes= scandir($dir);
        if($dirRes){//si il y a qqchose dans le dossier
            foreach($dirRes as $f){
                if(str_contains($f, $_SESSION['nomFichier'])){
                    if(str_contains($f,$type)){
                        $path='/opt/lampp/htdocs/Projet1/Assets/resized/'.$f;
                        list($width,$height)= getimagesize($path);
                        $_SESSION['resSizeH']=$height;
                        $_SESSION['resSizeW']=$width;
                        $_SESSION['resizedImage']='/Projet1/Assets/resized/'.$f;
                       return true;
                    }
                }
            }
             echo "fichier non présent dans ".$dir."!";
                    echo'<br>';
            return false;
        }   
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

function homothetie(string $path,float $rapport,image $img){
    if($img->getResolution()=='mini'){
            $vals=[($img->getWidth()*$rapport)/100,($img->getHeight()*$rapport)/100];
    }else {
    $vals=[$img->getWidth()*$rapport,$img->getHeight()*$rapport];}
    resize($vals,$img->getSource(),$img);
}
/*TODO ici faire une fonction setFile qui remplace $newfile= etc
*/
function resize(array $hAndw,string $path){
    $original=  imagecreatefromjpeg($path);
    $destImage = imagecreatetruecolor($hAndw[0], $hAndw[1]);
    imagecopyresampled($destImage, $original,0,0,0,0,$hAndw[0], $hAndw[1],$_SESSION['origWidth'],$_SESSION['origHeight']);
    $newfile= RESIZE_DIR.'/'.$_SESSION['resolution'].'_'.$_SESSION['nomFichier'].'.'.$_SESSION['extension'];
    imagejpeg($destImage,$newfile);
    $_SESSION['newFile']= $newfile;
    imagedestroy($destImage);
    imagedestroy($original);
}
function resizeWitdhAndHeight(int $newHeight,int $newWidth, string $path){
    resize($ar=[$newHeight,$newWidth], $path);
    /*$original=  imagecreatefromjpeg($path);
    list($width,$height)= getimagesize($path);
    $destImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($destImage, $original,0,0,0,0,$newWidth, $newHeight,$img->getWidth(),$img->getHeight());
    $newfile= RESIZE_DIR.$img->getResolution().'_'.$img->getName().'.'.$img->getExtension();
    imagejpeg($destImage,$newfile);
    $_SESSION['newFile']= $newfile;
    imagedestroy($destImage);
    imagedestroy($original);*/
}
?>
