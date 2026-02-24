<?php
session_start();
$message = "";

require_once('db.php');
$stmt = 'SELECT pid,produkt, preis, beschreibung, kategorie, bild, erstellungs_datum FROM product ORDER BY erstellungs_datum DESC';
$result = $pdo->query($stmt);

if (isset($_POST['bestellen'])) {
    $stmt2 = "INSERT INTO interessenten (artikel_id,benutzer_id) VALUES (" . $_POST['bestellen'] . "," . $_SESSION['user_id'] . ")";
    $result2 = $pdo->prepare($stmt2);
    $result2->execute();

    $message = 'Bestellung erfolgreich';
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauptseite</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>

<body>

    <form action="" method="post">
        <?php echo $message; ?>
        <div class="container">
            <table>
                <tr>
                    <th>Produkt</th>
                    <th>Preis</th>
                    <th>Beschreibung</th>
                    <th>Kategorie</th>
                    <th>Bild</th>
                    <th>Erstellungsdatum</th>
                </tr>
                <?php while ($row = $result->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['produkt']); ?></td>
                        <td><?php echo htmlspecialchars($row['preis']); ?></td>
                        <td><?php echo htmlspecialchars($row['beschreibung']); ?></td>
                        <td><?php echo htmlspecialchars($row['kategorie']); ?></td>
                        <td><?php echo "<img style='width: 20%; display: flex; justify-self: center'  src='" . htmlspecialchars($row['bild']) . "'>"; ?></td>
                        <td><?php echo htmlspecialchars($row['erstellungs_datum']); ?></td>
                        <td><button type="submit" name="bestellen" value="<?php echo $row['pid'];?>">Bestellen</button></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <br>
            <a href="produkt_anlegen.php">Produkt anlegen</a>
            <br>
            <a href="eigene_produkte.php">Eigene Produkte ansehen</a>
            <br>
            <a href="logout.php">Logout</a>
        </div>
    </form>
</body>

</html>