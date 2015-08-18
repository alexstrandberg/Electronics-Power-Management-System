<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();

    // Start a secure session
    sec_session_start();

    $redirect_url = '/';

    if(isset($_POST['username'], $_POST['p'])) { // Try to log in user if all necessary POST data was sent
        $username = $_POST['username'];
        $password = $_POST['p']; // The hashed password.

        if(login($username, $password, $db) == true) {
            // Login success
            do_header('Logged In');
            start_content();
            echo '<h2 align="center"><img src="ajax-loader.gif" style="vertical-align: middle "/>&nbsp;Loading...</h2>';
            echo '<meta http-equiv="refresh" content="0;URL='.$redirect_url.'">';
            echo '<p align="center"><a href="'.$redirect_url.'">Click here if you are not redirected within 5 seconds.</a></p>';

            do_footer();
        } else {
            // Login failed
            do_header('Login Failed');
            $GLOBALS['username'] = $username;
            do_login_page('Incorrect username or password. Please try again.');
            echo "<script type=\"text/javascript\">document.forms['login_form'].elements['password'].focus();</script>";
        }
    } else { 
        // The correct POST variables were not sent to this page.
        do_header('Login Failed');
        do_login_page('Invalid request. Please try again.');
	}
?>
