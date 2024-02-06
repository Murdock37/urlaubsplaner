<?php

    //mitarbeiter_details.php

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM Mitarbeiter WHERE Mitarbeiter_ID = :id");
    $stmt->bindParam(':id', $id);
    
    try {
        $stmt->execute();
        $mitarbeiter = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($mitarbeiter);
    } catch (PDOException $e) {
        die("Fehler beim Laden der Mitarbeiterdaten: " . $e->getMessage());
    }
}
?>
