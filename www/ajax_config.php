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
    
    // Check if all necessary POST data was sent to either update the name or enabled status of an appliance
    if (isset($_POST['appliance_name_number']) && isset($_POST['name']) && !empty($_POST['name'])) {
        if ($stmt = $db->prepare('UPDATE appliances SET name = ? WHERE id = ?;')) { 
            $stmt->bind_param('si', $_POST['name'], substr($_POST['appliance_name_number'], -1)); // Bind variables to parameters.
            if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
        } else echo "<p class=\"alert\">Prepare failed: (" . $db->errno . ") " . $db->error . "</p>";
    } else if (isset($_POST['appliance_enabled_number']) && substr($_POST['appliance_enabled_number'], -1) != 0 && isset($_POST['enabled'])) {
        if ($_POST['enabled'] == "true") $query = 'UPDATE appliances SET enabled = 1 WHERE id = ?;';
        else $query = 'UPDATE appliances SET enabled = 0, state = 0 WHERE id = ?;';
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param('i', substr($_POST['appliance_enabled_number'], -1)); // Bind variables to parameters.
            if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
        } else echo "<p class=\"alert\">Prepare failed: (" . $db->errno . ") " . $db->error . "</p>";
    }
	
    // Output information about appliances for config page - id, name, and enabled status
	if ($stmt = $db->prepare("SELECT id, name, enabled FROM appliances WHERE 1=1;")) { 
		if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
		$stmt->store_result();
		$stmt->bind_result($id, $name, $enabled); // get variables from result.
        
		if($stmt->num_rows != 0)  {
            echo '<table id="main_appliance_list"><tr><th>ID</th><th>Name</th><th>Enabled</th></tr>';
			while ($stmt->fetch()) { // Output info to table - allow user to change name and enabled status of an appliance
				echo '<tr><td>'.$id.'</td><td><input type="text" class="appliance_name" id="name'.$id.'" size="30" maxlength="30" value="'.$name.'" /></td><td><input type="checkbox" class="appliance_enabled" id="enabled'.$id.'" ';
                if ($enabled == 1) echo 'checked="true"';
                if ($id == 0) echo ' disabled="1"'; // User can't disable power supply
                echo ' /></td></tr>';
			}
            echo '</table>';
		}
	}
?>