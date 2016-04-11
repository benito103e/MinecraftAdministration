<?php
/**
 * Gestion du serveur minecraft
 * Titre : minecraft
 *
 * @desc Page principale
 * @author benito103e : https://github.com/benito103e/
 *
 * 
 * @log :
 * - 13/11/2015 BD (+) Création
 * - 18/11/2015 BD (m) Mode boostraps
 */


//Variable utile à modifier
$ssh_user = 'miecraft';
$server = '127.0.01';
$port = '22';
$title = 'Outils.Minecraft';


//ACTIONS sur le serveur
$retour = '';
if (isset($_GET['action']))
{
    $action = $_GET['action'];
    // on vérifie les commandes
    if (in_array($action,['start','stop','backup','command'])) {
        //Envoi d'un message
        if($action=='command' && $_POST["message"]){
            $action .= ' "/say '. str_replace(["'",'"'],["\`","\`\`"],$_POST["message"]) . '"';
        }
        //Téléportation
        if($action=='command' && $_POST["tp_from"] && $_POST["tp_to"]){
            $action .= ' "/tp '. $_POST["tp_from"] . ' '. $_POST["tp_to"] .'"';
        }
        //Météo
        if($action=='command' && $_POST["meteo"]){
            $action .= ' "/weather '. $_POST["meteo"] .'"';
        }
        //Horaire
        if($action=='command' && $_POST["horaire"]){
            $action .= ' "/time set '. $_POST["horaire"] .'"';
        }
        // on effectue la commande
        $retour = exec("ssh ".$ssh_user."@".$server." -p ".$port." '/etc/init.d/minecraft ".$action."'");
        $retour .= "\n";
    }
}

//Statut du serveur
$status = exec("ssh ".$ssh_user."@".$server." -p ".$port." '/etc/init.d/minecraft status'");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="<?=$title;?>">
    <meta name="author" content="benito103e">
    <link rel="icon" href="../img/favicon_minecraft.png">

    <title><?=$title;?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
</head>

<body>
<div class="container">

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <img class="navbar-brand" alt="logo" src="img/favicon_minecraft.png" />
                <a class="navbar-brand" href="#"><?=$title;?></a>
            </div>
        </div>
    </nav>

    <div class="jumbotron" style="padding:20px;margin:0px;">
        <!-- Statut du serveur -->
        <div class="text-center">
            <h3 style="margin-top:0px;"><?=($status == "minecraft_server.jar is running."?'Serveur : <span style="color:green">en ligne</span>':'Serveur : <span style="color:red">hors ligne</span>');?></h3>
            <div class="btn-group" role="group" aria-label="...">
                <button type='button' onclick="window.location.href='<?=$_SERVER['PHP_SELF'];?>?action=start';" class="btn btn-primary">
                    <span class="glyphicon glyphicon-play" aria-hidden="true"></span> Démarrer</button>
                <button type='button' onclick="window.location.href='<?=$_SERVER['PHP_SELF'];?>?action=stop';" class="btn btn-danger">
                    <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> Arrêter</button>
                <button type='button' onclick="window.location.href='<?=$_SERVER['PHP_SELF'];?>?action=backup';" class="btn btn-success">
                    <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Sauvegarder</button>
            </div><br /><br />
        </div>
        
        <!-- Console -->
        <div class="panel panel-primary">
            <div class="panel-heading">Console</div>
            <textarea id="sai_historique" readonly="readonly" style="text-align:left;background-color:black;color:green;width:100%;height:140px;border-color:grey;"><?=$retour.$status;?></textarea>
        </div>

        <!-- Envoi de message -->
        <form action="<?=$_SERVER['PHP_SELF'];?>?action=command" method="post">
            <div class="input-group">
                <label for="sai_message" class="input-group-addon" id="basic-addon1">Message système :</label>
                <input type="text" class="form-control" id="sai_message" name="message" placeholder="Votre message" />
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-primary" name="Valider" value="Valider" />
                </span>
            </div>
        </form><br />


        <!-- Téléportation -->
        <form action="<?=$_SERVER['PHP_SELF'];?>?action=command" method="post" style="display:inline;">
            <div class="input-group">
                <label for="tp_from" class="input-group-addon" id="basic-addon1">Téléporter </label>
                <input type="text" class="form-control" id="tp_from" name="tp_from" placeholder="pseudo" />
                <label for="tp_to" class="input-group-addon" id="basic-addon1">Vers </label>
                 <input type="text" class="form-control" id="tp_to" name="tp_to" placeholder="pseudo" />
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-primary" name="Valider" value="Valider" />
                </span>
            </div>
        </form><br />

        <!-- Météo -->
        <form action="<?=$_SERVER['PHP_SELF'];?>?action=command" method="post" class="col-lg-6" style="display:inline;">
            <strong>Météo :</strong>
            <div class="btn-group" role="group">
                <button type="submit" name="meteo" value="clear" class="btn btn-success"><span class="glyphicon glyphicon-sunglasses" aria-hidden="true"></span> Dégagé</button>
                <button type="submit" name="meteo" value="rain" class="btn btn-warning"><span class="glyphicon glyphicon-tent" aria-hidden="true"></span> Pluie</button>
                <button type="submit" name="meteo" value="thunder" class="btn btn-danger"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Tonnerre</button>
            </div>
        </form>
        <!-- Heure -->
        <form action="<?=$_SERVER['PHP_SELF'];?>?action=command" method="post" class="col-lg-6" style="display:inline;">
            <strong>Heure :</strong>
            <div class="btn-group" role="group">
                <button type="submit" name="horaire" value="day" class="btn btn-default"> Jour </button>
                <button type="submit" name="horaire" value="night" class="btn btn-primary" style="background-color:grey;"> Nuit </button>
            </div>
        </form><br /><br /><br />

        <!-- Logs du serveur -->
        <?php
            $last_log = '/home/minecraft/serveur/logs/latest.log'; 
            $lignes = file($last_log);
            for($i=count($lignes);$i>0;$i--){
                if(substr( $lignes[$i-1],11,36)<>"[Server thread/WARN]: Can't keep up!" || $_GET["details_log"]){
                    $contenu .= $lignes[$i-1];
                }
            }
            if($contenu){
                echo '
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Logs à <u>'.date("H:i:s").'</u></strong>&nbsp;&nbsp;&nbsp;<a class="btn btn-success btn-xs" href="'.$_SERVER['PHP_SELF'].'">
                        <span class="glyphicon glyphicon-refresh"></span></a>
                        &nbsp;-&nbsp;
                        Détaillés : <input type="checkbox" '.($_GET["details_log"]?'checked="checked" onclick="location.href=\''.$_SERVER['PHP_SELF'].'\'"':'onclick="location.href=\''.$_SERVER['PHP_SELF'].'?details_log=1\'"').' />
                    </div>
                    <textarea id="sai_historique" readonly="readonly" style="text-align:left;background-color:black;color:green;width:100%;height:210px;">'.$contenu.'</textarea>
                </div>';
            }
        ?>
    </div>
    <nav class="navbar navbar-inverse" style="margin-bottom:0px;">
        <div class="container-fluid">
            <p class="navbar-text">No copyright</p>
        </div>
    </nav>
</div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</body>
</html>
