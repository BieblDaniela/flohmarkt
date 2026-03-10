<?php

//Prüfung  auf verbotene Begriffe
function enthaeltVerboteneWoerter($text){
    //1. Liste an verbotenen Wörtern
    $blacklist = ['waffe', 'droge', 'porno', 'hausaufgabe'];

    //2. Den regEx dynamisch aufbauen:
    // /(waffen|droge|porno|hausaufgabe)/iu
    // i steht für "case-insensitive"   u steht für Umlaute und Sonsderzeichen (utf-8)

    $pattern = '/(' . implode('|', $blacklist) . ')/iu';

    //3. Prüfung: Gibt es einen Treffen?
    if (preg_match($pattern, $text)){
        //Treffer - Text enthält ein verbotenes Wort
        return true;
    }
    return false;
}

//Funktion für ein sicheres Passwort
//1 Groß-, 1 Kleinbuchstabe, 1 Ziffer, 1 Sonderzeichen und min. 8 Zeichen
function istPasswortSicher ($passwort){
    //regEx
    $regEx = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    return preg_match($regEx, $passwort);
}
?>