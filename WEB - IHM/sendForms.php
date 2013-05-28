<?php

  if(isset($_GET['sendLiveCode']))
  {
    $command = $_GET['commandArea'];
    if($command != "")
    {
      $randomkey = md5(microtime());
      shell_exec('sudo -u www-data echo "'.$command.'" > liveCode/'.$randomkey.'.sh');

    }
    else{echo 'Rien n\'a été envoyé';}
  }



?>