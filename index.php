<?php
session_start();

require_once('db.php');
$stmt = 'SELECT product.pid, product.kid_fk,product.produkt, product.preis, product.beschreibung, product.kategorie, product.bild, product.erstellungs_datum, konto.email FROM product INNER JOIN konto ON product.kid_fk = konto.kid ORDER BY product.erstellungs_datum DESC';
$result = $pdo->query($stmt);

if (isset($_POST['bestellen'])) {
    $stmt2 = "INSERT INTO interessenten (artikel_id,benutzer_id) VALUES (:pid, :kid )";
    $result2 = $pdo->prepare($stmt2);
    $result2->execute([
        'pid' => $_POST['bestellen'],
        'kid' => $_SESSION['user_id']
    ]);
}

if (isset($_POST['email'])){
    $_SESSION['v_email'] = $_POST['email'];
    header('location: emailsenden.php');
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
        <div class="container">
            <table>
                <tr>
                    <th>Produkt</th>
                    <th>Preis</th>
                    <th>Beschreibung</th>
                    <th>Kategorie</th>
                    <th>Bild</th>
                    <th>Erstellungsdatum</th>
                    <th>Bestellen</th>
                    <th>E-Mail</th>
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
                        <td><button type="submit" name="email" value = "<?php echo $row['email'];?>">E-Mail senden</button></td>
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