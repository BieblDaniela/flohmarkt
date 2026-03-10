<?php
session_start();
$message = "";

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}

if (isset($_POST['zurueck'])) {
    header ('Location: index.php');
    die();
}

require_once('db.php');
$stmt = 'SELECT pid, produkt, preis, beschreibung, kategorie, bild, erstellungs_datum FROM product WHERE kid_fk = :kid_fk ORDER BY erstellungs_datum DESC';
$result = $pdo->prepare($stmt);
$result->bindParam(':kid_fk', $_SESSION['user_id']);
$result->execute();

if (isset($_POST['bearbeiten'])) {
    $_SESSION['pid'] = $_POST['bearbeiten'];
    header('location: produkt_bearbeiten.php');
    die();
}

if (isset($_POST['verkaufen'])){
    $pid = $_POST['verkaufen'];
    $interessent = 'interessent_' . $pid;
    $kaeufer = $_POST["$interessent"];

    $sql = "UPDATE product SET gekauft_von = :gekauft_von WHERE pid = :pid";
    $stmt1 = $pdo->prepare($sql);
    $stmt1->execute([
        'gekauft_von' => $kaeufer,
        'pid' => $pid
    ]);
    }

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eigene Produkte</title>
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">

</head>
<body>
    <form action="" method="post">
        <div class="container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Produkt</th>
                    <th>Preis</th>
                    <th>Beschreibung</th>
                    <th>Kategorie</th>
                    <th>Bild</th>
                    <th>Erstellungsdatum</th>
                    <th>Interessenten</th>
                    <th>Aktionen</th>
                </tr>
                <?php while ($row = $result->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pid']); ?></td>
                        <td><?php echo htmlspecialchars($row['produkt']); ?></td>
                        <td><?php echo htmlspecialchars($row['preis']); ?></td>
                        <td><?php echo htmlspecialchars($row['beschreibung']); ?></td>
                        <td><?php echo htmlspecialchars($row['kategorie']); ?></td>
                        <td><?php echo "<img style='width: 20%; display: flex; justify-self: center'  src='" . htmlspecialchars($row['bild']) . "'>"; ?></td>
                        <td><?php echo htmlspecialchars($row['erstellungs_datum']); ?></td>
                        <td><?php 
                                $stmt_int = "SELECT interessenten.benutzer_id, konto.vorname, konto.nachname, konto.klasse  FROM interessenten INNER JOIN konto ON interessenten.benutzer_id = konto.kid WHERE interessenten.artikel_id = :artikel_id  ";
                                $result_int = $pdo->prepare($stmt_int);
                                $result_int->bindParam(':artikel_id', $row['pid']);
                                $result_int->execute();
                                $interessenten = $result_int->fetchAll();

                                if (count($interessenten) > 0): ?>
                                    <select name="interessent_<?php echo $row['pid']; ?>">
                                        <?php foreach ($interessenten as $i): ?>
                                            <option value="<?php echo htmlspecialchars($i['benutzer_id']); ?>"><?php echo htmlspecialchars($i['vorname']) .", ". htmlspecialchars($i['nachname']) ." ". htmlspecialchars($i['klasse']).". Klasse "; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <span>Noch keine Interessenten</span>
                                <?php endif; ?>
                        </td>
                        <td><button type="submit" name="verkaufen" value="<?php echo $row['pid']; ?>">Verkaufen</button></td>
                        <td><button type="submit" name="bearbeiten" value="<?php echo $row['pid']; ?>">Bearbeiten</button></td>
                    </tr>
                <?php endwhile;?>
            </table>

            <div class="button">
                <input type="submit" name="zurueck" id="zurueck" value="Zurück zum Shop">
            </div>
        </div>
    </form>
</body>
</html>