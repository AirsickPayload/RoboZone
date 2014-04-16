<meta http-equiv="content-type" content="text/html; charset=utf-8"><!--polskie znaki-->
<?php																												//tutaj trzeba dac sprawdzania czy zalogowany od razu !
include 'config.php';
error_reporting(0); 				//UWAGA TYLKO DLA TESTU !!!!!!!

session_start();

echo "Welcome to main.php file <br>"; // znak nowej lini htmla
echo "Witaj, ".$_SESSION['login']; 
?>
<a href="logowanie.php?akcja=wyloguj"> <p>Wyloguj</p> </a>
<!--akcja to zmienna przesyłana metodą GET o wartości wyloguj-->

    <table border="1" cellpadding="5" width="200" height="150" align="left"> <!--cellspadding-margines, width i hegight to wymiary tabeli, "left" - wyrównanie tabeli do lewej strony (domyślnie), względem otaczającego tekstu--> 
	<caption align="center">Dodawanie skryptów</caption>
	<form action="" method="POST" enctype="multipart/form-data">
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[0]" value=true id="1" /> <!--id uzywane dla skryptów wykonywanych po stronie klienta przez javascript, tak jakby trzeba było to zostawiam-->
			</td>
			<td> 
				<input type="file" name="userfile[]"  id="userfile" multiple ><!--albo bez / - nie wiem-->
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[1]" value=true id="2" />
			</td>
			<td> 
				<input type="file" name="userfile[]"  id="userfile" multiple >
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[2]" value=true id="3" />
			</td>
			<td> 
				<input type="file" name="userfile[]"  id="userfile" multiple >
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[3]" value=true id="4" />
			</td>
			<td> 
				<input type="file" name="userfile[]"  id="userfile" multiple >
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[4]" value=true id="5" />
			</td>
			<td> 
				<input type="file" name="userfile[]"  id="userfile" multiple >
			</td>
		</tr>
		<tr>
			<td align="center">
				<input type="checkbox" name="skrypty[5]" value=true id="6" />
			</td>
			<td> 
				6 skrypt-testowy(musi być zaznaczony)
			</td> 
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input name="upload" type="submit" class="box" id="upload" value=" Prześlij do bazy danych "><br> 
			</td>
		</tr>
		</form>
	</table>

<center>
      <img src="http://robozone.no-ip.biz:8080/?action=stream" />
</center>

		<?php
			error_reporting(0);							//UWAGA TYLKO DLA TESTU !!!!!!!
			for ($i = 0; $i <= 5; $i++){			
				if($_POST['skrypty'][$i])
				{
					//echo "Checkbox nr.: ".($i+1)." ma wartość: ".$_POST['skrypty'][$i]."<br>";									//TYLKO DO TESTu
					//echo "Jestem w ".$i." petli";?><br><?php		//TYLKO DO TESTu
				}
				else{//jezeli checkbox jest odznaczony
					$_FILES['userfile']['tmp_name'][$i]="stop";
					//echo "Checkbox nr.: ".($i+1)." ma wartość: ".$_POST['skrypty'][$i]." userfile".$i."-size: ".$_FILES['userfile']['size'][$i];
					
					//echo "FALSE ";								//TYLKO DO TESTu
					//echo "Jestem w ".$i." petli";?><br><?php		//TYLKO DO TESTu
				}
			}
		?>

<!--<form method="post" enctype="multipart/form-data"> <!--An upload form must have encytype="multipart/form-data" otherwise it won't work at all.
<table width="350" border="3" cellpadding="2" cellspacing="2" class="box">
<tr>
<td width="246">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000"><!--hidden input MAX_FILE_SIZE before the file input. It's to restrict the size of files.
<input type="file" name="userfile[]"  id="userfile" multiple ><!--albo bez / - nie wiem

</td>
<td width="80"><input name="upload" type="submit" class="box" id="upload" value=" Upload "></td>
</tr>
</table>
</form>-->
<?php
if(isset($_POST['upload'])){ //check to see if the file was successfully uploaded by looking at the file size. 
																//If it's larger than zero byte then we can assume that the file is uploaded successfully

$l_plikow=0;
foreach ($_FILES['userfile']['name'] as $filename) {
    echo $filename.'<br/>';
	$l_plikow++;
	if(is_uploaded_file($_FILES['userfile']['tmp_name'][$i])){exit("Zły upload ".$_FILES['userfile']['name'][$i]." skrypt zaktrzymany.");}
	//is_uploaded_file(). Sprawdza ona czy podany plik faktycznie został odebrany od użytkownika – sprawdzenie takie jest istotne, 
	//gdyż w przypadku źle napisanego skryptu “włamywacz” będzie mógł odczytać z serwera dowolny plik
}

foreach ($_FILES['userfile']['size'] as $filesizeFE) {
	if($filesizeFE <= 0){/*exit("Rozmiar pliku jest mniejszy od zera, skrypt zaktrzymany.");*/$_FILES['userfile']['tmp_name'][$i]="stop";}
}
echo "Istnieje: ".$l_plikow." plików.<br>";																
																
for ($i = 0; $i < $l_plikow; $i++) {
	$fileSize[$i]=$_FILES['userfile']['size'][$i]; // size to wielkość w bajtach przesyłanego pliku
	$fileType[$i] = $_FILES['userfile']['type'][$i]; // type to typ przesyłanego pliku
	$fileName[$i] = $_FILES['userfile']['name'][$i]; //tmp_name nazwa tymczasowej kopii pliku przechowywanego na serwerze
	$tmpName[$i]  = $_FILES['userfile']['tmp_name'][$i]; //tmp_name nazwa tymczasowej kopii pliku przechowywanego na serwerze
	
	$fp = fopen($tmpName[$i], 'r');//r-plik tylko do odczytu; Zmienna $fp zawiera teraz wskaźnik do pliku
	$content[$i] = fread($fp, filesize($tmpName[$i])); //filesize($tmpName) wczyta cały plik do zmiennej content,nie pobiera jako parametr wskaźnika do pliku ale nazwę pliku
	$content[$i] = addslashes($content[$i]);
	fclose($fp);
}//czyli content bedzie miała rozmiar l_plikow licząc od 0

if(!get_magic_quotes_gpc())//Sprawdza czy są aktywne "magiczne cudzysłowy" jak nie to se sami uzyjemy addslashes(). Jeśli zwróci true, wszystkie ukośniki będą usunięte.
{
	for ($i = 0; $i < $l_plikow; $i++) {
    $fileName[$i] = addslashes($_FILES['userfile']['name'][$i]);//addslashes(), Zwraca ciąg znaków, który został zabezpieczony przed niebezpiecznymi znakami, znakiem ucieczki '' (back slash). 
	}															//Te znaki zapytania to pojedynczy cudzysłów ( '), podwójny cudzysłów ( "), backslash (\) i NUL.																							
}

db_connect();

if(isset($_SESSION['zalogowany'])){
	date_default_timezone_set('Europe/Warsaw');
	$data = date('Y-m-d H:i:s');
	$ses_id = session_id();

	for ($i = 0; $i < $l_plikow; $i++) {
	if($_FILES['userfile']['tmp_name'][$i] != "stop" ){//<0 czyli rozne
		$query[$i] = "INSERT INTO `Scripts`(`u_id`, `name`, `script_data`, `script_size`, `upload_date`) ".//query będzie miała długość l_plikow
			"VALUES ('1','$fileName[$i]', '$content[$i]', '$fileSize[$i]', '$data')";
		mysql_query($query[$i]) or die('Error, błąd w zapytaniu: '.($i+1));
		echo "<b><font color=\"green\">Plik $fileName[$i] załadowany pomyślnie !</font></b><br>";
		}
		else {echo "<font color=\"red\">Ten nie został dodany do bazy:<b>".$_FILES['userfile']['name'][$i]."</b></font><br>";}
	}
} 	
else
	{
		echo "Aby przesyłać skrypty musisz być zalogowany";
	}
	
db_close();
} else{ //to jest else to is_upload_files()
	echo "Post upload przenieść na nowy plik php -> ten błąd teraz nie ma sensu.(Błąd przy przesyłaniu danych!)";
}
?>
