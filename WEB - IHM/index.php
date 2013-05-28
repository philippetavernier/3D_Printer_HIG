<?php require('arduino.lib.php');?>
<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta charset="UTF-8" />
        <title><?php echo $lang['SITE_TITLE'];?></title>
        <meta name="viewport" content="width=device-width, initial-scale=0.8"> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 

        <link rel="stylesheet" type="text/css" href="css/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/css/style.css" />
        <link rel="stylesheet" type="text/css" href="css/css/custom.css" />
        <link href="scripts/jtable/themes/standard/blue/jtable_blue.css" rel="stylesheet" type="text/css" />
        <link href="css/modern.css" rel="stylesheet">
        <link href="css/modern-responsive.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/mcds.css">
        <link rel="stylesheet" href="css/jquery-ui.css" />
        <link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/jquery.pageslide.css" rel="stylesheet">
        <link href="css/modern.css" rel="stylesheet">
        <link href="css/modern-responsive.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/jquery.jscrollpane.custom.css" />
        <link rel="stylesheet" type="text/css" href="css/bookblock.css" />
        <link href="scripts/jtable/themes/standard/blue/jtable_blue.css" rel="stylesheet" type="text/css" />
        <link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
   
        <script src="scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="scripts/jtable/jquery.jtable.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/notifier.js"></script>
        <script src="scripts/jtable/jquery.jtable.js" type="text/javascript"></script>
        <script src="js/modernizr.custom.79639.js"></script>
        <script type="text/javascript" src="js/modernizr.custom.79639.js"></script>
        <noscript><link rel="stylesheet" type="text/css" href="css/css/styleNoJS.css"/></noscript>


        <style type="text/css">
        html{
        height:100%;background:url(freefall.jpg) no-repeat center fixed;
        -webkit-background-size:cover;
        -moz-background-size:cover;
        -o-background-size:cover;
        background-size:cover
        }

        .cadre{
            border-radius: 10px;
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            box-shadow: 8px 0 10px #555;  
            background-color: #E0F8F1;
            height:70%;
            padding:10px;
            margin:100px;
            width: 1140px;
        }

        #langageBox{
        margin-left:10%;
        width:auto;
        height:auto;
        margin-top: 40px;
        position: absolute;

        }
    </style>
 
 <script>

 function getRadioCheckedValue(radio_name)
{
   var oRadio = document.form1.elements[radio_name];
 
   for(var i = 0; i < oRadio.length; i++)
   {
      if(oRadio[i].checked)
      {
         return oRadio[i].value;
      }
   }
 
   return '';
}


 function valider() {
  var passed = true;
  var direction=document.form1.direction.value;
  var vitesse=document.form1.speed.value;
  var Npas=document.form1.nbTour.value;
  var moteur=getRadioCheckedValue("typeMotor");

  if(vitesse == 0){passed = false; error = "La vitesse ne peut pas être nulle. / Speed can't be null";}
  if(Npas == null || Npas == ""){passed = false; error = "Indiquez le nombre de pas. / Please, indicate the number of steps";}
  if(Npas == 0){passed = false; error = "Le nombre de pas ne peut pas être nul ! / Steps number can't be null !";}

  if(passed)
  {
 
 
jQuery.ajax({
  type: 'GET', 
  url: 'sendCustomForm.php', 
  data: {
               motor: moteur,
                 dir: direction,
               speed: vitesse,
                 pas: Npas,
  }, 
  success: function(data, textStatus, jqXHR) {
    if(data != "")
    {
        alert(data);
    }
    else { alert("NO DATA (AJAX ERROR)");}

  },
  error: function(jqXHR, textStatus, errorThrown) {
  alert("(AJAX ERROR)");
  }
});

 

  }
    else 
    {
      alert(error);
      return 0;
    }
}
</script>

<?php

  if(isset($_['sendLiveCode']))
  {
    $command = $_['commandArea'];
    if($command != "")
    {
      $randomkey = md5(microtime());
      shell_exec('sudo -u root echo "'.$command.'" > liveCode/'.$randomkey.'.sh');
      $state = 'Le fichier à été sauvegardé !<br/>';

     echo '<font color="white"><pre>'.shell_exec('sudo -u root sh /var/www/liveCode/'.$randomkey.'.sh').'</pre></font>';
      exit(); 
    }
    else{$state = 'Rien n\'a été envoyé'; }
    echo "<script>Notifier.success('Fichier sauvegardé (".$randomkey.".sh)', 'LiveCode');</script>";
  }

if(isset($_['uploadFile']))
{
$dossier = 'upload/';
$fichier = basename($_FILES['fichier']['name']);
$taille_maxi = 100000;
$taille = filesize($_FILES['fichier']['tmp_name']);
$extension = strrchr($_FILES['fichier']['name'], '.'); 
 
if($extension != ".sh")  
{
     $erreur = 'Fichier SH obligatoire';
}
if($taille>$taille_maxi)
{
     $erreur = 'Le fichier est trop gros...';
}
if(!isset($erreur)) 
{ 
     $fichier = strtr($fichier, 
          'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
     $fichier = md5(microtime().preg_replace('/([^.a-z0-9]+)/i', '-', $fichier));
     if(move_uploaded_file($_FILES['fichier']['tmp_name'], $dossier . $fichier.'.sh')) 
     {
          echo"<script>Notifier.success('Upload OK', 'Envoie Fichier');</script>";
     }
     else  
     {
          echo"<script>Notifier.error('Upload ERROR', 'Envoie Fichier');</script>";
     }
}
else
{
     echo $erreur;
}
 
//TRAITEMENT DES OPTIONS

if(!$_['option1']){shell_exec('sudo -u root sh /var/www/upload/'.$fichier.'.sh'); echo"<script>Notifier.success('Exécution OK', 'Envoie Fichier');</script>";} // Si ne pas executer
if(!$_['option2']){shell_exec('sudo -u root rm -R /var/www/upload/'.$fichier.'.sh'); echo"<script>Notifier.success('Suppression OK', 'Envoie Fichier');</script>";} // Si ne pas sauvegarder
 


}

?>
    </head>
     <body class="modern-ui">
        <div class="container demo-1">
            <div id="slider" class="sl-slider-wrapper">
                <div class="sl-slider">
          
          <div class="sl-slide bg-1" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
            <div class="sl-slide-inner">
              <div id="langageBox">
                <form action="" name="" id="" method="">
                 <SELECT name="direction" onChange="location = this.options[this.selectedIndex].value;">
                   <?php
                   if($_SESSION['lang'] == "fr_FR")
                   {
                  echo" <OPTION VALUE=\"?l=fr_FR\">".$lang['LANG_FR']."</OPTION>
                        <OPTION VALUE=\"?l=en_EN\">".$lang['LANG_EN']."</OPTION>";
                   }else{
                  echo" <OPTION VALUE=\"?l=en_EN\">".$lang['LANG_EN']."</OPTION>
                        <OPTION VALUE=\"?l=fr_FR\">".$lang['LANG_FR']."</OPTION>";
                        
                   }

                   ?>
                 </SELECT>
                </form>
              </div>

                  <div class="cadre">
        <h2 style="margin:30px;"><img src="img/printer.png" style="float:left;" width="54" height="54" alt="printer"/>&nbsp;&nbsp;<?php echo $lang['INTER_CTRL'];?><span id="DivClignotante" style="visibility:hidden;" style="background:#FF0000;">&nbsp;&nbsp;<b><font color="red"><?php echo $lang['EMER_STOP'];?></font>&nbsp;&nbsp;</b></span></h2>
 
        <table style="margin-top:50px;margin-right:400px;float:right;width:auto;">
            <tr style="width:auto;">
                <td><img onClick="sendToPrinter('UP')" src="img/zplus.png" alt="zplus"/></td>
                <td>&nbsp;</td>
                <td><!-- <img onClick="sendToPrinter('MACRO1')" src="img/macro1.png" alt="Macro 1"/> --></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="width:auto;">
                <td><img src="img/updown.png" alt="updown"/></td>
                <td>&nbsp;</td>
                <td><!-- <img onClick="sendToPrinter('MACRO2')" src="img/macro2.png" alt="Macro 2"/> --></td>
                <td>&nbsp;</td>
                <td><center><img onClick="sendToPrinter('EMERG_STOP'); setInterval(clignotement, 400)" id="ArretUrgence" height="64" width="64" src="img/stop.png" alt="Arret d'urgence"/></center></td>
            </tr>
            <tr style="width:auto;">
                <td><img onClick="sendToPrinter('DOWN')" src="img/zmoins.png" alt="zmoins"/></td>
                <td>&nbsp;</td>
                <td><!-- <img onClick="sendToPrinter('MACRO3')" src="img/macro3.png" alt="Macro 3"/> --></td>
                <td>&nbsp;</td>
                <td><center><img onClick="sendToPrinter('SERIAL_STOP')" height="64" width="64" src="img/SerialStop.png" alt="Arret liaison série"/></center></td>
            </tr>
        </table>
 
        <table style="margin-top:50px;margin-right:50px;float:right;width:auto;">
            <tr style="width:auto;">
                <td><img onClick="sendToPrinter('XNEGATIVEYPOSITIVE')" height="32" width="32" src="img/ypxm.png" alt="yplusxmoins"/></td>
                <td><img onClick="sendToPrinter('YPOSITIVE')" src="img/yplus.png" alt="yplus"/></td>
                <td><img onClick="sendToPrinter('XPOSITIVEYPOSITIVE')" height="32" width="32" src="img/xpyp.png" alt="yplusxplus"/></td>
            </tr>
             <tr>
                <td><img onClick="sendToPrinter('XNEGATIVE')" src="img/xmoins.png" alt="xmoins"/></td>
                <td><center><img onClick="sendToPrinter('RESET')" src="img/reset.png" height="54" width="54" alt="reset"/></center></td>
                <td><img onClick="sendToPrinter('XPOSITIVE')" src="img/xplus.png" alt="xplus"/></td>
            </tr>   
            <tr>
                <td><img onClick="sendToPrinter('XNEGATIVEYNEGATIVE')" height="32" width="32" src="img/ymxm.png" alt="ymoinsxmoins"/></td>
                <td><img onClick="sendToPrinter('YNEGATIVE')" src="img/ymoins.png" alt="ymoins"/></td>
                <td><img onClick="sendToPrinter('XPOSITIVEYNEGATIVE')" height="32" width="32" src="img/ymxp.png" alt="ymoinsxplus"/></td>
            </tr>   

        </table>

        <table style="position:absolute;margin-top:-50px;margin-left:730px;width:auto;border: solid 1px;">
            <tr style="width:auto;">
                <td colspan="4"><h3><?php echo $lang['ACTUAL_POS']?></h3></td>
            </tr>
             <tr>
                <td><b>X : <span id="posx">00</span></b></td>
                <td><b>Y : <span id="posy">00</span></b></td>
                <td><b>Z : <span id="posz">00</span></b></td>
                <td><b>Port: </b> <input type="text" name="COMTYPE" style="width: 85px;" value="ttyUSB0"/></td>
            </tr>   
        </table>
 
          <table id="speedForm" style="position: absolute;margin-top:40px;margin-left:740px;float:right;width:auto;">
            <form name="form1" onSubmit="return false;">
            <tr style="width:auto;">
                <td><?php echo $lang['PARA_DIREC'];?></td>
                <td>
                  <SELECT name="direction">
                   <OPTION VALUE="0"><?php echo $lang['PARA_FORW'];?></OPTION>
                   <OPTION VALUE="1"><?php echo $lang['PARA_BACK'];?></OPTION>
                  </SELECT>
                </td>
            </tr>
             <tr>
                <td><?php echo $lang['PARA_SPEED'];?><input type="range" name="speedshow" min="0" max="150" value="60" step="1" onchange="showValue(this.value)" />
                  <script type="text/javascript">
                  function showValue(newValue)
                  {
                    val = document.getElementById("speed");
                    val.value = newValue;
                  }
                  </script>
                </td>
                <td><input type="text" size="1" value="60" id="speed" name="speed" /></td>
            </tr>
            <tr>
                <td><?php echo $lang['PAS_NUMBPT'];?></td>
                <td><input type="text" size="1" value="0" name="nbTour" /></td>
            </tr> 
            <tr>
                <td> <center>
                     X &nbsp;&nbsp;<input type="radio" name="typeMotor" value="x" checked>  
                     &nbsp;&nbsp;Y &nbsp;&nbsp;<input type="radio" name="typeMotor" value="y">
                     &nbsp;&nbsp;Z &nbsp;<input type="radio" name="typeMotor" value="z">
                </td></center>
            </tr>
            <tr>
                <td><center><input type="submit" onClick="valider()" name="submit" value="<?php echo $lang['BUTTON_SND'];?>"/><button id="origin"><?php echo $lang['BUTTON_ORI'];?></button></center></td>
            </tr>  
            <tr><td><h5><?php echo $lang['PARA_DONES'];?> : 1600 micropas = 1 <?php echo $lang['TOUR_COMP'];?><br/> <?php echo $lang['MAX_VALUE'];?> : <b>65.535</b></h5></td></tr>
        </form>
        </table>

        <br/><br/>
        <br/><br/>
       </div>

         </div>
          </div>
          <div class="sl-slide bg-2" data-orientation="vertical" data-slice1-rotation="10" data-slice2-rotation="-15" data-slice1-scale="1.5" data-slice2-scale="1.5">
            <div class="sl-slide-inner">
       
            <div class="cadre">
        <h2 style="margin:30px;"><img src="img/printer.png" style="float:left;" width="54" height="54" alt="printer"/>&nbsp;&nbsp;<?php echo $lang['CALIB_IMP'];?></h2>
 
        <br/><br/>
        <br/><br/>
       </div>

            </div>
          </div>
          
          <div class="sl-slide bg-3" data-orientation="horizontal" data-slice1-rotation="3" data-slice2-rotation="3" data-slice1-scale="2" data-slice2-scale="1">
            <div class="sl-slide-inner">
             
  <div class="cadre">
        <h2 style="margin:30px;"><img src="img/printer.png" style="float:left;" width="54" height="54" alt="printer"/>&nbsp;&nbsp;<?php echo $lang['IA_INITIAL'];?></h2>
 
        <center><img src="img/yuri.gif" alt="Yuri"/></center>
   
<div id="bulle"></div>
<?php 
$macroPath = 'home/paul/ymf/';
if(arduinoCheckState()){$ArduinoCard="<font color=green>".$lang['IA_LINKED']." [OK]</font>";}else{$ArduinoCard="<font color=red>".$lang['IA_NLINKED']."</font>";}
echo'
<script language="JavaScript">
var i=0
var j=0
var texteNE, affiche
var texte="<font size=3>Initialisation</font><br> Interfacage ----------------------------------------------> [OK] <br> Vérification Arduino ------------------> '.$ArduinoCard.' <br> '.$lang['IA_SERIAL'].' ----------------------------------------------> [OK] <br> '.$lang['IA_WIFICHCK'].' ---------------------------> '.exec('sudo -u root iwconfig wlan0 | grep Mode').' <br> Vérification journaux ------------------------------------> [OK] <br> '.$lang['IA_MCROFILE'].' ('.$macroPath.') ------------------> [OK] <br> '.$lang['IA_WAITFOR'].' ----------------------------------->                                                                                  [OK]<br>Création clef : '.substr(sha1('YURI_01D5513DY').rand(1, 255) , 1).'<br> Arguments: --ack --configure --disableAutoRemove --br 9600<br><br><center>IA lancée niveau serveur [I_M_READY].</center>"
var ie = (document.all);
var ne = (document.layers); 
function init(){
texteNE=\'\';
machine_a_ecrire();
}
function machine_a_ecrire(){
texteNE=texteNE+texte.charAt(i)
affiche=\'<table><tr><td>[Console] <b>'.$lang['IA__SAYS'].'</b></td><td><font size=2 color=black><strong><b>\'+texteNE+\'</b></strong></font></td></tr></table>\'
if (texte.charAt(i)=="<") {
j=1
}
if (texte.charAt(i)==">") {
j=0
}
if (j==0) {
if (document.getElementById) { // avec internet explorer
document.getElementById("bulle").innerHTML = affiche;
}
}
if (i<texte.length-1){
i++
setTimeout("machine_a_ecrire()",20)
}
else{

  Notifier.info(\'CYPHER accepté (GRANTED).\', \'Interface Vocale\', 3000, 30000);
Notifier.success(\'Initialisation terminée !\', \'Interface Vocale\', 3000, 30000)
return
}

}

</script>
';?>
        <br/><br/>
        <br/><br/>
               </div>
             </div>
          </div>
          
          <div class="sl-slide bg-4" data-orientation="vertical" data-slice1-rotation="-5" data-slice2-rotation="20" data-slice1-scale="1" data-slice2-scale="1">
            <div class="sl-slide-inner">
           
            <div class="cadre">
               <h2 style="margin:30px;"><img src="img/printer.png" style="float:left;" width="54" height="54" alt="printer"/>&nbsp;&nbsp;<?php echo $lang['INT_SAISIE'];?></h2>
  
        <div id="section1" style="position: absolute;width: 23%; float:left; padding-left:100px;">
         <h2>LiveCode</h2>
            <form type="form" method="POST" action="?sendLiveCode">
              <textarea name="commandArea" style="color: yellow; font-size:70%; background-color: black" rows="23" cols="60">#!/bin/bash
#CIPHER-> AsSW50ZWdlciBwdWx2aW5hcZnNmcw==
beginSerial;
echo [Liaison serie ON]
#
## Code ici !
#
endSerial;
echo [Liaison serie OFF]
</textarea>
          <br/>
          <center><input type="submit" value="<?php echo $lang['BUTTON_SND'];?>"/></center>
  
            </form>               
        </div>

        <div id="section2" style="width: 50%; float:right;">
          <h2><?php echo $lang['SEND_FILES'];?></h2>
              <form action="?uploadFile" method="post" enctype="multipart/form-data">
              <input style="z-index: 25;" type="file" name="fichier" id="file"><br/>
              <input type="hidden" name="MAX_FILE_SIZE" value="10000">
              <h4>Options :</h4>
              <input type="checkbox" name="option1" value="dontExecute"> <?php echo $lang['DONT_EXECU'];?><br/>
              <input type="checkbox" name="option2" value="save" checked> <?php echo $lang['PARA_SAVE'];?><br/>
               <br/><br/>
              <center><input type="submit" name="submit" value="<?php echo $lang['BUTTON_SND'];?>"></center>
              </form>
        </div>
                         <br/><br/>
                     <br/><br/>
                  </div>
              </div>
          </div>
       </div><br/>
        
        <nav id="nav-dots" class="nav-dots">
          <button style="background-color: #72B4FF;"><?php echo $lang['INTER_CTRL'];?></button>
          <button style="background-color: #088A08;"><?php echo $lang['CALIB_IMP'];?></button>
          <button style="background-color: #088A4B;" onClick="initYuri()"><?php echo $lang['IA_INITIAL'];?></button>
          <button style="background-color: #2E64FE;"><?php echo $lang['SEND_COMND'];?></button>
          <button onClick="javascript:window.location='phpshell.php'" style="background-color: #08D5CE;"><?php echo $lang['PHP__SHELL'];?></button>
        </nav>

          </div> 
        </div>
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.ba-cond.min.js"></script>
    <script type="text/javascript" src="js/jquery.slitslider.js"></script>
    <script>
var alreadyviewOFF, alreadyviewON, initializeYuri;

Notifier.info('<?php echo $lang["NOTIF_LOAD"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (INITIALIZE)');      


var clignotement = function(){ 
   if (document.getElementById('DivClignotante').style.visibility=='visible'){ 
      document.getElementById('DivClignotante').style.visibility='hidden'; 
   } 
   else{ 
   document.getElementById('DivClignotante').style.visibility='visible'; 
   } 
}; 

 
function getCommands()
{
 
alert("Commandes :\n\n#Commandes liaison\nbeginSerial : Initialise la connexion série\ncloseSerial : Met fin à la connexion série\n\n#Commandes moteur\n\n");

}

function initYuri()
{
 if(initializeYuri != 5)
 {
  initializeYuri = 5;
  init();
 }
}

function arduinoStateChecker(){

jQuery.ajax({
  type: 'GET', 
  url: 'motors.php', 
  data: {
    key: "CHECKSTATE",
  }, 
  success: function(data, textStatus, jqXHR) {
    if(data != "")
    {
        if(data == "NaN")
        {
           alreadyviewON = 0;
          if(alreadyviewOFF == 0)
          {
             Notifier.error('<?php echo $lang["ARDUI_NDETEC"]; ?>', '<?php echo $lang["NOTIF_ARDTI"]; ?> (DISCONECTED)');
              alreadyviewOFF = 1;
          }
        }
        else
        {       
            alreadyviewOFF = 0;
              if(alreadyviewON == 0)
          {
             Notifier.success('<?php echo $lang["IA_LINKED"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (DETECTED)');
             Notifier.success('<?php echo $lang["NOTIF_LINKED"];?>', '<?php echo $lang["NOTIF_ARDTI"];?>');
              alreadyviewON = 1;
          }
        }
    }
    else {Notifier.error('Erreur, carte OK mais pas de données', '<?php echo $lang["NOTIF_ARDTI"];?>');}

  },
  error: function(jqXHR, textStatus, errorThrown) {
  Notifier.error("Erreur de connexion", "Ajax");
  }
});

setTimeout(arduinoStateChecker,1500);
}

jQuery.ajax({
  type: 'GET', 
  url: 'motors.php', 
  data: {
    key: "CHECKSTATE",
  }, 
  success: function(data, textStatus, jqXHR) {
    if(data != "")
    {
        if(data == "NaN")
        {
                setTimeout(function() {
                 Notifier.error('<?php echo $lang["ARDUI_NDETEC"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (DISCONECTED)');
                }, 1600);
        }
        else
        {
                setTimeout(function() {
                Notifier.success('<?php echo $lang["ARDUI_DETEC"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (DETECTED)');
                Notifier.success('<?php echo $lang["NOTIF_LINKED"];?> ', '<?php echo $lang["NOTIF_ARDTI"];?> (SERIAL_ON)');
                }, 1600);
        }
    }
    else {Notifier.error('Erreur, carte OK mais pas de données', '<?php echo $lang["NOTIF_ARDTI"];?> (FATAL_ERR)');}

  },
  error: function(jqXHR, textStatus, errorThrown) {
  Notifier.error('Erreur Ajax', 'Ajax');
  }
});


function sendToPrinter(str) // Seulement pour des ordres simple préconfigurés
{
jQuery.ajax({
  type: 'GET', 
  url: 'motors.php', 
  data: {
    key: str,
  }, 
  success: function(data, textStatus, jqXHR) {
    if(data != "")
    {
        if(data == "NaN")
        {
            Notifier.error('<?php echo $lang["NOTIF_ARDND"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (DISCONECTED)');
        }
        else
        {
            Notifier.success('<?php echo $lang["NOTIF_EXECOR"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> ('+str+')');

            //Init
             x = document.getElementById("posx").innerHTML;
             y = document.getElementById("posy").innerHTML;
             z = document.getElementById("posz").innerHTML;

      }
    }
    else {Notifier.error('<?php echo $lang["NOTIF_EXECOR"];?>', '<?php echo $lang["NOTIF_ARDTI"];?> (ERROR)');}

  },
  error: function(jqXHR, textStatus, errorThrown) {
  Notifier.error('Erreur Ajax', 'Ajax');
  }
});
}
 
 $(document).ready(function(){
  $("togglebutton").click(function(){
    $("#speedForm").toggle();
  });
}); 

      $(function() {
      
        var Page = (function() {

          var $navArrows = $( '#nav-arrows' ),
            $nav = $( '#nav-dots > button' ),
            slitslider = $( '#slider' ).slitslider( {
              onBeforeChange : function( slide, pos ) {

                $nav.removeClass( 'nav-dot-current' );
                $nav.eq( pos ).addClass( 'nav-dot-current' );
              }
            } ),

            init = function() {

              initEvents();
              
            },
            initEvents = function() {

              // add navigation events
              $navArrows.children( ':last' ).on( 'click', function() {

                slitslider.next();
                return false;

              } );

              $navArrows.children( ':first' ).on( 'click', function() {
                
                slitslider.previous();
                return false;

              } );

              $nav.each( function( i ) {
              
                $( this ).on( 'click', function( event ) {
                  
                  var $dot = $( this );
                  
                  if( !slitslider.isActive() ) {

                    $nav.removeClass( 'nav-dot-current' );
                    $dot.addClass( 'nav-dot-current' );
                  
                  }
                  
                  slitslider.jump( i + 1 );
                  return false;
                
                } );
                
              } );

            };

            return { init : init };

        })();
        Page.init();
      });

  function AjaxInfoMotorX()
      {
        var
          $http,
          $self = arguments.callee;

        if (window.XMLHttpRequest) {
          $http = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
          try {
            $http = new ActiveXObject('Msxml2.XMLHTTP');
          } catch(e) {
            $http = new ActiveXObject('Microsoft.XMLHTTP');
          }
        }

        if ($http) {
          $http.onreadystatechange = function()
          {
            if (/4|^complete$/.test($http.readyState)) {
              document.getElementById('posx').innerHTML = $http.responseText;
              setTimeout(function(){$self();}, 1000);
            }
          };
          $http.open('GET', 'motors.php' + '?getState=x', true);
          $http.send(null);

       }

      }

        function AjaxInfoMotorY()
      {
        var
          $http,
          $self = arguments.callee;

        if (window.XMLHttpRequest) {
          $http = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
          try {
            $http = new ActiveXObject('Msxml2.XMLHTTP');
          } catch(e) {
            $http = new ActiveXObject('Microsoft.XMLHTTP');
          }
        }

        if ($http) {
          $http.onreadystatechange = function()
          {
            if (/4|^complete$/.test($http.readyState)) {
              document.getElementById('posy').innerHTML = $http.responseText;
              setTimeout(function(){$self();}, 1000);
            }
          };
          $http.open('GET', 'motors.php' + '?getState=y', true);
          $http.send(null);

       }

      }
        function AjaxInfoMotorZ()
      {
        var
          $http,
          $self = arguments.callee;

        if (window.XMLHttpRequest) {
          $http = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
          try {
            $http = new ActiveXObject('Msxml2.XMLHTTP');
          } catch(e) {
            $http = new ActiveXObject('Microsoft.XMLHTTP');
          }
        }

        if ($http) {
          $http.onreadystatechange = function()
          {
            if (/4|^complete$/.test($http.readyState)) {
              document.getElementById('posz').innerHTML = $http.responseText;
              setTimeout(function(){$self();}, 1000);
            }
          };
          $http.open('GET', 'motors.php' + '?getState=z', true);
          $http.send(null);

       }

      }


// Position moteur

setTimeout(function() {AjaxInfoMotorX();}, 200);
setTimeout(function() {AjaxInfoMotorY();}, 200);
setTimeout(function() {AjaxInfoMotorZ();}, 200);

        </script>
        <script src="js/jquery.mousewheel.js"></script>
        <script src="js/jquery.jscrollpane.min.js"></script>
        <script src="js/jquerypp.custom.js"></script>
        <script src="js/jquery.bookblock.js"></script>
        <script> arduinoStateChecker(); </script>
  </body>
</html>

<!--     <div class="page secondary">
        <div class="page-header">
            <div class="page-header-content">
                <h1 style="color: #FFFFFF;">Imprimante 3D<small>Projet BAC</small></h1>
                <a href="/" class="back-button big page-back white"></a>
            </div>
             <div id="profileMenu">
                /
            <div>
         <div class="divider"></div>
    </div>
</div>
</div>

   </div>
  -->

  