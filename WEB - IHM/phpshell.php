<?php 

define('PHPSHELL_VERSION', '1.0');
 

/* There are no user-configurable settings in this file anymore, please see
 * config.php instead. */

header("Content-Type: text/html; charset=utf-8");

/* This error handler will turn all notices, warnings, and errors into fatal
 * errors, unless they have been suppressed with the @-operator. */
function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    /* The @-operator (used with chdir() below) temporarely makes
     * error_reporting() return zero, and we don't want to die in that case.
     * We do note the error in the output, though. */
    if (error_reporting() == 0) {
        $_SESSION['output'] .= $errstr . "\n";
    } else {
        die('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Script-Type" content="text/javascript">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta name="generator" content="phpshell">
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
  <h1>Fatal Error!</h1>
  <p><b>' . $errstr . '</b></p>
  <p>in <b>' . $errfile . '</b>, line <b>' . $errline . '</b>.</p>
 

</body>
</html>');
    }
}

set_error_handler('error_handler');


function logout()
{
    $_SESSION = array('authenticated' => false);
}

/* Clear screen */
function clearscreen() 
{
    $_SESSION['output'] = '';
}

function stripslashes_deep($value)
{
    if (is_array($value)) {
        return array_map('stripslashes_deep', $value);
    } else {
        return stripslashes($value);
    }
}

if (get_magic_quotes_gpc()) {
    $_POST = stripslashes_deep($_POST);
}

/* Initialize some variables we need again and again. */
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$nounce   = isset($_POST['nounce'])   ? $_POST['nounce']   : '';

$command  = isset($_POST['command'])  ? $_POST['command']  : '';
$rows     = isset($_POST['rows'])     ? $_POST['rows']     : 24;
$columns  = isset($_POST['columns'])  ? $_POST['columns']  : 77;

if (!preg_match('/^[[:digit:]]+$/', $rows)) { 
    $rows=20 ; 
} 
if (!preg_match('/^[[:digit:]]+$/', $columns)) {
    $columns=20 ;
}
 
$ini = parse_ini_file('config.php', true);

if (empty($ini['settings'])) {
    $ini['settings'] = array();
}

/* Default settings --- these settings should always be set to something. */
$default_settings = array('home-directory' => '.',
                          'PS1'            => '$ ');
$showeditor = false;

/* Merge settings. */
$ini['settings'] = array_merge($default_settings, $ini['settings']);

session_start();

 
if (isset($_POST['logout'])) {
    logout();
}

/* Clear screen if submitted */
if (isset($_POST['clear'])) {
    clearscreen();
}

/* Attempt authentication. */
if (isset($_SESSION['nounce']) && $nounce == $_SESSION['nounce'] 
    && isset($ini['users'][$username])
) {
    if (strchr($ini['users'][$username], ':') === false) {
        // No seperator found, assume this is a password in clear text.
        $_SESSION['authenticated'] = ($ini['users'][$username] == $password);
    } else {
        list($fkt, $salt, $hash) = explode(':', $ini['users'][$username]);
        $_SESSION['authenticated'] = ($fkt($salt . $password) == $hash);
    }
}

 
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = false;
}

if ($_SESSION['authenticated']) {  
    /* Initialize the session variables. */
    if (empty($_SESSION['cwd'])) {
        $_SESSION['cwd'] = realpath($ini['settings']['home-directory']);
        $_SESSION['history'] = array();
        $_SESSION['output'] = '';
    }
    /* Clicked on one of the subdirectory links - ignore the command */
    if (isset($_POST['levelup'])) {
        $levelup = $_POST['levelup'] ;
        while ($levelup > 0) {
            $command = '' ; /* ignore the command */
            $_SESSION['cwd'] = dirname($_SESSION['cwd']);
            $levelup -- ;
        }
    }
    /* Selected a new subdirectory as working directory - ignore the command */
    if (isset($_POST['changedirectory'])) {
        $changedir= $_POST['changedirectory'];
        if (strlen($changedir) > 0) {
            if (@chdir($_SESSION['cwd'] . '/' . $changedir)) {
                $command = '' ; /* ignore the command */
                $_SESSION['cwd'] = realpath($_SESSION['cwd'] . '/' . $changedir);
            }
        }
    }
    if (isset($_FILES['uploadfile']['tmp_name'])) {
        if (is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {
            if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $_SESSION['cwd'] . '/' . $_FILES['uploadfile']['name'])) { 
                echo "CANNOT MOVE {$_FILES['uploadfile']['name']}" ;
            }
        }
    }

    /* Save content from 'editor' */
    if (isset($_POST["filetoedit"]) && ($_POST["filetoedit"] != "")) {
        $filetoedit_handle = fopen($_POST["filetoedit"], "w");
        fputs($filetoedit_handle, str_replace("%0D%0D%0A", "%0D%0A", $_POST["filecontent"]));
        fclose($filetoedit_handle);
    }

    if (!empty($command)) {
        /* Save the command for late use in the JavaScript. If the command is
         * already in the history, then the old entry is removed before the
         * new entry is put into the list at the front. */
        if (($i = array_search($command, $_SESSION['history'])) !== false) {
            unset($_SESSION['history'][$i]);
        }

        array_unshift($_SESSION['history'], $command);
  
        /* Now append the command to the output. */
        $_SESSION['output'] .= htmlspecialchars($ini['settings']['PS1'] . $command, ENT_COMPAT, 'UTF-8') . "\n";

        /* Initialize the current working directory. */
        if (trim($command) == 'cd') {
            $_SESSION['cwd'] = realpath($ini['settings']['home-directory']);
        } elseif (preg_match('/^[[:blank:]]*cd[[:blank:]]+([^;]+)$/', $command, $regs)) {
            /* if the directory starts and ends with quotes ("), remove them -
               allows command like 'cd "abc def"' */
            if ((substr($regs[1], 0, 1) == '"') && (substr($regs[1], -1) =='"') ) {
                $regs[1] = substr($regs[1], 1);
                $regs[1] = substr($regs[1], 0, -1);
            }

            if ($regs[1]{0} == '/') {
                /* Absolute path, we use it unchanged. */
                $new_dir = $regs[1];
            } else {
                /* Relative path, we append it to the current working directory. */
                $new_dir = $_SESSION['cwd'] . '/' . $regs[1];
            }

            /* Transform '/./' into '/' */
            while (strpos($new_dir, '/./') !== false) {
                $new_dir = str_replace('/./', '/', $new_dir);
            }

            /* Transform '//' into '/' */
            while (strpos($new_dir, '//') !== false) {
                $new_dir = str_replace('//', '/', $new_dir);
            }

            /* Transform 'x/..' into '' */
            while (preg_match('|/\.\.(?!\.)|', $new_dir)) {
                $new_dir = preg_replace('|/?[^/]+/\.\.(?!\.)|', '', $new_dir);
            }

            if ($new_dir == '') {
                $new_dir = '/';
            }

            /* Try to change directory. */
            if (@chdir($new_dir)) {
                $_SESSION['cwd'] = $new_dir;
            } else {
                $_SESSION['output'] .= "cd: could not change to: $new_dir\n";
            }

            /* history command (without parameter) - output the command history */
        } elseif (trim($command) == 'history') {
            $i = 1 ; 
            foreach ($_SESSION['history'] as $histline) {
                $_SESSION['output'] .= htmlspecialchars(sprintf("%5d  %s\n", $i, $histline), ENT_COMPAT, 'UTF-8');
                $i++;
            }
            /* history command (with parameter "-c") - clear the command history */
        } elseif (preg_match('/^[[:blank:]]*history[[:blank:]]*-c[[:blank:]]*$/', $command)) {
            $_SESSION['history'] = array() ;
            /* "clear" command - clear the screen */
        } elseif (trim($command) == 'clear') {
            clearscreen();
        } elseif (trim($command) == 'editor') {
            /* You called 'editor' without a filename so you get an short help
             * on how to use the internal 'editor' command */
               $_SESSION['output'] .= " Syntax: editor filename\n (you forgot the filename)\n";
        
        } elseif (preg_match('/^[[:blank:]]*editor[[:blank:]]+([^;]+)$/', $command, $regs)) {
            /* This is a tiny editor which you can start with 'editor filename'. */
            $filetoedit = $regs[1];
            if ($regs[1]{0} != '/') {
                /* relative path, add it to the current working directory. */
                $filetoedit = $_SESSION['cwd'].'/'.$regs[1];
            } ;
            if (is_file(realpath($filetoedit)) || ! file_exists($filetoedit)) {
                $showeditor = true;
                if (file_exists(realpath($filetoedit))) {
                    $filetoedit = realpath($filetoedit);
                }
            } else {
                $_SESSION['output'] .= " Syntax: editor filename\n (just regular or not existing files)\n";
            }

        } elseif ((trim($command) == 'exit') || (trim($command) == 'logout')) {
            logout();
        } else {

            /* The command is not an internal command, so we execute it after
             * changing the directory and save the output. */
            if (@chdir($_SESSION['cwd'])) {

                // We canot use putenv() in safe mode.
                if (!ini_get('safe_mode')) {
                    // Advice programs (ls for example) of the terminal size.
                    putenv('ROWS=' . $rows);
                    putenv('COLUMNS=' . $columns);
                }

                /* Alias expansion. */
                $length = strcspn($command, " \t");
                $token = substr($command, 0, $length);
                if (isset($ini['aliases'][$token])) {
                    $command = $ini['aliases'][$token] . substr($command, $length);
                }
                $io = array();
                $p = proc_open(
                    $command,
                    array(1 => array('pipe', 'w'),
                          2 => array('pipe', 'w')),
                    $io
                );

                /* Read output sent to stdout. */
                while (!feof($io[1])) {
                    $line=fgets($io[1]);
                    if (function_exists('mb_convert_encoding')) {
                        /* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
                        $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                    }
                    $_SESSION['output'] .= htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
                }
                /* Read output sent to stderr. */
                while (!feof($io[2])) {
                    $line=fgets($io[2]);
                    if (function_exists('mb_convert_encoding')) {
                        /* (hopefully) fixes a strange "htmlspecialchars(): Invalid multibyte sequence in argument" error */
                        $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                    }
                    $_SESSION['output'] .= htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
                }
            
                fclose($io[1]);
                fclose($io[2]);
                proc_close($p);
            } else {$_SESSION['output'] .= "PHP Shell could not change to working directory. Your command was not executed.\n";}
        }
    }

    /* Build the command history for use in the JavaScript */
    if (empty($_SESSION['history'])) {
        $js_command_hist = '""';
    } else {
        $escaped = array_map('addslashes', $_SESSION['history']);
        $js_command_hist = '"", "' . implode('", "', $escaped) . '"';
    }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Script-Type" content="text/javascript">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" href="style.css" type="text/css">

  <script type="text/javascript">
  <?php if ($_SESSION['authenticated'] && ! $showeditor) { ?>

    var current_line = 0;
    var command_hist = new Array(<?php echo $js_command_hist ?>);
    var last = 0;

    function key(e) {
        if (!e) var e = window.event;

        if (e.keyCode == 38 && current_line < command_hist.length-1) {
            command_hist[current_line] = document.shell.command.value;
            current_line++;
            document.shell.command.value = command_hist[current_line];
        }

        if (e.keyCode == 40 && current_line > 0) {
            command_hist[current_line] = document.shell.command.value;
            current_line--;
            document.shell.command.value = command_hist[current_line];
        }

    }

    function init() {
        document.shell.setAttribute("autocomplete", "off");
        document.shell.output.scrollTop = document.shell.output.scrollHeight;
        document.shell.command.focus()
    }

  <?php } elseif ($_SESSION['authenticated'] && $showeditor) { ?>

    function init() {
      document.shell.filecontent.focus();
    }

  <?php } else { ?>

    function init() {
        document.shell.username.focus();
    }

  <?php } ?>
    function levelup(d) {
        document.shell.levelup.value=d ; 
        document.shell.submit() ;
    }
    function changesubdir(d) {
        document.shell.changedirectory.value=document.shell.dirselected.value ; 
        document.shell.submit() ;
    }
  </script>
</head>

<body onload="init()">

<form name="shell" enctype="multipart/form-data" action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
<div><input name="levelup" id="levelup" type="hidden"></div>
<div><input name="changedirectory" id="changedirectory" type="hidden"></div>
<?php
if (!$_SESSION['authenticated']) {
    $_SESSION['nounce'] = mt_rand();


    if (ini_get('safe_mode') && $ini['settings']['safe-mode-warning'] == true ) {
        echo '<div class="warning">Warning: Safe-mode is enabled. PHP Shell will probably not work correctly.</div>';
    }


?>

<fieldset>
    <?php
    if (!empty($username)) {
        echo "  <p class=\"error\">Accès refusé. Recommencez :</p>\n";
    } else {
        echo "  <p>Identification :</p>\n";
    }
    ?>

  <label for="username">Identifiant:</label>
  <input name="username" id="username" type="text" value="paul"><br>
  <label for="password">Mot-de-passe:</label>
  <input name="password" id="password" type="password">
  <p><input type="submit" value="Login"></p>
  <input name="nounce" type="hidden" value="<?php echo $_SESSION['nounce']; ?>">

</fieldset>

<?php } else {?>
<fieldset>
<p>
<span class="pwd"><?php
    if ( $showeditor ) {
        echo htmlspecialchars($_SESSION['cwd'], ENT_COMPAT, 'UTF-8') . '</span>';
    } else { /* normal mode - offer navigation via hyperlinks */
        $parts = explode('/', $_SESSION['cwd']);
     
        for ($i=1; $i<count($parts); $i=$i+1) {
            echo '<a class="pwd" title="Change to this directory. Your command will not be executed." href="javascript:levelup(' . (count($parts)-$i) . ')">/</a>' ;
            echo htmlspecialchars($parts[$i], ENT_COMPAT, 'UTF-8');
        }
        echo '</span>';
        if (is_readable($_SESSION['cwd'])) { /* is the current directory readable? */
            /* Now we make a list of the directories. */
            $dir_handle = opendir($_SESSION['cwd']);
            /* We store the output so that we can sort it later: */
            $options = array();
            while ($dir = readdir($dir_handle)) {
                if (($dir != '.') and ($dir != '..') and is_dir($_SESSION['cwd'] . "/" . $dir)) {
                    $options[$dir] = "<option value=\"/$dir\">$dir</option>";
                }
            }
            closedir($dir_handle);
            if (count($options)>0) {
                ksort($options);
            }
        } else {
            
        }  
    }
?>
<br>

    <?php if (! $showeditor) { /* Outputs the 'terminal' without the editor */ ?>

<div id="terminal">
<textarea name="output" readonly="readonly" cols="<?php echo $columns ?>" rows="<?php echo $rows ?>">
<?php
        $lines = substr_count($_SESSION['output'], "\n");
        $padding = str_repeat("\n", max(0, $rows+1 - $lines));
        echo rtrim($padding . $_SESSION['output']);
?>
</textarea>
<p id="prompt">
<span id="ps1"><?php echo htmlspecialchars($ini['settings']['PS1'], ENT_COMPAT, 'UTF-8'); ?></span>
<input name="command" value="sudo -u root " type="text" onkeyup="key(event)"
       size="<?php echo $columns-strlen($ini['settings']['PS1']); ?>" tabindex="1">
</p>
</div>

    <?php } else { /* Output the 'editor' */ ?>
    <?php print("Edition: ".$filetoedit); ?>

<div id="terminal">
<textarea name="filecontent" cols="<?php echo $columns ?>" rows="<?php echo $rows ?>">
<?php
    if (file_exists($filetoedit)) {
         print(htmlspecialchars(str_replace("%0D%0D%0A", "%0D%0A", file_get_contents($filetoedit))));		 
    }
?>
</textarea>
</div>

<?php } /* End of terminal */ ?>

<p>
<?php if (! $showeditor) {?>
<br>
<input type="submit" value="/">
<input type="submit" name="clear" value="Effacer">
<?php } else {?>
<input type="hidden" name="filetoedit" id="filetoedit" value="<?php print($filetoedit) ?>">
<input type="submit" value="Save and Exit">
<input type="reset" value="Undo all Changes">
<input type="submit" value="Exit without saving" onclick="javascript:document.getElementById('filetoedit').value='';return true;">
<?php } ?>

  <input type="submit" name="logout" value="Déconnexion">
</p>
</fieldset>

<?php if ($ini['settings']['file-upload']) { ?>
<br><br>
<fieldset>
  <legend>File upload</legend>
    Select file for upload:
    <input type="file" name="uploadfile" size="40"><br>
<input type="submit" value="Upload file">
</fieldset>
    <?php } ?>

<?php } ?>

</form>

 
</body>
</html>
