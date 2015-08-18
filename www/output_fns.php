<?php

	function do_login_page($message) {
		start_content();
		echo '<script type="text/javascript" src="sha512.js"></script>';
		echo '<script type="text/javascript" src="forms.js"></script>';
		?>
        <script type="text/javascript">
		    function validate() {
				if (document.getElementById('username').value=='' || document.getElementById('password').value=='') {
					document.getElementById('error_text').innerHTML = 'You did not fill out all of the required fields. Please try again.';
					return false;
				}
				else {
					formhash(login_form, login_form.password);
					return true;
				}
			}
		</script>
        <div id="loading_screen" style="display:none"><h2 align="center"><img src="ajax-loader.gif" style="vertical-align: middle "/>&nbsp;Loading...</h2></div>
		<div id="login_box">
        <p class="alert" id="error_text"><?php echo $message ?></p>
        <form action="login.php" method="post" name="login_form" id="login_form" onsubmit="return validate();">
        <p>Username:&nbsp;<input type="text" name="username" id="username" size="20" maxlength="20" value="<?php if (!empty($GLOBALS['username'])) echo $GLOBALS['username']; ?>" /></p>
        <p>Password:&nbsp;&nbsp;<input type="password" name="password" id="password" length="20" size="20" /></p>
		<div align="center"><input type="submit" value="Login" /></div>
		</form>
        </div>
        <div id="result"></div>
<?php
		echo "<script type=\"text/javascript\">document.forms['login_form'].elements['username'].focus();</script>";
		do_footer();
	}
	
	function do_header($title, $page='normal') {
                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><title>'.$title.'</title><link rel="stylesheet" type="text/css" href="styling.css" /><link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" /><script src="jquery-1.9.1.min.js"></script></head><body>';
		echo '<div id="container"><div id="header">';
		echo '<div style="float:left;"><a href="index.php"><img src="logo.png" /></a></div><div style="float:left;">';
		echo '<h1 class="alert">Power Manager</h1></div>';
		
		if (!empty($_SESSION['username'])) echo "<div style='float:right;'><br/><br/><br/><br/><br/><br/><br/><br/><p>Logged in: <b>".$_SESSION['username']."</b>&nbsp;&nbsp;&nbsp;<a href=\"logout.php\">Logout</a></p></div>";
		
		echo '<div style="clear:both;"></div>';
		
		
		
		echo '</div>';
	}

	function do_nav_bar() {
        if (!empty($_SESSION['username'])) {
            echo '<div id="nav"><a href="index.php">Home Page</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="config.php">Configuration</a></div>';
        }
    }

	function start_content() {
		echo '<div id="content"><br/>';
	}
	
	function do_footer() {
		echo '</div><div id="footer">';
		echo '<p>&copy 2015 Alex Strandberg</p>';
		
		echo '</div></div></body></html>';
	}
	
	function do_error_message($message) {
		echo '<h2 class="alert">'.$message.'</h2><br/><br/><a href="/">Home Page</a><br/><br/>';
		do_footer();
		exit();
	}
?>
