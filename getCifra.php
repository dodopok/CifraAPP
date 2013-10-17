<?php
	include 'include/database.php';
	
	$chord = mysql_fetch_array(mysql_query('SELECT * FROM chords WHERE id = "'.mysql_escape_string($_GET['id']).'" LIMIT 1'));

	$chord['title'] = utf8_encode($chord['title']);
	$chord['artist'] = utf8_encode($chord['artist']);
	$chord['chord'] = utf8_encode($chord['chord']);

	echo json_encode($chord);
?>