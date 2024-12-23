<!DOCTYPE html>
<html><head>
<link rel="stylesheet" href="style.css" />
</head><body>

<?php
require_once 'cnct_chronic_bot.php';
require_once 'clas_chronic_bot.php';
date_default_timezone_set( 'Europe/Moscow' );

$dblink = mysqli_connect($dbhost, $dbuser, $dbpswd, $dbname);
if (mysqli_connect_errno())
	echo mysqli_connect_error();

$dbquery = "select * from ".$tbname."";
if ($result = mysqli_query($dblink, $dbquery)) {
	echo '<table>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>['.$row['chat_id'].']'
			.'<td>'.$row['user_name']
			.'<td>'.$row['user_lang']
			.'<td>'.$row['msg_id']
			.'<td>'.$row['flag'];

		$user_list = json_decode($row['user_list'], false, 512, JSON_UNESCAPED_UNICODE);
		echo '<td><table><tr><th>chronic';
		foreach ($user_list as $c => $chronic)
			echo '<tr><td>'.$c.' - '.$chronic;
		echo '</table>';
		
		$user_day = json_decode($row['user_day'], false, 512, JSON_UNESCAPED_UNICODE);
		echo '<td><table><tr><th>time<th>msg_id<th>chronic<th>comment';
		foreach ($user_day as $chronic) {
			echo '<tr><td>'.date('H:i:s', $chronic->ctime);
			echo '<td>'.$chronic->msg_id;
			echo '<td>'.$chronic->chronic;
			echo '<td>'.$chronic->comment;
		} echo '</table>';

    } echo '</table>';
	mysqli_free_result($result);
}

// $dbdrop = "DROP TABLE `".$tbname."`";
// if (mysqli_query($dblink, $dbdrop))
	// echo '<br>Table dropped';
// else echo mysqli_error($dblink);

// $dbcreate = "CREATE TABLE `".$tbname."` (
// `id` INT NOT NULL AUTO_INCREMENT, 
// `chat_id` INT NOT NULL, 
// `msg_id` INT, 
// `flag` TEXT, 
// `user_name` TEXT, 
// `user_lang` TEXT, 
// `user_list` TEXT, 
// `user_day` TEXT, 
// PRIMARY KEY (`id`)) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_general_ci;";
// if (mysqli_query($dblink, $dbcreate))
	// echo '<br>Table created';
// else echo mysqli_error($dblink);

mysqli_close($dblink); ?></body></html>