<?php

    //suche_mitarbeiter.php

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];

    try {
        // Aktiviere PDO-Exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vorbereitung der Abfrage
        $stmt = $pdo->prepare("SELECT * FROM Mitarbeiter WHERE Mitarbeiter_ID LIKE :search OR Vorname LIKE :search OR Nachname LIKE :search");
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);

        // Ausführung der Abfrage
        $stmt->execute();

        // Abrufen der Ergebnisse
        $mitarbeiter = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Überprüfen, ob Mitarbeiter gefunden wurden
        if(empty($mitarbeiter)) {
            echo "Keine Mitarbeiter gefunden für die Suchanfrage: $search";
        } else {
            echo json_encode($mitarbeiter);
        }
    } catch (PDOException $e) {
        // Fehler beim Datenbankzugriff abfangen und behandeln
        die("Fehler beim Suchen von Mitarbeitern: " . $e->getMessage());
    } catch (Exception $e) {
        // Andere allgemeine Fehler abfangen und behandeln
        die("Ein Fehler ist aufgetreten: " . $e->getMessage());
    }
} else {
    die("Ungültige Anforderungsmethode oder Suchparameter fehlen.");
}
?>
