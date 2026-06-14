<?php
$mysqli = new mysqli("localhost", "root", "", "tms_logistyka");

if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą: " . $mysqli->connect_error);
}

$wynikKlienci = $mysqli->query("SELECT * FROM klient");
$klienci = [];

if ($wynikKlienci) {
    while ($row = $wynikKlienci->fetch_assoc()) {
        $pelnaNazwa = $row['imie'] . ' ' . $row['nazwisko'];
        $klienci[] = [
            'id' => $row['id'],
            'nazwa' => $pelnaNazwa,
            'nip' => $row['NIP']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS - Kreator Zleceń (Ewelina)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-kreator {
            padding: 30px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            border: none;
        }

        .card-header {
            background-color: #2c3e50;
            color: white;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            font-weight: bold;
        }

        .section-number {
            background: #34495e;
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold">
            <i class="bi bi-arrow-left-circle"></i> Powrót do Pulpitu
        </a>
        <span class="navbar-brand mb-0 h1 m-0"><i class="bi bi-file-earmark-plus"></i> System TMS - Kreator Handlowy</span>
        <div style="width: 145px;"></div> </div>
</nav>

    <div class="container-fluid container-kreator">

        <form id="zintegrowanyKreatorForm">
            <div class="row">

                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <span class="section-number">1</span> Okno 1: Kontrahent & Trasa
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Numer Zlecenia wewnętrzny:</label>
                                <input type="text" name="nrZlecenia" class="form-control" value="ZLEC/2026/<?php echo rand(100, 999); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Wybierz Klienta z bazy:</label>
                                <select name="kontrahent" id="selectKontrahent" class="form-select" onchange="uzupelnijNip()" required>
                                    <option value="" data-nip="">-- Wybierz klienta z listy --</option>
                                    <?php foreach ($klienci as $k): ?>
                                        <option value="<?php echo $k['nazwa']; ?>" data-nip="<?php echo $k['nip']; ?>">
                                            <?php echo $k['nazwa']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">NIP Kontrahenta:</label>
                                <input type="text" name="nip" id="inputNip" class="form-control bg-light" placeholder="Wybierz klienta, aby uzupełnić" readonly required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Miejsce Załadunku (Kraj/Miasto):</label>
                                <input type="text" name="miejsceZal" class="form-control" placeholder="np. PL - Warszawa" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Miejsce Rozładunku (Kraj/Miasto):</label>
                                <input type="text" name="miejsceRozl" class="form-control" placeholder="np. DE - Berlin" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <span class="section-number">2</span> Okno 2: Szczegóły Ładunku
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Opis / Nazwa towaru:</label>
                                <input type="text" name="nazwaTowaru" class="form-control" placeholder="np. Części samochodowe / AGD" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Masa całkowita ładunku (kg):</label>
                                <input type="number" name="waga" class="form-control" placeholder="Waga w kilogramach" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center">
                            <span class="section-number">3</span> Okno 3: Rozliczenie & Rentowność
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Przychód od Klienta (EUR/PLN):</label>
                                    <input type="number" id="finPrzychod" name="stawkaKlienta" class="form-control" placeholder="Stawka dla nas" oninput="obliczMarze()" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Koszt Przewoźnika (EUR/PLN):</label>
                                    <input type="number" id="finKoszt" name="kosztPrzewoznika" class="form-control" placeholder="Koszt zakupu auta" oninput="obliczMarze()" required>
                                </div>

                                <div class="p-3 bg-light rounded border mb-3">
                                    <div class="small text-muted mb-1">Wyliczona marża handlowa:</div>
                                    <h4 class="mb-0 text-success" id="txtMarza">0.00 PLN / EUR</h4>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold p-3">
                                Zapisz zintegrowane zlecenie w systemie
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function uzupelnijNip() {
            const select = document.getElementById('selectKontrahent');
            // Pobieramy ukryty parametr 'data-nip' z wybranej opcji listy
            const wybranyNip = select.options[select.selectedIndex].getAttribute('data-nip');

            document.getElementById('inputNip').value = wybranyNip;
        }

        function obliczMarze() {
            const przychod = parseFloat(document.getElementById('finPrzychod').value) || 0;
            const koszt = parseFloat(document.getElementById('finKoszt').value) || 0;
            const marza = przychod - koszt;

            const txtMarza = document.getElementById('txtMarza');
            txtMarza.innerText = marza.toFixed(2) + " PLN / EUR";

            if (marza < 0) {
                txtMarza.className = "mb-0 text-danger";
            } else {
                txtMarza.className = "mb-0 text-success";
            }
        }

        document.getElementById('zintegrowanyKreatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('http://localhost:8080/api/kalkulator/kreator-eweliny?' + new URLSearchParams(formData), {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.href = 'index.php'; 
                })
                .catch(error => alert("Błąd: " + error.message));
        });
    </script>

</body>

</html>
