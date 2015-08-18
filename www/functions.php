<?php
    function db_connect() // Connect to database with credentials below - returns mysqli instance
	{
	    $db_host = 'localhost';
        $db_username = 'power';
        $db_password = 'G6B4DdbJbSBRwzDe';
        $db_name = 'power_manager';
        
        return new mysqli($db_host, $db_username, $db_password, $db_name);
	    
	}
    
    function sec_session_start() // Start a secure session
    {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id. 
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        //session_regenerate_id(true); // regenerated the session, delete the old one.     
	}
    
    function login($username, $password, $db) {
        // Using prepared Statements means that SQL injection is not possible. 
        if ($stmt = $db->prepare("SELECT id, password, salt FROM users WHERE username = ? LIMIT 1")) { 
            $stmt->bind_param('s', $username); // Bind "$username" to parameter.
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();
            $stmt->bind_result($user_id, $db_password, $salt); // get variables from result.
            $stmt->fetch();
            $password = hash('sha512', $password.$salt); // hash the password with the unique salt.

            if($stmt->num_rows == 1) { // If the user exists
                // We check if the account is locked from too many login attempts
                if(checkbrute($user_id, $db) == true) { 
                    // Account is locked
                    // Send an email to user saying their account is locked
                    return false;
                } else {
                    $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user.
                    $user_agent = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
                    
                    if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
                        // Password is correct!
                        $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
                        $_SESSION['user_id'] = $user_id; 
                        $username = preg_replace("/[^a-zA-Z0-9@._\-]+/", "", $username); // XSS protection as we might print this value
                        $_SESSION['username'] = $username;
                        $_SESSION['login_string'] = hash('sha512', $password.$ip_address.$user_agent);
                        // Login successful.
                        
                        return true;
                    } else {
                        // Password is not correct
                        // We record this attempt in the database
                        $now = time();
                        $result = $db->query("INSERT INTO login_attempts (user_id, when, ip, user_agent) VALUES ('".$user_id."', '".$now."', '".ip2long($ip_address)."', '".$user_agent."')");
                        return false;
                    }
                }
            } else {
                // No user exists. 
                return false;
            }
        }
	}
	
	function checkbrute($user_id, $db) { // See if user is trying to brute force login
        // Get timestamp of current time
        $now = time();
        // All login attempts are counted from the past 2 hours. 
        $valid_attempts = $now - (2 * 60 * 60); 

        if ($stmt = $db->prepare("SELECT when FROM login_attempts WHERE user_id = ? AND when > '$valid_attempts'")) { 
            $stmt->bind_param('i', $user_id); 
            // Execute the prepared query.
            $stmt->execute();
            $stmt->store_result();
            // If there has been more than 5 failed logins
            if($stmt->num_rows > 5) {
                return true;
            } else {
                return false;
            }
        }
	}
	
    function login_check($db) // Check if user is logged in
    {
        // Check if all session variables are set
        if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];
            $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
            $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
            
            if ($stmt = $db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")) { 
                $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
                $stmt->execute(); // Execute the prepared query.
                $stmt->store_result();
                
                if($stmt->num_rows == 1) { // If the user exists
                    $stmt->bind_result($password); // get variables from result.
                    $stmt->fetch();
                    $login_check = hash('sha512', $password.$ip_address.$user_browser);
                    if($login_check == $login_string) {
                        // Logged In!!!!
                        
                        return true;
                    } else {
                        // Not logged in
                        return false;
                    }
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in
                return false;
            }
        } else {
            // Not logged in
            return false;
        }
	}
?>