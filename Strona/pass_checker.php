<?php
function testPassword($password)
{

    if ( strlen( $password ) == 0 )
    {
        return 1;
    }

    $strength = 0;

    //pobiera długość hasła
    $length = strlen($password);

    //Sprawdza czy hasło nie jest zapisane tylko małymi literami
    if(strtolower($password) != $password)
    {
        $strength += 1;
    }
    
    //Sprawdza czy hasło nie jest zapisane tylko dużymi literami
    if(strtoupper($password) == $password)
    {
        $strength += 1;
    }

    //Sprawdza czy ciąg znaków jest długości 8 - 15
    if($length >= 8 && $length <= 15)
    {
        $strength += 1;
    }

    //Sprawdza czy ciąg znaków jest długości 16 - 35
    if($length >= 16 && $length <=35)
    {
        $strength += 2;
    }

    //Sprawdza czy ciąg znaków jest dłuższy niż 35
    if($length > 35)
    {
        $strength += 3;
    }
    
    //Pobiera ilość cyfr użytych w haśle
    preg_match_all('/[0-9]/', $password, $numbers);
    $strength += count($numbers[0]);

    //Sprawdza wystąpienia znaków szczególnych
    preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
    $strength += sizeof($specialchars[0]);

    //Pobiera liczbę  użytych znaków szczególnych
    $chars = str_split($password);
    $num_unique_chars = sizeof( array_unique($chars) );
    $strength += $num_unique_chars * 2;

    //Siła hasła to liczba z przedziału 1 - 10
    $strength = $strength > 99 ? 99 : $strength;
    $strength = floor($strength / 10 + 1);

    return $strength;
}
?>
