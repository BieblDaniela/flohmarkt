<?php
session_start();

    $empfaenger = $_SESSION['v_email'];
    $sender = $_SESSION['user_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $betreff = $_POST['betreff'];
    $inhalt = $_POST['inhalt'];

    mail($empfaenger, $betreff, $inhalt, "From: $sender");
}


?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailsenden</title>
</head>

<body>
    <h1>Email versenden</h1>
    <p>Erstellen Sie hier die E-Mail mit Ihren Fragen an den Verkäufer.</p>
    <p><?php echo $empfaenger; ?></p>
    <br>

    <form action="" method="post">
        <label for="betreff">Betreff:</label>
        <input type="text" name="betreff" id="betreff" required>
        <br><br>

        <label for="inhalt">E-Mail-Inhalt:</label>
        <br>
        <textarea name="inhalt" id="inhalt" cols="30" rows="10" required></textarea>
        <br><br>

        <input type="submit" name="submit" id="submit" value="E-Mail versenden">

    </form>

</body>

</html>