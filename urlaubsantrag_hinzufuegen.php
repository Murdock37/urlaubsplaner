<?php

// urlaubsantrag_hinzufuegen.php

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Überprüfen, ob die Schlüssel im $_POST-Array vorhanden sind
    if(isset($_POST['mitarbeiter_id'], $_POST['startdatum'], $_POST['enddatum'], $_POST['genehmigungsstatus'])) {
        $mitarbeiter_id = $_POST['mitarbeiter_id'];
        $startdatum = $_POST['startdatum'];
        $enddatum = $_POST['enddatum'];
        $genehmigungsstatus = $_POST['genehmigungsstatus'];

        try {
            // Starte die Transaktion
            $pdo->beginTransaction();

            // Überprüfen, ob der Mitarbeiter bereits für diesen Zeitraum Urlaub hat
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Urlaubsantraege WHERE Mitarbeiter_ID = :mitarbeiter_id AND ((Startdatum <= :startdatum AND Enddatum >= :startdatum) OR (Startdatum <= :enddatum AND Enddatum >= :enddatum))");
            $stmt->bindParam(':mitarbeiter_id', $mitarbeiter_id);
            $stmt->bindParam(':startdatum', $startdatum);
            $stmt->bindParam(':enddatum', $enddatum);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if($count > 0) {
                echo "Der Mitarbeiter hat bereits für diesen Zeitraum Urlaub beantragt.";
            } else {
                // Validierung der Eingabedaten
                if($startdatum >= $enddatum) {
                    echo "Das Startdatum muss vor dem Enddatum liegen.";
                } elseif (!in_array($genehmigungsstatus, ['ausstehend', 'genehmigt', 'abgelehnt'])) {
                    echo "Ungültiger Genehmigungsstatus.";
                } else {
                    // Anpassung des INSERT-Statements, um den neuen Primärschlüssel "Antrags_ID" zu verwenden
                    $stmt = $pdo->prepare("INSERT INTO Urlaubsantraege (Mitarbeiter_ID, Startdatum, Enddatum, Genehmigungsstatus) VALUES (:mitarbeiter_id, :startdatum, :enddatum, :genehmigungsstatus)");
                    $stmt->bindParam(':mitarbeiter_id', $mitarbeiter_id);
                    $stmt->bindParam(':startdatum', $startdatum);
                    $stmt->bindParam(':enddatum', $enddatum);
                    $stmt->bindParam(':genehmigungsstatus', $genehmigungsstatus);

                    $stmt->execute(); // Führe das INSERT-Statement aus

                    // Committet die Transaktion
                    $pdo->commit();

                    echo "Urlaubsantrag erfolgreich eingereicht!";
                }
            }
        } catch (PDOException $e) {
            // Bei einem Fehler Rollback der Transaktion
            $pdo->rollBack();
            die("Fehler beim Einreichen des Urlaubsantrags: " . $e->getMessage());
        }
    } else {
        echo "Ein oder mehrere Formularfelder wurden nicht gesendet.";
    }
} else {
    echo "Ungültige Anforderungsmethode.";
}
?>
