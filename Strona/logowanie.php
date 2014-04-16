<meta http-equiv="content-type" content="text/html; charset=utf-8"><!--polskie znaki-->
<?php
include 'config.php';//dostep do funkcji kodującej hasło

error_reporting(0); 																//UWAGA TYLKO DLA TESTU !!!!!!!

session_start();

if(isset($_GET['akcja']) && $_GET['akcja']=='wyloguj'){
	unset($_SESSION['zalogowany']);
}
db_connect();

if(isset($_SESSION['zalogowany'])) {//jezeli zalogowany to przezucam na maina
?><meta http-equiv="refresh" content="0; URL=main.php"><?php
}else{

if(isset($_POST['wyslij'])) {

   if(mysql_num_rows(mysql_query("SELECT `login`
   FROM `Users` WHERE `login` = '".$_POST['login']."' ")) > 0) {//Sprawdzamy czy podany użytkownik istnieje w bazie danych


       if(mysql_num_rows(mysql_query("SELECT `user_id` FROM `Users`
       WHERE `login` = '".$_POST['login']."' 
       && pass = '".codepass($_POST['haslo'])."' ")) > 0 ) {//sprawdzamy czy hasło do podanego użytkownika jest poprawne


           $_SESSION['zalogowany'] = true;
           $_SESSION['login'] = $_POST['login'];
           $_SESSION['haslo'] = codepass($_POST['haslo']);
           echo "Zostałeś poprawnie zalogowany.";
		   ?><meta http-equiv="refresh" content="2; URL=main.php">
		   <!--<a href="logowanie.php?akcja=wyloguj"> <p>Wyloguj</p> </a>-->
		   <?php


       } else { 

   echo "Złe hasło, proszę spróbować ponownie. Nastąpi powrót do strony logowania.";
   ?><meta http-equiv="refresh" content="5; URL=index.html"><?php
}
} else { 
   echo "Nie ma takiego użytkownika. Nastąpi powrót do strony logowania."; ?>
   <meta http-equiv="refresh" content="5; URL=index.html">
   <?php
}
} else { 

?>
<meta http-equiv="refresh" content="1; URL=index.html">
<!--Jak użytkownik się wyloguje to przezuci go na plik index.html w ciagu jednej sekundy-->  

<?php
}
}
?>