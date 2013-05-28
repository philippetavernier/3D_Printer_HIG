<?php require('arduino.lib.php');

  if(!arduinoCheckState()){echo "NaN";exit();}

if(isset($_['key']))
{
    if($_['key'] == 'RESET')
    {
    shell_exec('sudo -u www-data echo -n "o" > /dev/ttyUSB0');
    unset($_SESSION['motor']);
    echo true;
    }
    if($_['key'] == 'XPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    $_SESSION['motor']['x']++;
    echo true; 
    }
    if($_['key'] == 'YPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    $_SESSION['motor']['y']++;
    echo true; 
    }
    if($_['key'] == 'XNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    $_SESSION['motor']['x']--;
    echo true; 
    }
    if($_['key'] == 'YNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    $_SESSION['motor']['y']--;
    echo true; 
    }
    if($_['key'] == 'XPOSITIVEYPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    $_SESSION['motor']['x']++;
    $_SESSION['motor']['y']++;
    echo true; 
    }
    if($_['key'] == 'XPOSITIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    $_SESSION['motor']['y']++;
    $_SESSION['motor']['x']--;
    echo true; 
    }
    if($_['key'] == 'XNEGATIVEYPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    $_SESSION['motor']['x']--;
    $_SESSION['motor']['y']++;
    echo true; 
    }
 

    if($_['key'] == 'XPOSITIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    $_SESSION['motor']['x']++;
    $_SESSION['motor']['Y']--;
    echo true; 
    }
    if($_['key'] == 'XNEGATIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    $_SESSION['motor']['x']--;
    $_SESSION['motor']['x']--;
    echo true; 
    }
    if($_['key'] == 'MACRO1')
    {
    shell_exec('sudo -u www-data echo -n "u" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'MACRO2')
    {
    shell_exec('sudo -u www-data echo -n "i" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'MACRO3')
    {
    shell_exec('sudo -u www-data echo -n "o" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'UP')
    {
    shell_exec('sudo -u www-data echo -n "a" > /dev/ttyUSB0');
    $_SESSION['motor']['z']++;
    echo true; 
    }
    if($_['key'] == 'DOWN')
    {
    shell_exec('sudo -u www-data echo -n "z" > /dev/ttyUSB0');
    $_SESSION['motor']['z']--;
    echo true; 
    }
    if($_['key'] == 'EMERG_STOP')
    {
    shell_exec('sudo -u www-data echo -n "s" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'SERIAL_STOP')
    {
    shell_exec('sudo -u www-data echo -n "q" > /dev/ttyUSB0');
    echo true; 
    }
     if($_['key'] == 'CHECKSTATE')
    {
if(!arduinoCheckState()){echo "DISCONNECTED";exit();}else {echo true;}
    }




}


elseif(isset($_['getState']))
     {
        if($_['getState'] == "x")
        {
            echo $_SESSION['motor']['x'];
        }
         if($_['getState'] == "y")
        {
            echo $_SESSION['motor']['y'];
        }
         if($_['getState'] == "z")
        {
            echo $_SESSION['motor']['z'];
        }
     }


 
?>