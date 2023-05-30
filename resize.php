<?php
header("cache-control:no-cache");
/*variables de chemins*/
define('ASSETS_DIR', '/opt/lampp/htdocs/Projet1/Assets');
define('ORIGINALS_DIR', '/opt/lampp/htdocs/Projet1/Assets/originals');
define('RESIZE_DIR','/opt/lampp/htdocs/Projet1/Assets/resized');
define('INC_DIR','/opt/lampp/htdocs/Projet1/inc/');
define('EMPTY_IMG','/Projet1/Assets/empty.png');
require '/opt/lampp/htdocs/Projet1/inc/functions.php';

//init des variables 
$image='/Projet1/Assets/empty.png';
list($width,$height)= getImageSize(realpath('/opt/lampp/htdocs/Projet1/Assets/empty.png'));
$w= intval($width);
$h=intval($height);
$trouve=false;

//récup des infos du html
$nom=(isset($_POST['nom_image']) ? $_POST['nom_image']:'');


if(isset($_POST['submit'])){

//si le nom de fichier contient des infos normales
    if(fileNameValidation($nom)){
       
        // le fichier existe-t-il?
        $exists=fileExists(ASSETS_DIR.'/'.'originals');
            if($exists){
                if($_POST['radio']==='original'){
                    $original=true;
                    $image='/Projet1/Assets/originals'.'/'.$nom;
                    list($width,$height)= getImageSize(realpath(ORIGINALS_DIR.'/'.$nom));
                    $w= intval($width);
                    $h=intval($height);
                    session_unset();
                }
                else {$original=false;}
                //si c'est pas l'original qui est demandé
                if(!$original){
                    //enregistrement des mesures
                    $newHeight=(isset($_POST['height']) ? $_POST['height']:'');
                    $neWidth=(isset($_POST['width']) ? $_POST['width']:'');
                    
                    //est-il déjà resizé
                    $trouve=fileIsResized(RESIZE_DIR,$_POST['radio']);
                        if($trouve){
                            $image=$_SESSION['resizedImage'];
                            $w=$_SESSION['resSizeW'];
                            $h=$_SESSION['resSizeH'];
                        }
                     
                        //si la taille demandée correspond à ce que l'on a déjà
                        if($trouve && $newHeight !== $h && $neWidth !==  $w){
                            $trouve=false;
                            //n'est pas présent dans ces dimensions, on va le remplacer
                            $newHeight=(isset($_POST['height']) ? $_POST['height']:'');
                            $neWidth=(isset($_POST['width']) ? $_POST['width']:'');
                         }
                       
                
                        }
                //si l'original existe et que l'on veut une image resizée non présente
    if($exists && !$trouve && !$original){
                     //creation de la nouvelle image,préparation des valeurs
                    
                     if(empty($neWidth) && empty($newHeight)){
                          //changement d'échelle par pourcentage
                         if($_POST['radio']=='pourcentage'){
                            $rapport=(isset($_POST['pourcenNbr']) ? $_POST['pourcenNbr']:'');
                                if(!empty($rapport)){
                                    homothetie($_SESSION['imgSrc'], $rapport);
                                    $image=$_SESSION['resizedImage'];
                                    list($width,$height)= getImageSize(realpath($image));
                                    $w= intval($width);
                                    $h=intval($height);
                                }
                        }
                     }//divers cas pour l'utilisation des tailles 
                     $done=false;
                     if($_POST['pourcenNbr']=="" && !$done){
                            if(!empty($newHeight) && empty($neWidth)){
                                $_SESSION['newHeight']=$newHeight;
                                resizeHeight($newHeight);
                                $done=true;
                            }
                            if(!empty($neWidth) && empty($newHeight)  &&!$done){
                                $_SESSION['newWidth']=$neWidth;
                                resizeWidth($neWidth);
                                $done=true;
                            }
                            //mise à l'échelle sans homothétie et donc potentiellement déformée
                            if(!empty($neWidth) && !empty($newHeight) &&!$done){
                                $_SESSION['newHeight']=$newHeight;
                                $_SESSION['newWidth']=$neWidth;
                                resizeWitdhAndHeight($newHeight, $neWidth, $_SESSION['imgSrc']);
                                $done=true;
                            }
                            //affichage de l'image modifiée 
                            if(!empty($_SESSION['resizedImage'] && $done)){
                                $image=$_SESSION['resizedImage'];
                                list($width,$height)= getImageSize(realpath($image));
                                $w= intval($width);
                                $h=intval($height);
                                session_destroy();
                                header('Location:resize.php');
                            }
                    }
                 }
            }
         if(!$exists){
             die("Fichier demandé inexistant!!");
              session_destroy(); 
         }
        }
                                   session_destroy(); 
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
                    <p><label for="original">Original</label>
                        <input type="radio" name="radio" id="original" value="original" required></p>
                    <p><label for="low">Low</label>
                        <input type="radio" name="radio" id="low" value="low"></p>
                    <p><label for="pourcentage">Pourcentage</label>
                        <input type="radio" name="radio" id="pourcentage" value="pourcentage"></p>
                </p>
                <p> 
                    <label for="image">Image à redimensioner:</label>
                    
                    <div id="imagePlace"></div>

                </p>
            </fieldset>
            <fieldset>
                <label for="height">Hauteur:</label>
                <input type="number" name="height" id="height">
                <label for="width">Largeur:</label>
                <input type="number" name="width" id="width">
                <p><label for="pourcenNbr">%:</label>
                <input type="number" name="pourcenNbr" id="pourcenNbr"></p>
            </fieldset>
        </form>
            <!--variables cachées pour accéder au php       -->
         <input type="hidden" id="variable" value= <?php echo $image; ?>/>
         <input type="hidden" id="varHeight" value= <?php echo $h; ?>/>
         <input type="hidden" id="varWidth" value= <?php echo $w; ?>/>
    </body>
   
       <script>
            //creation d'une image
            var img = document.createElement("img");
            img.src= document.getElementById("variable").value;
            //recup la place de l'image
            var place = document.getElementById("imagePlace");
            //coupe la fin à cause d'un / en trop
            let int = img.src.length;
            img.src= img.src.substr(0,int-1);
            
            //idem pour les params de hauteur et largeur
            let h= document.getElementById("varHeight").value;
            let int1 = h.length;  
            h= h.substr(0,int1-1);
            img.height=h;
            let w= document.getElementById("varWidth").value;
            let int2 = w.length;
            w= w.substr(0,int2-1);
            img.width=w;
            //ajout de l'image dans le DOC
            place.appendChild(img);
       </script>
</html>

