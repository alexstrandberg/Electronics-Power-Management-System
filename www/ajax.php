<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();
    
    // Start a secure session
	sec_session_start();
    
    if(login_check($db) == false) {
		// User is not logged in.
	   do_login_page('Please login to use this site.');
	   exit();
	}
    
    // Check if all necessary POST data was sent to either turn the appliance on or off
    if (isset($_POST['appliance_number']) && isset($_POST['state'])) {
            if ($_POST['appliance_number'] == 0 && $_POST['state'] == 0) { // Handle shutting off all appliances when power supply is shut off
                $query = "
                    UPDATE appliances 
                        SET state = 0
                    WHERE id IN (0,1,2,3,4);";
                if ($stmt = $db->prepare($query)) { 
                    if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
                } else echo "<p class=\"alert\">Prepare failed: (" . $db->errno . ") " . $db->error . "</p>";
            } else { // Handle turning power supply on or turning any other appliance on or off
                if ($stmt = $db->prepare('UPDATE appliances SET state = ? WHERE id = ?;')) { 
                    $stmt->bind_param('ii', $_POST['state'], $_POST['appliance_number']); // Bind variables to parameters.
                    if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
                } else echo "<p class=\"alert\">Prepare failed: (" . $db->errno . ") " . $db->error . "</p>";
            }
    }
	
    // Output information about appliances for home page - id, name, state
	if ($stmt = $db->prepare("SELECT id, name, state FROM appliances WHERE enabled=1;")) { 
		if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
		$stmt->store_result();
		$stmt->bind_result($id, $name, $state); // get variables from result.
        
		if($stmt->num_rows != 0)  {
            echo '<table id="main_appliance_list"><tr><th>ID</th><th>Name</th><th>State</th></tr>';
			while ($stmt->fetch()) { // Output info to table - allow user to change state of an appliance
				echo '<tr><td>'.$id.'</td><td>'.$name.'</td><td><input type="image" class="appliance_button" src="power_symbol_';
                if ($id == 0) { // Handle main power supply being off - can't turn other appliances on while main power supply is off
                    $appliance0_state = $state;
                    if ($state == 0) echo 'off.png';
                    else if ($state == 1) echo 'on.png';
                } 
                else if ($appliance0_state == 0) echo 'disabled.png" disabled="1'; // Prevent user from turning on any other appliances while power supply is off
                else if ($state == 0) echo 'off.png';
                else if ($state == 1) echo 'on.png';  
                echo '" id="'.$id.'" /></td></tr>';
			}
            echo '</table>';
		}
	}
?>