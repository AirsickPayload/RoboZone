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
				?><meta http-equiv="refresh" content="5; URL=form_log.html"><?php
			}
        }
    }
	error_reporting(0); 				//UWAGA TYLKO DLA TESTU !!!!!!!
	//Jak nie jest zalogowany, zacznij od siły hasła
	echo '<form method="post" action="register.php">
			<p>
			Sprawdź siłę hasła:
			<input type="password" name="haslo">
			<input type="submit" value="Zatwierdź">
			</p>
			</form>		
		';
	
	echo "Obecna siła hasła w skali od 1 - 10 wynosi: ".testPassword($_POST['haslo']);
	
    // wyświetlamy formularz do rejestracji
    echo '<form method="post" action="register.php">
        <p>
            Imię:<br>
            <input type="text"  name="imie">
        </p>
        <p>
            Nazwisko:<br>
            <input type="text"  name="nazwisko">
        </p>
        <p>
            Nazwa użytkownika(login):<br>
            <input type="text"  name="login">
        </p>
        <p>
            Hasło:<br>
            <input type="password"  name="password">
        </p>
		<p>
            Powtórz hasło:<br>
            <input type="password"  name="password2">
        </p>
		<p>
            Adres e-mail:<br>
            <input type="text"  name="email">
        </p>
		<p>
            Uczelnia:<br>
            <input type="text"  name="uczelnia">
        </p>
        <p>
            <input type="submit" value="Zarejestruj">
			<input type="text" name="captcha_code" size="10" maxlength="6" />
			<a href="#" onclick="document.getElementById(\'captcha\').src = \'/securimage/securimage_show.php?\' + Math.random(); return false">[ Zmień obrazek ]</a>
        </p>
		
		<img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" />
    </form>';
} else {
    echo '<p>Jesteś już zalogowany, więc nie możesz stworzyć nowego konta.</p>
        <p>[<a href="index.php">Powrót</a>]</p>';
}
 
db_close();
?>