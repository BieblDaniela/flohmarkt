<?php
$message = '';

//prüfen, ob Button geklickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $vorname = trim($_POST['vn']);
    $nachname = trim($_POST['nn']);
    $email = trim($_POST['email']);
    $password = $_POST['pass'];
    $klasse = $_POST['kl'];
    $password2 = $_POST['best'];

    //serverseitige Validierung
    if (!empty($email) && !empty($password) && !empty($vorname) && !empty($nachname) && !empty($klasse) && !empty($password2)) {
        if ($password == $password2) {
            //1. Passwort hashen
            //PASSWORD_DEFAULT nutzt den aktuell stärksten Algorithmus der PHP-Version
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            //2. In der DB speichern
            require_once('db.php');

            try {
                $sql = "INSERT INTO konto(vorname, nachname, email, klasse, passwort) VALUES (:vorname,:nachname,:email,:klasse,:passwort)";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute(['vorname' => $vorname, 'nachname' => $nachname, 'email' => $email, 'klasse' => $klasse, 'passwort' => $passwordHash])) {
                    header("location: login.php");
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { //Code für Dublicated Entry
                    $message = "Email ist bereits im System!";
                } else {
                    $message = "Es ist ein Fehler beim Registrieren aufgetreten. Bitte versuche es erneut.";
                }
            }
        }else {
            $message = "Keine Übereinstimmung bei dem Passwort. Bitte überprüfen.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>

<body>

    <div class="container">

        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>

        <h1>Registrierung</h1>
        <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>

        <form action="" method="post">
            <label for="vn">Vorname:</label>
            <input type="text" name="vn" id="vn" required>

            <label for="nn">Nachname:</label>
            <input type="text" name="nn" id="nn" required>

            <label for="kl">Klasse:</label>
            <select name="kl">
                <option name="kl" id="1" value="1" selected>1.Klasse</option>
                <option name="kl" id="2" value="2">2.Klasse</option>
                <option name="kl" id="3" value="3">3.Klasse</option>
                <option name="kl" id="4" value="4">4.Klasse</option>
                <option name="kl" id="5" value="5">5.Klasse</option>
            </select>

            <label for="email">E-Mail:</label>
            <input type="email" name="email" id="email" required>

            <label for="pass">Password:</label>
            <input type="password" name="pass" id="pass" required>

            <label for="best">Password bestätigen:</label>
            <input type="password" name="best" id="best" required>

            <div class="buttons">
                <input type="submit" name="abb" id="abb" value="Abbrechen">
                <input type="submit" name="reg" id="reg" value="Registrierung">
            </div>
        </form>

    </div>
</body>

</html>