<meta http-equiv="content-type" content="text/html; charset=utf-8"><!--polskie znaki-->
<?php
echo "Witaj na stronie send.php";
include 'config.php';
db_connect();

$result = mysql_query("SELECT Count(`u_id`) FROM `Scripts`");
$row = mysql_fetch_row($result);
$l_skyptow = $row[0];
?>
<form action="" method="POST" >
<table border="1">
<?php
for ($i = 1; $i <= $l_skyptow; $i++) {
	$zapytanieNazwa = mysql_query("SELECT name FROM `Scripts` WHERE `script_id` = '{$i}'");
	$skryptNazwa = mysql_fetch_row($zapytanieNazwa);
	$zapytanieData = mysql_query("SELECT upload_date FROM `Scripts` WHERE `script_id` = '{$i}'");
	$skryptData = mysql_fetch_row($zapytanieData);
    ?>
	<tr>
		<td> <input type="radio" name="skrypty[]" value="<?php echo $skryptNazwa[0];?>|<?php echo $skryptData[0];?>" /> </td>	<td> <?php echo $skryptNazwa[0];?> </td>	<td> <?php echo $skryptData[0];?> </td>
	</tr>
	<?php
}
?>
</table>
<input name="upload" type="submit" id="upload" value="Zatwierdz">
</form>
<?php
for ($i = 0; $i <= 1; $i++){

	if(isset($_POST['skrypty'][$i]))
	{
		list($value1,$value2) = explode('|', $_POST['skrypty'][$i]);
		//echo "Zaznaczono: ".$_POST['skrypty'][$i];
		$zapytanie = mysql_query("SELECT script_id, u_id FROM `Scripts` WHERE `name` = '{$value1}' AND `upload_date` = '{$value2}' ");
		$skrypt = mysql_fetch_row($zapytanie);
		echo "script_id: ".$skrypt[0]."<br>"."u_id: ".$skrypt[1];
	//wysyłanie wybranego script_id($skrypt[0]) oraz u_id($skrypt[1])
	/*
		$sock = socket_create(AF_INET, SOCK_STREAM, 0);
		if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Couldn't create socket: [$errorcode] $errormsg \n");
		}
		if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Couldn't create socket: [$errorcode] $errormsg \n");
		}
 
		echo "Socket created \n";
 
		if(!socket_connect($sock , '74.125.235.20' , 80))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Could not connect: [$errorcode] $errormsg \n");
		}
 
		echo "Connection established \n";
 
		echo "Socket created";
		if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Couldn't create socket: [$errorcode] $errormsg \n");
		}
 
		echo "Socket created \n";
 
		//Connect socket to remote server
		if(!socket_connect($sock , '74.125.235.20' , 80))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Could not connect: [$errorcode] $errormsg \n");
		}
 
		echo "Connection established \n";
 
		$message1 = $skrypt[0];
		$message2 = $skrypt[1];
 
		//Send the message1 to the server
		if( ! socket_send ( $sock , $message1 , strlen($message1) , 0))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Could not send data: [$errorcode] $errormsg \n");
		}
 
		echo "Script_id send successfully \n";	
		
		//Send the message2 to the server
		if( ! socket_send ( $sock , $message2 , strlen($message2) , 0))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
     
			die("Could not send data: [$errorcode] $errormsg \n");
		}
 
		echo "u_id send successfully \n";*/
	}
}

db_close();
?>