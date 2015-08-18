<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();
    
    // Start a secure session
	sec_session_start();
	
	do_header('Power Manager'); // Make a header with the title in quote marks.
    
    do_nav_bar();
	start_content();
    
    $registration_enabled = FALSE; // Set to TRUE when registering new users
    
    if ($registration_enabled) { // Only process registrations if variable above is TRUE
        if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['p']) && !empty($_POST['p'])) { // Register user if all necessary POST data was sent
            // The hashed password from the form
            $username = $_POST['username'];
            $password = $_POST['p'];
            
            // Create a random salt
            $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
            // Create salted password (Careful not to over season)
            $password = hash('sha512', $password.$random_salt);
             
            if ($insert_stmt = $db->prepare("INSERT INTO users (id, username, password, salt) VALUES (0, ?, ?, ?);")) { // Add user to users table in database
                $insert_stmt->bind_param('sss', $username, $password, $random_salt); 
                // Execute the prepared query.
                $insert_stmt->execute();
                echo '<h2 class="alert">Success</h2>';
            } else {
                echo '<h2 class="alert">Failed</h2>';
            }
        } else echo '
            <script type="text/javascript" src="sha512.js"></script>
            <script type="text/javascript" src="forms.js"></script>
            <div id="login_box">
            <p class="alert" id="error_text">Please enter a username and password to register (Character limit: 20).</p>
            <form action="register.php" method="post" name="login_form" onsubmit="formhash(login_form, login_form.password);">
               <p>Username:&nbsp;<input type="text" name="username" id="username" size="20" maxlength="20" /></p>
               <p>Password:&nbsp;&nbsp;<input type="password" name="password" id="password" length="20" size="20" /></p>
               <div align="center"><input type="submit" value="Register" /></div>
            </form>
            </div>'; // Show registration form if POST data is not sent
    } else echo '<h2 class="alert">Registration disabled</h2>';
    
    do_footer();
?>