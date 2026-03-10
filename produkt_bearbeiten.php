<?php
session_start();
$message = "";
$targetDir = "uploads/";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

if (isset($_POST['zurueck'])) {
    header('Location: index.php');
    die();
}

$pid = $_SESSION['pid'];

if (!$pid) {
    die("Keine Produkt-ID gefunden.");
}

require_once('db.php');
$stmt = 'SELECT produkt, preis, beschreibung, kategorie, bild, erstellungs_datum FROM product WHERE pid = :pid ORDER BY erstellungs_datum DESC';
$result = $pdo->prepare($stmt);
$result->bindParam(':pid', $pid, PDO::PARAM_STR);
$result->execute();
$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $produkt = $row['produkt'];
    $preis = $row['preis'];
    $beschreibung = $row['beschreibung'];
    $kategorie = $row['kategorie'];
    $bild = $row['bild'];
    $erstellungs_datum = $row['erstellungs_datum'];
}


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = $_FILES['bild']['error'];
    $produkt = trim($_POST['produkt']);
    $preis = trim($_POST['preis']);
    $kategorie = trim($_POST['kategorie']);
    $beschreibung = trim($_POST['beschreibung']);

    $destination = $bild;

    if (isset($_FILES['bild'])&& $_FILES['bild']['error'] === UPLOAD_ERR_OK) {
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
    }       

        try {
            $sql = "UPDATE product SET produkt=:produkt,preis=:preis,beschreibung=:beschreibung,kategorie=:kategorie,bild=:bild WHERE pid = :pid";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'produkt' => $produkt,
                'preis' => $preis,
                'beschreibung' => $beschreibung,
                'kategorie' => $kategorie,
                'bild' => $destination,
                'pid' => $pid
            ]);

            echo "Produkt aktualisiert";
            $bild = $destination;
        } catch (PDOException $e) {
            $e->getMessage();
            echo "Fehler beim Speichern der Daten in die Datenbank!";
        }
    }

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkt bearbeiten</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>

<body class="produkt-anlegen">
    <div class="container">

        <h1>Produkt bearbeiten</h1>
        <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="produkt">Produktname:</label>
            <input type="text" name="produkt" id="produkt" value="<?php echo htmlspecialchars($produkt ?? '') ?>">

            <div class="form-row">
                <div>
                    <label for="preis">Preis:</label>
                    <input type="number" name="preis" id="preis" value="<?php echo htmlspecialchars($preis ?? '') ?>">
                </div>
                <div>
                    <label for="kategorie">Kategorie:</label>
                    <select name="kategorie">
                        <option name="kategorie" id="school" value="school" <?php if ($kategorie == 'school') echo 'selected'; ?>>Schulartikel</option>
                        <option name="kategorie" id="electronics" value="electronics" <?php if ($kategorie == 'electronics') echo 'selected'; ?>>Elektonik</option>
                        <option name="kategorie" id="furniture" value="furniture" <?php if ($kategorie == 'furniture') echo 'selected'; ?>>Möbel</option>
                        <option name="kategorie" id="freetime" value="freetime" <?php if ($kategorie == 'freetime') echo 'selected'; ?>>Freizeit</option>
                    </select>
                </div>
            </div>

            <label>Aktuelles Bild:</label>
            <div style="margin-bottom: 10px;">
                <?php if (!empty($bild)): ?>
                    <img src="<?php echo htmlspecialchars($bild); ?>" alt="Produktbild" style="max-width: 150px; display: block; margin-bottom: 5px;">
                    <small>Dateipfad: <?php echo htmlspecialchars($bild); ?></small>
                <?php else: ?>
                    <p>Kein Bild vorhanden.</p>
                <?php endif; ?>
            </div>

            <label for="bild">Neues Bild hochladen (optional):</label>
            <input type="file" name="bild" id="bild">

            <label for="beschreibung">Beschreibung:</label>
            <textarea name="beschreibung" id="beschreibung"><?php echo htmlspecialchars($beschreibung ?? '') ?></textarea>

            <div class="button">
                <input type="submit" name="zurueck" id="zurueck" value="Zurück zum Shop">
                <input type="submit" name="anlegen" id="anlegen" value="Produkt speichern">
            </div>

        </form>
    </div>
</body>

</html>