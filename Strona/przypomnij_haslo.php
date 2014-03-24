<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php
error_reporting(0); 				//UWAGA TYLKO DLA TESTU !!!!!!!
define('IN_SCRIPT', true);
// Start a session
session_start();

include 'config.php';
db_connect();//łączy z bazą

//Ta funkcja będzie wyświetlać błędy w oknie aleret, używanym do wpisywania 
//loginu więc jeśli pole jest nie właściwe cały czas będzie info
function error($msg) {
    /*?>
    <html>
    <head>
    <script language="JavaScript">
    <!--
        alert("<?php=$msg?>");
        history.back();
    //-->
    </script>
    </head>
    <body>
    </body>
    </html>
    <?php*/
	echo "Mamy problem: ".$msg." - Przeglądarka powróci za 5 sekund.";
	?><meta http-equiv="refresh" content="5; URL=przypomnij_haslo.php"><?php
    exit;
}
//Tutaj sprawdza się, że adres email który został dodany do bazy danych ma poprawny format
function check_email_address($email) {
  // Spr. długość i użycie znaku @
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
	// Adres nieprawidłowy ponieważ zła liczba znaków uzytych w jednej z sekcji albo zła liczba symboli @.
    return false;
  }
  // Dla ulatwienia dzieli na sekcje
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
     if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      return false;
    }
  }  
  //ereg() - regular expression match
  //explode() - Split a string by string
 // Znak ^ oznacza początek ciągu, a znak $ koniec lub "prawie" koniec
 //? - 0 lub 1
 //+ - 1 lub więcej
 //. - kropka symbolizuje dowolny znak (za wyjątkiem przełamania linii)
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Sprawdza czy jest domena IP. 
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Za mało częsci aby tworzyło domenę
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}


if (isset($_POST['zatwierdz'])) {
	
	if ($_POST['podanyemail']=='') {
		error('Wpisz adres e-mail.');
	}
	if(get_magic_quotes_gpc()) {
		$podanyemail = htmlspecialchars(stripslashes($_POST['podanyemail']));
	} 
	else {
		$podanyemail = htmlspecialchars($_POST['podanyemail']);
	}
	//Sprawdź podany adres email czy jest poprawny
	if (!check_email_address($_POST['podanyemail'])) {
  		error('Email nie jest poprawny musi mieć format: nazwa@domena.dnp');
	}
    // Sprawdza czy e-mail istnieje	
    $sql = "SELECT COUNT(*) FROM `uzytkownicy` WHERE `e-mail` = '$podanyemail'";
    $result = mysql_query($sql);//or die('Nie mogę odnaleźć takiego użytkownika: ' . mysql_error());
    if (!mysql_result($result,0,0)>0) {
        error('Podany adres e-mail nie został odnaleziony!');
    }

	//Generuje lowoy hash MD5 na nowe hasło
	$random_password=codepass(uniqid(rand()));
	
	// Weź pierwsze 8 liczb i uzyj ich jako hasła które chcemy wysłać użytkownikowi
	$emailhaslo=substr($random_password, 0, 8);
	
	//Szyfruje hasło SHA-1 i MD5 - funkcja z pliku config.php
	$newpassword = codepass($emailhaslo);
	
        // Bezpieczne zapytanie (uwaga na sql injection)
       	$query = sprintf("UPDATE `uzytkownicy` SET `haslo` = '%s' 
						  WHERE `e-mail` = '$podanyemail'",
                    mysql_real_escape_string($newpassword));
					
					mysql_query($query)or die('Nie mogę odnaleźć takiego użytkownika: ' . mysql_error());

//Wyśli email z informacjami i nowym hasłem
$subject = "Przywrócenie hasła"; 
$message = "Twoje nowe hasło:
---------------------------- 
Password: $emailhaslo
---------------------------- 
Ta informacja została zaszyfrowana w naszej bazie danych 

E-mail wygenerowany automatycznie."; 
        echo "HASLO WYSLANE/ZMIENIONE - BRAK DEMONA POCZTY ! - TYLKO TEST PRZEKIEROWANIA (za 5 sek. na strone log.)";
		?><meta http-equiv="refresh" content="5; URL=form_log.html"><?php
		exit;
          if(!mail($podanyemail, $subject, $message,  "FROM: robozone1 <robozone1@gmail.com>")){
             die ("Wysłanie e-mail nie powiodło się, skontaktuj się z administratorem strony! ($site_email)"); 
          }else{ 
                error('Nowe hasło zostało wysłane!.');
         }
	}
	
else {
?>
      <form name="podanyemailform" action="przypomnij_haslo.php" method="post">
        <table border="2" cellspacing="5" cellpadding="7" width="15%">
          <caption>
          <div>Przywracanie hasła</div>
          </caption>
          <tr>
            <td>Adres e-mail:</td>
            <td><input name="podanyemail" type="text" value="" id="podanyemail" /></td>
          </tr>
          <tr>
            <td colspan="2" class="footer"><input type="submit" name="zatwierdz" value="Zatwierdź" class="mainoption" /></td>
          </tr>
        </table>
      </form>
      <?php
}
?>