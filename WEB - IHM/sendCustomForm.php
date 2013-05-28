<?php

// Récupération

 
$moteur = $_GET['motor'];
$direction = $_GET['dir'];
$vitesse = $_GET['speed'];
$pas = $_GET['pas'];
 

shell_exec('sudo -u www-data echo -n "mot '.$moteur.$direction.$pas."v".$vitesse);
echo "Envoie moteur OK [mot ".$moteur.$direction.$pas."v".$vitesse."]";
?>