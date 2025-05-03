<?php
    $servername = "localhost";
    $dbusername = "robert";
    $dbpassword = "Naotedigo420";
    $database = "autocarros";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $database);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
function my_query($sql,$conn, $debug=0) {
	if($debug) echo $sql;
	$result = $conn->query($sql);
	
	/* SELECT
	mysqli_result Object
	(
	    [current_field] => 0
	    [field_count] => 5
	    [lengths] => 
	    [num_rows] => 3
	    [type] => 0
	)
	*/

	/* UPDATE
	1: correu tudo bem
	0: erro na QUERY
	*/

	/* DELETE
	1: correu tudo bem
	0: erro na QUERY
	*/

	/* INSERT
	id: correu tudo bem
	0: erro na QUERY
	*/
	
	if(isset($result->num_rows)) { // SELECT
		$arrRes = array();
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		        $arrRes[] = $row;
		    }
		}
		return $arrRes;
	}
	else if ($result === TRUE) { // INSERT, DELETE, UPDATE
		if($last_id = $conn->insert_id) {
			return $last_id;
		}
		return 1;
	} 
	return 0;
}
?>