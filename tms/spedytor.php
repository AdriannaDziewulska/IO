<?php
// Połączenie z Twoją bazą danych w XAMPP
$mysqli = new mysqli("localhost", "root", "", "tms_logistyka");

if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą: " . $mysqli->connect_error);
}

// Pobieramy wszystkie dostępne pojazdy
$wynikPojazdy = $mysqli->query("SELECT * FROM Pojazd WHERE status_dostepnosci = 1");
$pojazdy = [];
while ($row = $wynikPojazdy->fetch_assoc()) {
    $pojazdy[] = $row;
}

// Dopisz to pod zapytaniem o pojazdy
$wynikKierowcy = $mysqli->query("SELECT * FROM kierowca");
$kierowcy = [];
while ($row = $wynikKierowcy->fetch_assoc()) {
    $kierowcy[] = $row;
}
?>


<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS - Dashboard Operacyjny Spedytora</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            padding: 30px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #2c3e50;
            color: white;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        .naczepa-box {
            border: 3px solid #34495e;
            background-color: #ecf0f1;
            min-height: 120px;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 8px;
            padding: 12px;
        }

        .paleta-wizualna {
            background-color: #f39c12;
            color: white;
            border: 2px solid #d35400;
            width: calc(16.66% - 8px);
            padding: 12px 5px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            box-sizing: border-box;
        }

        .oversize-row {
            background-color: #fde8e8 !important;
            color: #9b1c1c;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold">
            <i class="bi bi-arrow-left-circle"></i> Powrót do Pulpitu
        </a>
        <span class="navbar-brand mb-0 h1 m-0"><i class="bi bi-truck"></i> System TMS - Panel Spedytora</span>
        <div style="width: 145px;"></div> </div>
</nav>

    <div class="container-fluid dashboard-container">
        <div class="row">

            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">1. Ładunki i Zamówienia</h5>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importModal">Importuj Zlecenie</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Numer Zlecenia</th>
                                        <th>Trasa (Od -> Do)</th>
                                        <th>Masa</th>
                                        <th>Typ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $wynikZlecenia = $mysqli->query("SELECT * FROM zlecenia");
                                    while ($z = $wynikZlecenia->fetch_assoc()):
                                        $miejsceZal = $z['miejsce_zaladunku'] ? $z['miejsce_zaladunku'] : 'Brak';
                                        $miejsceRozl = $z['miejsce_rozladunku'] ? $z['miejsce_rozladunku'] : 'Brak';
                                        $masaText = $z['masa_towaru'] ? number_format($z['masa_towaru'], 0, ',', ' ') . ' kg' : '0 kg';
                                    ?>
                                        <tr>
                                            <td><?php echo $z['id']; ?></td>
                                            <td><strong><?php echo $z['numer_zlecenia']; ?></strong></td>
                                            <td><?php echo $miejsceZal . ' -> ' . $miejsceRozl; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo $masaText; ?></span></td>
                                            <td>
                                                <button class="btn btn-xs btn-primary py-0 px-1"
                                                    onclick="wybierzZlecenie(<?php echo $z['id']; ?>, '<?php echo $z['numer_zlecenia']; ?>', <?php echo $z['masa_towaru']; ?>)">
                                                    Planuj
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">2. Dobór Taboru i Floty</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Wymagany typ zabudowy:</label>
                            <select class="form-select" id="typZabudowySelect">
                                <option value="Firanka">Firanka (Standard)</option>
                                <option value="Chlodnia">Chłodnia</option>
                                <option value="Mega">Mega</option>
                            </select>
                        </div>
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nr Rej.</th>
                                    <th>Typ</th>
                                    <th>Ładowność</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pojazdy as $p): ?>
                                    <tr>
                                        <td><?php echo $p['nr_rejestracyjny']; ?></td>
                                        <td><?php echo $p['typ_zabudowy']; ?></td>
                                        <td><?php echo number_format($p['max_ladownosc'], 0, ',', ' '); ?> kg</td>
                                        <td>
                                            <button class="btn btn-xs btn-outline-primary py-0 px-1"
                                                onclick="wybierzPojazd(<?php echo $p['id']; ?>, '<?php echo $p['nr_rejestracyjny']; ?> (<?php echo $p['typ_zabudowy']; ?>)')">
                                                Wybierz
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">3. Kreator Wirtualnej Naczepy & Obliczenia LDM</h5>
                    </div>
                    <div class="card-body">

                        <form id="kalkulatorForm" class="row g-3 mb-4">
                            <input type="hidden" id="selectedZlecenieId" name="idZleceniaActual" value="">
                            <input type="hidden" id="selectedZlecenieMasa" value="0">
                            <div class="col-md-4">
                                <label class="form-label">Wybierz ładunek:</label>
                                <select name="idTowaru" class="form-select" required>
                                    <option value="1">Palety Euro AGD (0.4 LDM / 400kg)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pojazd docelowy:</label>
                                <select name="idPojazdu" id="selectIdPojazdu" class="form-select" required>
                                    <?php foreach ($pojazdy as $p): ?>
                                        <option value="<?php echo $p['id']; ?>">
                                            <?php echo $p['nr_rejestracyjny']; ?> (<?php echo $p['typ_zabudowy']; ?> - max <?php echo $p['max_ladownosc']; ?>kg)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Przypisz Kierowcę:</label>
                                <select name="idKierowcy" id="selectIdKierowcy" class="form-select" required>
                                    <?php foreach ($kierowcy as $k): ?>
                                        <option value="<?php echo $k['id']; ?>"><?php echo $k['imie_nazwisko']; ?> (<?php echo $k['status']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Ilość palet (szt.):</label>
                                <input type="number" name="iloscJednostek" id="inputIloscPalet" class="form-control" value="5" min="1" required>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary px-4">Oblicz LDM</button>
                            </div>
                        </form>

                        <div id="wynikiKalkulatora" class="mb-4 d-none">
                            <h6>Wyniki weryfikacji systemowej:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">Zajętość naczepy: <span id="resLdm" class="fw-bold">0</span> / 13.6 m bieżących</p>
                                    <div class="progress" style="height: 25px;">
                                        <div id="resProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;">0%</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">Masa całkowita towaru: <span id="resWaga" class="fw-bold">0</span> kg</p>
                                    <div id="alertWaga" class="alert p-2 small mb-0"></div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4">Wizualizacja ułożenia przestrzennego:</h6>
                        <div class="naczepa-box mb-3">
                            <div class="paleta-wizualna">Paleta #1</div>
                            <div class="paleta-wizualna">Paleta #2</div>
                            <div class="paleta-wizualna">Paleta #3</div>
                            <div class="paleta-wizualna">Paleta #4</div>
                            <div class="paleta-wizualna">Paleta #5</div>
                        </div>

                        <div class="row small text-muted text-center mb-4">
                            <div class="col-6">Nacisk Oś 1 (Napędowa): <input type="text" class="form-control form-control-sm d-inline-block w-50" value="8 500 kg" readonly></div>
                            <div class="col-6">Nacisk Oś 2 (Wózek): <input type="text" class="form-control form-control-sm d-inline-block w-50" value="11 200 kg" readonly></div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <button id="btnPdf" class="btn btn-secondary">Podgląd dokumentu planu (PDF)</button>
                            <button id="btnZatwierdz" class="btn btn-danger">Zatwierdź i Wyślij do Magazynu</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import nowego zlecenia frachtowego</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="importForm">
                        <div class="mb-3">
                            <label class="form-label">Numer zlecenia (np. CRM/123):</label>
                            <input type="text" name="nrZlecenia" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Miejsce załadunku:</label>
                            <input type="text" name="miejsceZal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Miejsce rozładunku:</label>
                            <input type="text" name="miejsceRozl" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Masa towaru (kg):</label>
                            <input type="number" name="masa" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Zapisz zlecenie w bazie</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Podgląd Dokumentu Spedycyjnego</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="pdfModalBody" style="background-color: #fff; padding: 30px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" onclick="window.print()">Drukuj dokument</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funkcja wywoływana po kliknięciu "Planuj" w lewej tabeli
        function wybierzZlecenie(id, numer, masa) {
            document.getElementById('selectedZlecenieId').value = id;
            document.getElementById('selectedZlecenieMasa').value = masa;

            // Zmieniamy tekst w select ładunku dla widoczności
            const selectTowar = document.querySelector('select[name="idTowaru"]');
            selectTowar.innerHTML = `<option value="1">Ładunek ze zlecenia: ${numer} (${masa} kg)</option>`;

            // AUTOMATYZACJA: Liczymy propozycję palet (Masa / 500 kg)
            let proponowanePalety = Math.ceil(masa / 500);

            if (proponowanePalety <= 0) {
                proponowanePalety = 1;
            }

            const polePalet = document.getElementById('inputIloscPalet');
            polePalet.value = proponowanePalety;

            alert(`Wybrano zlecenie: ${numer}.\nSystem sugeruje przygotowanie ${proponowanePalety} palet (w przeliczniku ~500kg/paleta).\n\nMożesz zmienić tę liczbę przed kliknięciem "Oblicz LDM".`);
        }

        // Funkcja łącząca wybór taboru z tabeli floty
        function wybierzPojazd(id, opis) {
            document.getElementById('selectIdPojazdu').value = id;
            alert("Ustawiono tabor: " + opis);
        }

        // OBSŁUGA KALKULATORA LDM
        document.getElementById('kalkulatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const iloscPalet = parseInt(formData.get('iloscJednostek'));

            const idZleceniaCheck = document.getElementById('selectedZlecenieId').value;
            if (!idZleceniaCheck) {
                alert("Błąd: Najpierw wybierz aktywne zlecenie klikając 'Planuj' w tabeli po lewej stronie!");
                return;
            }

            const masaZlecenia = parseFloat(document.getElementById('selectedZlecenieMasa').value) || 0;
            const selectPojazd = document.getElementById('selectIdPojazdu');
            const wybranyPojazdTekst = selectPojazd.options[selectPojazd.selectedIndex].text;

            const dopuszczalnaLadownosc = parseInt(wybranyPojazdTekst.match(/max (\d+)kg/)[1]);

            fetch('http://localhost:8080/api/kalkulator/oblicz?' + new URLSearchParams(formData), {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('wynikiKalkulatora').classList.remove('d-none');
                    document.getElementById('resLdm').innerText = Number(data.wyliczoneLdm).toFixed(2) + ' m';

                    const progressBar = document.getElementById('resProgressBar');
                    if (data.statusMiejsca === "BLAD_BRAK_MIEJSCA") {
                        progressBar.style.width = '100%';
                        progressBar.className = "progress-bar bg-danger";
                        progressBar.innerText = "BRAK MIEJSCA (>13.6m)";
                    } else {
                        progressBar.style.width = data.procentZajetosci + '%';
                        progressBar.className = "progress-bar bg-success";
                        progressBar.innerText = data.procentZajetosci + '%';
                    }

                    document.getElementById('resWaga').innerText = data.sumarycznaWaga;
                    const alertWaga = document.getElementById('alertWaga');

                    if (data.sumarycznaWaga > dopuszczalnaLadownosc) {
                        alertWaga.className = "alert alert-danger p-2 small mb-0";
                        alertWaga.innerText = `BLAD! Przekroczono ładowność wybranego auta o ${data.sumarycznaWaga - dopuszczalnaLadownosc} kg! Zmień tabor na większy.`;
                    } else {
                        alertWaga.className = "alert alert-success p-2 small mb-0";
                        alertWaga.innerText = "Masa ładunku w normie dla tego pojazdu. Status: OK.";
                    }

                    const naczepaBox = document.querySelector('.naczepa-box');
                    naczepaBox.innerHTML = '';
                    if (data.statusMiejsca !== "BLAD_BRAK_MIEJSCA") {
                        for (let i = 1; i <= iloscPalet; i++) {
                            const nowaPaleta = document.createElement('div');
                            nowaPaleta.className = 'paleta-wizualna';
                            nowaPaleta.innerText = 'Paleta #' + i;
                            naczepaBox.appendChild(nowaPaleta);
                        }
                    } else {
                        naczepaBox.innerHTML = '<div class="text-danger fw-bold w-100 text-center">Naczepa przeładowana gabarytowo!</div>';
                    }
                });
        });

        // POPRAWIONA OBSŁUGA ZATWIERDZANIA - ZAPOBIEGANIE WYSYŁANIU NULL
        document.getElementById('btnZatwierdz').addEventListener('click', function() {
            const idZlecenia = document.getElementById('selectedZlecenieId').value;
            const idPojazdu = document.getElementById('selectIdPojazdu').value;
            const idKierowcy = document.getElementById('selectIdKierowcy').value;

            // Walidacja: Zapobiegamy zatwierdzeniu pustego planu
            if (!idZlecenia) {
                alert("Błąd operacyjny: Nie można zatwierdzić planu! Najpierw wybierz zlecenie z listy (kliknij przycisk 'Planuj' w tabeli po lewej stronie).");
                return;
            }

            if (confirm(`Czy chcesz zatwierdzić ten plan załadunku?\n\n- ID Zlecenia: ${idZlecenia}\n- ID Pojazdu: ${idPojazdu}\n- ID Kierowcy: ${idKierowcy}`)) {
                fetch(`http://localhost:8080/api/kalkulator/zatwierdz?idZlecenia=${idZlecenia}&idPojazdu=${idPojazdu}&idKierowcy=${idKierowcy}`, {
                        method: 'POST'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Błąd serwera Javy. Status: " + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        location.reload(); // Odświeżamy stronę, aby zaktualizować status zlecenia w bazie i zwolnić formularz
                    })
                    .catch(error => {
                        alert("Wystąpił problem z integracją API: " + error.message);
                    });
            }
        });

        // IMPORT NOWEGO ZLECENIA DO BAZY
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('http://localhost:8080/api/kalkulator/importuj?' + new URLSearchParams(formData), {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert("Pomyślnie zaimportowano zlecenie o numerze: " + data.numerZlecenia);
                    location.reload();
                });
        });

        // DYNAMICZNY DOKUMENT
        document.getElementById('btnPdf').addEventListener('click', function() {
            const ldmText = document.getElementById('resLdm').innerText;
            const wagaText = document.getElementById('resWaga').innerText;
            const selectPojazd = document.getElementById('selectIdPojazdu');
            const wybranyTaborOpis = selectPojazd.options[selectPojazd.selectedIndex].text;
            const ukladdNaczepyHtml = document.querySelector('.naczepa-box').innerHTML;

            if (ldmText === '0' || ldmText === '0 m' || ldmText === '0.00 m') {
                alert("Najpierw dokonaj obliczeń!");
                return;
            }

            const trescDokumentu = `
        <div style="border-bottom: 3px solid #2c3e50; padding-bottom: 20px; margin-bottom: 30px;">
            <h2>SPECYFIKACJA PLANU ZAŁADUNKU DLA MAGAZYNU</h2>
        </div>
        <p><strong>Status dokumentu:</strong> ZATWIERDZONY DO ZAŁADUNKU</p>
        <table class="table table-bordered mt-4">
            <tbody>
                <tr><td>Zajętość przestrzeni liniowej (LDM)</td><td><strong>${ldmText}</strong></td></tr>
                <tr><td>Masa całkowita towaru</td><td><strong>${wagaText} kg</strong></td></tr>
                <tr><td>Przypisany tabor transportowy</td><td><strong>${wybranyTaborOpis}</strong></td></tr>
            </tbody>
        </table>
        <h5 class="mt-4">Schemat rozmieszczenia:</h5>
        <div class="naczepa-box mb-3" style="border: 3px dashed #34495e; background-color: #f8f9fa;">
            ${ukladdNaczepyHtml}
        </div>
    `;
            document.getElementById('pdfModalBody').innerHTML = trescDokumentu;
            const myModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            myModal.show();
        });
    </script>
</body>

</html>