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
                if(fileIsResized(RESIZE_DIR,"low")){//TODO faire avec form et post
                    $image=$_SESSION['resizedImage'];
                }
            }
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

