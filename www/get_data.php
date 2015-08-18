<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();
	
	if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['p']) && !empty($_POST['p'])) { // Verify all necessary POST data was sent
        $username = $_POST['username'];    // Retrieve POST data and store into local variables
        $hashed_password = $_POST['p'];    // Python sends username and hashed password
        
        if(login($username, $hashed_password, $db) == true) { // If login is successful
            if ($stmt = $db->prepare("SELECT id, name, state FROM appliances WHERE 1=1;")) { 
                if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
                $stmt->store_result();
                $stmt->bind_result($id, $name, $state); // get variables from result.
        
                if($stmt->num_rows != 0)  {
                    while ($stmt->fetch()) {
                        echo '!'.$id.'@'.$state.'#'; // Output format is appliance id and state with symbols surrounding data - ex: !1@1# - Appliance 1 is on
                    }
                }
            }
        }
    } else echo 'Missing Data'; // Not all POST data was sent
?>