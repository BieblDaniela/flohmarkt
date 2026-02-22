<?php
session_start();
$message ="";
$targetDir = "uploads/";

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}

if (isset($_POST['zurueck'])) {
    header ('Location: index.php');
    die();
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_FILES['bild'])){
    $error = $_FILES['bild']['error'];
    $produkt = trim($_POST['produkt']);
    $preis = trim($_POST['preis']);
    $kategorie = trim($_POST['kategorie']);
    $beschreibung = trim($_POST['beschreibung']);

    if ($error === UPLOAD_ERR_NO_FILE) {
        echo "Keine neue Datei hoachgeladen.";
    } elseif ($error !== UPLOAD_ERR_OK) {
        echo "Es ist ein Fehler beim Upload aufgetreten. Fehlercode: " . $error;
    } else {
        $file = $_FILES['bild'];

        if ($file['size'] > 2000000) {
            die("Die hochgeladene Datei ist zu groß.");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowTypes = ['image/jpeg', 'image/png'];

        if (!in_array($mimeType, $allowTypes)) {
            die("Ungültiger Dateityp.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid('upload_', true) . "." . $extension;
        $destination = $targetDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            echo "Die Datei-Upload erfolgreich ausgeführt.";
        } else {
            echo "Fehler beim Verschieben der Datei.";
        }

        require_once('db.php');
        try {
            $sql = "INSERT INTO product(produkt, preis, beschreibung, kategorie, bild, kid_fk) VALUES (:produkt, :preis,:beschreibung,:kategorie,:bild,:kid_fk)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'produkt' => $produkt,
                'preis' => $preis,
                'beschreibung' => $beschreibung,
                'kategorie' => $kategorie,
                'bild' => $destination,
                'kid_fk' => $_SESSION['user_id']
            ]);
            
        } catch (PDOException $e) {
            $e->getMessage();
            echo "Fehler beim Speichern der Daten in die Datenbank!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkt anlegen</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>
<body class="produkt-anlegen">
    <div class="container">

        <h1>Produkt anlegen</h1>
        <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <label name="produkt">Produktname:</label>
            <input type="text" name="produkt" id="produkt">

            <div class="form-row">
                <div>
                    <label name="preis">Preis:</label>
                    <input type="number" name="preis" id="preis">
                </div>
                <div>
                    <label name="kategorie">Kategorie:</label>
                    <select name="kategorie">
                        <option name="kategorie" id="school" value="school" selected>Schulartikel</option>
                        <option name="kategorie" id="electronics" value="electronics">Elektonik</option>
                        <option name="kategorie" id="furniture" value="furniture">Möbel</option>
                        <option name="kategorie" id="freetime" value="freetime">Freizeit</option>
                    </select>
                </div>
            </div>

            <label name="bild">Produktbild:</label>
            <input type="file" name="bild" id="bild"> 

            <label name="beschreibung">Beschreibung:</label>
            <textarea name="beschreibung" id="beschreibung"></textarea>

            <div class="button">
                <input type="submit" name="zurueck" id="zurueck" value="Zurück zum Shop">
                <input type="submit" name="anlegen" id="anlegen" value="Produkt anlegen">
            </div>

        </form>
    </div>
</body>
</html>