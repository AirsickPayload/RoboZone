<meta http-equiv="content-type" content="text/html; charset=utf-8"><!--polskie znaki-->
<?php
include 'config.php';//dostep do funkcji kodującej hasło

error_reporting(0); 				//UWAGA TYLKO DLA TESTU !!!!!!!

session_start();

if(isset($_GET['akcja']) && $_GET['akcja']=='wyloguj'){
	unset($_SESSION['zalogowany']);
}
$link = mysql_connect("mysql.cba.pl","roboze","picode");
mysql_select_db("robozone_cba_pl");

if(isset($_SESSION['zalogowany'])) {
echo "Witaj, ".$_SESSION['login']; ?>
<a href="logowanie.php?akcja=wyloguj"> <p>Wyloguj</p> </a>
<!--akcja to zmienna przesyłana metodą GET o wartości wyloguj-->		   
<?php
}else{

if(isset($_POST['wyslij'])) {

/* if(mysql_num_rows(mysql_query("SELECT login, haslo ------------------------ !(Sprawdź czy w ogóle istnieje taki użytkownik)
   FROM Users WHERE login = '".$_POST['login']."' 
   && haslo = '".codepass($_POST['haslo'])."' ")) > 0) { */

   if(mysql_num_rows(mysql_query("SELECT login
   FROM Users WHERE login = '".$_POST['login']."' ")) > 0) {//Sprawdzamy czy podany użytkownik istnieje w bazie danych


       if(mysql_num_rows(mysql_query("SELECT user_id FROM Users
       WHERE login = '".$_POST['login']."' 
       && haslo = '".codepass($_POST['haslo'])."' ")) > 0 ) {//sprawdzamy czy hasło do podanego użytkownika jest poprawne


           $_SESSION['zalogowany'] = true;
           $_SESSION['login'] = $_POST['login'];
           $_SESSION['haslo'] = codepass($_POST['haslo']);
           echo "Jesteś zalogowany.";
		   ?>
		   <a href="logowanie.php?akcja=wyloguj"> <p>Wyloguj</p> </a>
		   <?php


       } else { 

   echo "Złe hasło, proszę spróbować ponownie. Nastąpi powrót do strony logowania.";
   ?><meta http-equiv="refresh" content="5; URL=form_log.html"><?php
}
} else { 
   echo "Nie ma takiego użytkownika. Nastąpi powrót do strony logowania."; ?>
   <meta http-equiv="refresh" content="5; URL=form_log.html">
   <?php
}
} else { 

?>
<meta http-equiv="refresh" content="1; URL=form_log.html">
<!--Jak użytkownik się wyloguje to przezuci go na plik form_log.html w ciagu jednej sekundy-->  

<?php
}
}
?>