<?php
 
// definiujemy dane do połączenia z bazą danych
define('DBHOST', 'mysql.cba.pl');
define('DBUSER', 'robozone');
define('DBPASS', 'picode');
define('DBNAME', 'robozone_cba_pl');
 
function db_connect() {
    // połączenie z mysql
    mysql_connect(DBHOST, DBUSER, DBPASS) or die('<h2>ERROR</h2> MySQL Server is not responding');
 
    // wybór bazy danych
    mysql_select_db(DBNAME) or die('<h2>ERROR</h2> Cannot connect to specified database');
}
 
function db_close() {
    mysql_close();
}
 
function clear($text) {
    // jeśli serwer automatycznie dodaje slashe to je usuwamy
    if(get_magic_quotes_gpc()) {
        $text = stripslashes($text);
    }
    $text = trim($text); // usuwamy białe znaki na początku i na końcu
    $text = mysql_real_escape_string($text); // filtrujemy tekst aby zabezpieczyć się przed sql injection
    $text = htmlspecialchars($text); // dezaktywujemy kod html
    return $text;
}
 
function codepass($password) {
    // kodujemy hasło (losowe znaki można zmienić lub całkowicie usunąć
    return sha1(md5($password).'#!%Rgd64');
}
 
// funkcja na sprawdzanie czy user jest zalogowany, jeśli nie to wyświetlamy komunikat
function check_login() {
    if(!$_SESSION['logged']) {
        die('<p>To jest strefa tylko dla użytkowników.</p>
        <p>[<a href="login.php">Logowanie</a>] [<a href="register.php">Zarejestruj się</a>]</p>');
    }
}
 
// funkcja na pobranie danych usera
function get_user_data($user_id = -1) {
    // jeśli nie podamy id usera to podstawiamy id aktualnie zalogowanego
    if($user_id == -1) {
        $user_id = $_SESSION['id'];
    }
    $result = mysql_query("SELECT * FROM `uzytkownicy` WHERE `id` = '{$user_id}' LIMIT 1");
    if(mysql_num_rows($result) == 0) {
        return false;
    }
    return mysql_fetch_assoc($result);
}
 
// startujemy sesje
session_start();
 
// jeśli nie ma jeszcze sesji "logged" i "user_id" to wypełniamy je domyślnymi danymi
if(!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
    $_SESSION['user_id'] = -1;
}
?>