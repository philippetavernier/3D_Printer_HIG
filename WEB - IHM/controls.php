<?php require('arduino.lib.php');

  if(!arduinoCheckState()){echo "DISCONNECTED";exit();}

if(isset($_['key']))
{
    if($_['key'] == 'RESET')
    {
    echo true;
    }
    if($_['key'] == 'XPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'YPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'YNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XPOSITIVEYPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XPOSITIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XNEGATIVEYPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XPOSITIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "w" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XNEGATIVEYPOSITIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "t" > /dev/ttyUSB0');
    echo true; 
    }
    if($_['key'] == 'XNEGATIVEYNEGATIVE')
    {
    shell_exec('sudo -u www-data echo -n "x" > /dev/ttyUSB0');
    shell_exec('sudo -u www-data echo -n "y" > /dev/ttyUSB0');
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
    echo true; 
    }
    if($_['key'] == 'DOWN')
    {
    shell_exec('sudo -u www-data echo -n "z" > /dev/ttyUSB0');
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



}
 
?>
