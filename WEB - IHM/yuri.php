<?php require('arduino.lib.php');

if(!arduinoCheckState())
{
    echo "Commande refusée, la carte arduino n'est pas connectée.";
    exit();
}

header('Content-type: application/json; charset=UTF-8');
 date_default_timezone_set('Europe/Paris');

  if(date('H') > 6 && date('H') < 17){$state = 'Bonjour';}
    else{$state = 'Bonsoir';}

$_ = array();
foreach($_GET as $key=>$value){
    $_[addslashes(htmlentities($key))] = addslashes(htmlentities($value));
}
foreach($_POST as $key=>$value){
    $_[addslashes(htmlentities($key))] = addslashes(htmlentities($value));
}

$pin_engine = array(
    'LED'=>0, // Relation logique : un 0 logique correspond à la pin 11 !
    'LAMPE_CHAMBRE'=>13,
    'LAMPE_SALLE_DE_BAIN'=>8,
    );

$response = 'Aucune action n\'a été spécifiée';    

switch ($_['action']){

    //action permettant l'activation/la désactivation d'un PIN GPIO (par exemple rélié a une carte relais elle même reliée a une lampe)
    // $_['engine'] = position de la lampe visée(ex : LAMPE_SALON)
    // $_['state'] = etat ciblé (0 désactivé, 1 activé)
    case 'CHANGE_ENGINE_STATE':
        system('gpio mode '.$pin_engine[$_['engine']].' out');
        system('gpio write '.$pin_engine[$_['engine']].' '.$_['state']);
        $response = 'Action effectuée sur '.$_['engine'];
    break;

     case 'PRINTER_MACRO':
 
        $response = 'Ok. Je lance l\'impression de l\'objet '. $_['type'];
    break;

    case 'HELLO':
    if(date('H') > 6 && date('H') < 17){$state = 'Bonjour';}
    else{$state = 'Bonsoir';}
        $response = $state.', Paul comment tallez vous ?';
    break;
     case 'REBOOT':
        system('sudo -u root reboot');  
        $response = 'La carte redémarerra dans 4 secondes. A bientot.';
    break;

    case 'STATUT':
        $response = 'Vous savez, je ne suis qu\'un morçeau de code. Mais sinon sava. Merci de vous en soucier.';
    break;

    //action de récuperation de l'heure courante
    case 'GET_TIME':
        $response = 'Il est '.date('H').' heures '.date('i');
    break;
 
    //action de récuperation de la phrase d'identification de YURI
    case 'GET_IDENTIFICATION':
        $response = 'Je suis Youri, chargée de relation Homme-Machine, je transmet vos ordres à travers la raspe berrie (ou je réside) vers a la carte arduino qui se charge du reste';
    break;
    
    //action de récuperation de la date courante
    case 'GET_DATE':
        $month = array(
            'number'=>array('01','02','03','04','05','06','07','08','09','10','11','12'),
            'name'=>array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre')
        );
        $response = 'Nous sommes le '.date('d').' '.str_replace($month['number'],$month['name'],date('m')).' '.date('Y');
    break;

     case 'INITIALIZE_PRINTER':
     if($_['init'] == 'initialize')
     {
         $response = 'J\'ai activé la liaison série, tout est au vert, Vous pouvez commencer !';
     }
     elseif($_['init'] == "restart")
     {
        shell_exec('sudo -u root stty --file=/dev/ttyUSB0 hupcl');
        shell_exec('sudo -u root stty --file=/dev/ttyUSB0 -hupcl');
        $response = 'Ok ! L\'interface devrait redémarer maintenant';
     }
       
    break;

     case 'MOTORS':
     if($_['MOTOR'] == "S") // moteur stop
     {
        if($_['state'] == 0 || $_['state'] == 1 || $_['state'] == 2)
        {
         shell_exec('sudo -u www-data echo -n "s" > /dev/ttyUSB0');
         $response = "Commande moteur arret d'urgence initialisée.";
        }

         if($_['state'] == 4)
        {
         shell_exec('sudo -u www-data echo -n "s" > /dev/ttyUSB0');
         $response = "Commande moteur aarai total envoyée";
        }
     }
       if($_['MOTOR'] == "X")  
     {
        if($_['state'] == 0)
        {
         shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
         $response = "Commande moteur X arriere envoyée";
        }
        elseif($_['state'] == 1)
        {
         shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
         $response = "Commande moteur X avant envoyée";
        }
     }

            if($_['MOTOR'] == "Y")  
     {
        if($_['state'] == 0)
        {
         shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
         $response = "Commande moteur Y arriere envoyée";
        }
        elseif($_['state'] == 1)
        {
         shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
         $response = "Commande moteur Y avant envoyée";
        }
     }

            if($_['MOTOR'] == "Z")  
     {
        if($_['state'] == 0)
        {
         shell_exec('sudo -u www-data echo -n "z" > /dev/ttyUSB0');
         $response = "Commande moteur Z arriere envoyée";
        }
        elseif($_['state'] == 1)
        {
         shell_exec('sudo -u www-data echo -n "a" > /dev/ttyUSB0');
         $response = "Commande moteur Z avant envoyée";
        }
     }

            if($_['MOTOR'] == "o")  
     {
        if($_['state'] == 0 || $_['state'] == 1 || $_['state'] == 2 || $_['state'] == 3 || $_['state'] == 4 || $_['state'] == 5)
        {
         shell_exec('sudo -u www-data echo -n "o" > /dev/ttyUSB0');
         $response = "OK ! Je remet tous les moteurs à leur point d'origine.";
        }
     }


    break;

}


echo $response;
?>