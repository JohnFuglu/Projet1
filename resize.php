<?php
/*variables de chemins*/
define('ASSETS_DIR', '/opt/lampp/htdocs/Projet1/assets');
define('ORIGINALS_DIR', '/opt/lampp/htdocs/Projet1/Assets/originals');
define('RESIZE_DIR','/opt/lampp/htdocs/Projet1/Assets/resized');
define('INC_DIR','/opt/lampp/htdocs/Projet1/inc/');
define('EMPTY_IMG','/Projet1/Assets/empty.png');
require '/opt/lampp/htdocs/Projet1/inc/functions.php';
//init des variablles 
$image='/Projet1/Assets/empty.png';
$trouve=false;
//récup des infos du html
$nom=(isset($_POST['nom_image']) ? $_POST['nom_image']:'');


if(isset($_POST['submit'])){
//si le nom de fichier contient des infos normales

    if(fileNameValidation($nom)){
        // le fichier existe-t-il?
        $trouve=fileExists(ORIGINALS_DIR);
            if($trouve){
                if(fileIsResized(RESIZE_DIR,$_POST['radio'])){
                     //si la taille demandée correspond à ce que l'on a déjà
                     $newHeight=(isset($_POST['height']) ? $_POST['height']:'');
                     $neWidth=(isset($_POST['width']) ? $_POST['width']:'');
                    
                     if($newHeight ==  $_SESSION['resSizeH'] && $neWidth ==  $_SESSION['resSizeW']){
                        $image=$_SESSION['resizedImage'];
                     }
                }
                else{
                     //creation de la nouvelle image,préparation des valeurs
                     $_SESSION['resolution']=$_POST['radio'];
                     if(!empty($newHeight) && empty($neWidth)){
                        $_SESSION['newHeight']=$newHeight;
                        resizeHeight($newHeight);
                     }
                     if(!empty($neWidth) && empty($newHeight)){
                        $_SESSION['newWidth']=$newWidth;
                        resizeWidth($newidth);
                     }
                     if(!empty($neWidth) && !empty($newHeight)){
                         $_SESSION['newHeight']=$newHeight;
                         $_SESSION['newWidth']=$newWidth;
                         resizeWitdhAndHeight($newHeight, $newWidth, $path);
                     }
                     if(!empty($_SESSION['resizedImage']))
                        $image=$_SESSION['resizedImage'];
                }
            }
            session_destroy(); 
        }
    }
?>

<!doctype html>
<html lang="fr">
    <head>
        <title>Service de redimension</title>
        <meta charset="utf-8">
        <link rel='stylesheet' href="inc/styleSheet.css">
    </head>
    <body>
        <h1>Service de redimension d'images</h1>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <fieldset>
                <p>
                    <label for="nom_image">Chercher une image</label>
                    <input type="text" name="nom_image"id="nom_image" required="Entrez un nom d'image">
                    <input type="submit" name="submit" value="chercher">
                    <p><label for="low">Low</label><input type="radio" name="radio" id="low" value="Low" required></p>
                    <p><label for="high">High</label><input type="radio" name="radio" id="high" value="high"></p>
                    <p><label for="mini">Mini</label><input type="radio" name="radio" id="mini" value="mini"></p>
                </p>
                <p> 
                    <label for="image">Image à redimensioner:</label>
                    <img src="<?=$image?>" alt="image chargée" id="image">
                </p>
            </fieldset>
            <fieldset>
                <label for="height">Hauteur:</label>
                <input type="number" name="height" id="height">
                <label for="width">Largeur:</label>
                <input type="number" name="width" id="width">
            </fieldset>
        </form>
    </body>
</html>

