<?php
//Session starten
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['pass']; //keien Funktionen wie htmlspecialchar oder trim anwenden, um das Passwort nicht zu verändern

    //1. User aus der DB holen
    //WICHTIG: Wir holen nur die notwendigen Daten (Spalten) aus der Tabelle!
    require_once('db.php');
    $stmt = $pdo->prepare("SELECT kid,email,passwort,vorname, nachname, klasse FROM konto WHERE email = :email");

    $stmt->execute(['email' => $email]);
    //Da des SELECT Daten zurücklifert, müssen wir diese Daten in einem Array entgegennehmen
    $user = $stmt->fetch(); //Der gewünschte Datensatz des 'eingelogten' Users wird zurückgegeben

    //2. Passwort überprüfen
    if ($user && password_verify($password, $user['passwort'])){
        //User darf sich einloggen - Passwort und Email stimmen

        //SICHERHEITS-UPDATE 
        //Prüfen, ob der Hash veraltet ist (wenn ja, erneuern und in der DB speichern)
        if (password_needs_rehash($user['passwort'], PASSWORD_DEFAULT)){
            
            //neuer Hash generieren
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            //neuen Hash in der DB speichern
            $updateStmt = $pdo->prepare("UPDATE konto SET passwort = :passwort WHERE kid = :kid");

            $updateStmt->execute([
                'passwort' => $newHash,
                'kid' => $user['kid']
            ]);
        }
        //Session setzen (Schutz vor Session Fixation)
        session_regenerate_id(true);

        //Die Session mit Daten befüllen
        $_SESSION['user_id'] = $user['kid'];
        $_SESSION['user_vorname'] = $user['vorname'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nachname'] = $user['nachname'];
        $_SESSION['user_klasse'] = $user['klasse'];

        header("location: index.php");
   }else{
        $message = "Benutzername oder Passwort falsch.";
   }

}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>

<body>

    <div class="container">

        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>

        <h1>Login</h1>
        <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>

        <form action="" method="post">
            <label for="email">E-Mail:</label>
            <input type="email" name="email" id="email" required>

            <label for="pass">Password:</label>
            <input type="password" name="pass" id="pass" required>

            <div class="buttons">
                <input type="button" name="abbrechen" id="abbrechen" value="Abbrechen">
                <input type="button" name="registrieren" id="registrieren" value="Registrieren">
                <input type="submit" name="anmelden" id="anmelden" value="Anmelden">
            </div>
        </form>

    </div>

</body>

</html>