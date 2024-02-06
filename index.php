<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitarbeitersuche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Mitarbeitersuche</h1>
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Mitarbeiter suchen" aria-label="Mitarbeiter suchen" id="searchInput">
        <button class="btn btn-outline-primary" type="button" id="searchButton"><i class='bx bx-search'></i> Suchen</button>
    </div>
    <div id="searchResults"></div> <!-- Hier werden die Suchergebnisse angezeigt -->
    <div id="loadingIndicator" class="text-center" style="display: none;"> <!-- Ladeanzeige, ausgeblendet zu Beginn -->
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Modal für Urlaubseintrag -->
<div class="modal fade" id="urlaubEintragenModal" tabindex="-1" aria-labelledby="urlaubEintragenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="urlaubEintragenModalLabel">Urlaub eintragen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4" id="employeeProfile">
                        <!-- Mitarbeiterdetails werden hier angezeigt -->
                    </div>
                    <!-- Formular zum Urlaubseintragen -->
                    <form id="vacationForm">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Startdatum</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Enddatum</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="genehmigungsstatus" class="form-label">Genehmigungsstatus</label>
                            <select class="form-select" id="genehmigungsstatus" name="genehmigungsstatus" required>
                                <option value="genehmigt">Genehmigt</option>
                                <option value="abgelehnt">Abgelehnt</option>
                            </select>
                        </div>
                        <input type="hidden" id="mitarbeiter_id" name="mitarbeiter_id"> <!-- Hier hinzugefügt -->
                        <button type="submit" class="btn btn-primary">Urlaub eintragen</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<!-- Tabelle für eingetragene Urlaube -->
<div class="container mt-5">
    <h2>Eingetragene Urlaube</h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Mitarbeiter</th>
                <th scope="col">Startdatum</th>
                <th scope="col">Enddatum</th>
                <th scope="col">Aktionen</th>
            </tr>
        </thead>
        <tbody id="vacationTable">
            <!-- Hier werden eingetragene Urlaube angezeigt -->
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<script>
    // DOM-Elemente zwischenspeichern
    const searchInput = document.getElementById('searchInput');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const employeeProfile = document.getElementById('employeeProfile');
    const genehmigungsstatusSelect = document.getElementById('genehmigungsstatus');
    const mitarbeiterIdInput = document.getElementById('mitarbeiter_id');
    const vacationForm = document.getElementById('vacationForm');

    // Funktion, um nach Mitarbeitern zu suchen
    function searchEmployees() {
        const searchValue = searchInput.value;

        // Zeige die Ladeanzeige an
        loadingIndicator.style.display = 'block';

        // URL-Parameter mit encodeURIComponent kodieren
        const requestBody = 'search=' + encodeURIComponent(searchValue);

        fetch('suche_mitarbeiter.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: requestBody,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Netzwerkantwort war nicht okay');
            }
            return response.json();
        })
        .then(data => {
            // Verstecke die Ladeanzeige, wenn die Suche abgeschlossen ist
            loadingIndicator.style.display = 'none';

            // Verarbeite die JSON-Daten und aktualisiere die Anzeige
            // ...
        })
        .catch(error => {
            // Verstecke die Ladeanzeige bei einem Fehler
            loadingIndicator.style.display = 'none';

            // Informiere den Benutzer über den Fehler
            console.error('Fehler beim Suchen von Mitarbeitern:', error.message);
            alert('Es ist ein Fehler beim Suchen von Mitarbeitern aufgetreten. Bitte versuchen Sie es später erneut.');
        });
    }

    // Function to open vacation entry modal
    function openVacationModal(employeeId) {
        fetch('mitarbeiter_details.php?id=' + employeeId)
            .then(response => response.json())
            .then(data => {
                // Display employee details in employeeProfile
                employeeProfile.innerHTML = `
                    <p><strong>Mitarbeiter ID:</strong> ${data.Mitarbeiter_ID}</p>
                    <p><strong>Vorname:</strong> ${data.Vorname}</p>
                    <p><strong>Nachname:</strong> ${data.Nachname}</p>
                    <p><strong>Funktion:</strong> ${data.Role}</p>
                    <p><strong>Store ID:</strong> ${data.Store_ID}</p>
                `;

                // Display Genehmigungsstatus in vacationForm
                genehmigungsstatusSelect.innerHTML = `
                    <option value="genehmigt">Genehmigt</option>
                    <option value="abgelehnt">Abgelehnt</option>
                `;
                mitarbeiterIdInput.value = employeeId;

                var modal = new bootstrap.Modal(document.getElementById('urlaubEintragenModal'));
                modal.show();
            })
            .catch(error => console.error('Fehler beim Laden der Mitarbeiterdaten:', error));
    }

    // Submit vacation form
    vacationForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission

        // Überprüfen, ob alle erforderlichen Felder ausgefüllt wurden
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const genehmigungsstatus = document.getElementById('genehmigungsstatus').value;
        const mitarbeiter_id = document.getElementById('mitarbeiter_id').value;

        if (startDate && endDate && genehmigungsstatus && mitarbeiter_id) {
            // Alle erforderlichen Felder sind ausgefüllt, Formular absenden
            const formData = new FormData(this);
            fetch('urlaubsantrag_hinzufuegen.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Show success message
                searchEmployees(); // Reload search results after submitting vacation
                var modal = new bootstrap.Modal(document.getElementById('urlaubEintragenModal'));
                modal.hide(); // Hide modal after submission
            })
            .catch(error => console.error('Fehler beim Eintragen des Urlaubs:', error));
        } else {
            // Ein oder mehrere erforderliche Felder fehlen, Fehlermeldung anzeigen
            alert('Bitte füllen Sie alle erforderlichen Felder aus.');
        }
    });

    // Suche starten, wenn Enter-Taste gedrückt wird
    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            searchEmployees();
        }
    });

    // Suche starten, wenn auf den Suchen-Button geklickt wird
    document.getElementById('searchButton').addEventListener('click', searchEmployees);
</script>
</body>
</html>
