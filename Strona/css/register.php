<meta http-equiv="content-type" content="text/html; charset=utf-8">
<!--Polskie znaki -->
<?php
include 'config.php';
include 'pass_checker.php';

db_connect();

	include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
	$securimage = new Securimage();
	if(isset($_POST['captcha_code'])){
	if ($securimage->check($_POST['captcha_code']) == false) {
		echo "Tekst z obrazka źle przepisany, przeglądarka powróci do strony rejestracji za 7 sekund.";
		?><meta http-equiv="refresh" content="7; URL=register.php"><?php
		exit;
	}}
// sprawdzamy czy user nie jest przypadkiem zalogowany
if(!$_SESSION['logged']) {
    // jeśli zostanie naciśnięty przycisk "Zarejestruj"
    if(isset($_POST['login'])) {
        // jeśli serwer automatycznie dodaje slashe to je usuwamy
		// usuwamy białe znaki na początku i na końcu
		// filtrujemy tekst aby zabezpieczyć się przed sql injection za pomocą mysql_real_escape_string() - jak na wiki
		// dezaktywujemy kod html
        $_POST['imie'] = clear($_POST['imie']);
        $_POST['nazwisko'] = clear($_POST['nazwisko']);
        $_POST['login'] = clear($_POST['login']);
        $_POST['password'] = clear($_POST['password']);
		$_POST['password2'] = clear($_POST['password2']);
		$_POST['email'] = clear($_POST['email']);
		$_POST['uczelnia'] = clear($_POST['uczelnia']);
 
        // sprawdzamy czy wszystkie pola zostały wypełnione
        if(empty($_POST['imie']) || empty($_POST['nazwisko']) || empty($_POST['login']) || empty($_POST['password']) || empty($_POST['password2']) || empty($_POST['email']) || empty($_POST['uczelnia'])) {
            echo '<p>Musisz wypełnić wszystkie pola.</p>';
        // sprawdzamy czy podane dwa hasła są takie same
        } elseif($_POST['password'] != $_POST['password2']) {
            echo '<p>Podane hasła różnią się od siebie.</p>';
        // sprawdzamy poprawność emaila
        } elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
            echo '<p>Podany email jest nieprawidłowy.</p>';
        } else {
            // sprawdzamy czy są jacyś użytkownicy z takim loginem lub adresem email
            $result = mysql_query("SELECT Count(`user_id`) FROM `Users` WHERE `login` = '{$_POST['login']}' OR `address` = '{$_POST['email']}'");
            $row = mysql_fetch_row($result);
            if($row[0] > 0) {
                echo '<p>Już istnieje użytkownik z takim loginem lub adresem e-mail.</p>';
            } else {
                // jeśli nie istnieje to kodujemy haslo...
                $_POST['password'] = codepass($_POST['password']);
                // i wykonujemy zapytanie na dodanie usera
                mysql_query("INSERT INTO `Users` (`name`, `lastname`,`login`,`pass`,`address`,`university`) VALUES ('{$_POST['imie']}', '{$_POST['nazwisko']}', '{$_POST['login']}', '{$_POST['password']}', '{$_POST['email']}', '{$_POST['uczelnia']}')");
                echo '<p>Zostałeś poprawnie zarejestrowany! Możesz się teraz <a href="logowanie.php">zalogować</a>. Przeglądarka przekieruje automatycznie za 5 sekund.</p>';
				?><meta http-equiv="refresh" content="5; URL=index.html"><?php
			}
        }
    }
	error_reporting(0); 				//UWAGA TYLKO DLA TESTU !!!!!!!
	//Jak nie jest zalogowany, zacznij od siły hasła
	}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<!--META-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--STYLESHEETS-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!--SCRIPTS-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>


</head>
<body id="lic">

<!--WRAPPER-->
<div id="wrapper">

<!--LOGIN FORM-->
<form name="login-form" class="login-form" method="post" action="register.php">

	<!--HEADER-->
    <div class="header">
    <!--TITLE-->Zarejestruj się<!--END TITLE-->
    </div>
    <!--END HEADER-->
	
	<!--CONTENT-->
    <div class="content">
	
	<!--imie--><input name="imie" type="text" class="input username" value="Imie" onfocus="this.value=''" />
    <!--nazwisko--><input name="nazwisko" type="text" class="input username" value="Nazwisko" onfocus="this.value=''" /> 
	<!--username--><input name="login" id="login" type="text" class="input username" value="Nazwa Użytkownika" onfocus="this.value=''" />
	<div class="input username>
	<input onfocus="return makeItPassword()" name="pass_word" type="text" id="password" class="testresult" value="Wpisz haslo" onfocus="this.value=''"/>
	<div>
    <!--haslo2--><input name="password2" type="password" class="input username" value="Powtórz haslo" onfocus="this.value=''" />
	<!--e-mail--><input name="email" type="text" class="input username" value="E-mail" onfocus="this.value=''" />
    <!--uczelnia--><input name="uczelnia" type="text" class="input username" value="Uczelnia" onfocus="this.value=''" />
	<!--uczelnia--><input name="captcha_code" type="text" class="input username" value="Przepisz kod z obrazka:" onfocus="this.value=''" />
	<a href="#" onclick="document.getElementById(\'captcha\').src = \'/securimage/securimage_show.php?\' + Math.random(); return false">[Zmień obrazek]</a>


	</div>
	<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />

    <!--END CONTENT-->
    <!--FOOTER-->
    <div class="footer">
    <!--REGISTER BUTTON--><input type="submit" name="submit" value="Zarejestruj" class="register" /><!--END REGISTER BUTTON-->
	</form>
	
    </div>
    <!--END FOOTER-->


<!--END LOGIN FORM-->
</div>

<!--END WRAPPER-->

<!--GRADIENT--><div class="gradient"></div><!--END GRADIENT-->
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
 <script src="script.js"></script>	
 <script>
  $(document).ready( function() {

	$(".testresult").passStrength({
	 userid:	"#login"
	});
				
 $(".password_adv").passStrength({
					shortPass: 		"top_shortPass",
					badPass:		"top_badPass",
					goodPass:		"top_goodPass",
					strongPass:		"top_strongPass",
					baseStyle:		"top_testresult",
					userid:			"#user_id_adv",
					messageloc:		0
				});
			});

      function makeItPassword()
      {
         document.getElementById("password")
            .innerHTML = "<input id=\"password\" name=\"pass_word\" type=\"password\" class=\"testresult\"/>";
         document.getElementById("password").focus();
      }
 
 </script>
</body>
</html>
<? 
db_close();
?>