<?php
    require_once('functions.php');
    require_once('output_fns.php');
    $db = db_connect();
    
    if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['p']) && !empty($_POST['p']) && isset($_POST['appliance0']) && isset($_POST['appliance1']) &&  // Verify all necessary POST data was sent
        isset($_POST['appliance2']) && isset($_POST['appliance3']) && isset($_POST['appliance4'])) {
        $username = $_POST['username'];        // Retrieve POST data and store into local variables
        $hashed_password = $_POST['p'];        // Python sends username, hashed password, and appliance states (0 or 1)
        $appliance0 = $_POST['appliance0'];
        $appliance1 = $_POST['appliance1'];
        $appliance2 = $_POST['appliance2'];
        $appliance3 = $_POST['appliance3'];
        $appliance4 = $_POST['appliance4'];
        
        if(login($username, $hashed_password, $db) == true) { // If login is successful
            $query = "
                UPDATE appliances 
                    SET state = CASE id
                        WHEN 0 THEN ?
                        WHEN 1 THEN ?
                        WHEN 2 THEN ?
                        WHEN 3 THEN ?
                        WHEN 4 THEN ?
                    END
                WHERE id IN (0,1,2,3,4);";
            if ($stmt = $db->prepare($query)) { // Prepare query to update states of appliances
				$stmt->bind_param('iiiii', $appliance0, $appliance1, $appliance2, $appliance3, $appliance4); // Bind variables to parameters.
				if (!$stmt->execute()) echo "<p class=\"alert\">Execute failed: (" . $stmt->errno . ") " . $stmt->error . "</p>"; // Execute the prepared query.
		        else echo 'Successfully updated appliances table';
		    } else echo "<p class=\"alert\">Prepare failed: (" . $db->errno . ") " . $db->error . "</p>";
        }
    } else echo 'Missing Data'; // Not all POST data was sent
?>