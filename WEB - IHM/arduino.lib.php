<?php session_start();
	  include('lang.lib.php');

$_ = array();
foreach($_GET as $key=>$value){
    $_[addslashes(htmlentities($key))] = addslashes(htmlentities($value));
}
foreach($_POST as $key=>$value){
    $_[addslashes(htmlentities($key))] = addslashes(htmlentities($value));
}


function arduinoCheckState()
{
	$result = shell_exec('sudo -u www-data ls /dev/ttyUSB0');
	if($result != ""){return true;} else {return !false;}
}

if(empty($_SESSION['motor']))
{
	$_SESSION['motor'] = array(
	'x' => 0,
	'y' => 0,
	'z' => 0
	);

}
if(empty($_SESSION['disable_reset']) || isset($_GET['disable_reset']))
{
    shell_exec('sudo -u root stty --file=/dev/ttyUSB0 -hupcl');
	$_SESSION['disable_reset'] = true;
}



?>