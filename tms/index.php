<?php
$mysqli = new mysqli("localhost", "root", "", "tms_logistyka");
$kierowcy = [];

if (!$mysqli->connect_error) {
    $wynik = $mysqli->query("SELECT * FROM kierowca");
    if ($wynik) {
        while($row = $wynik->fetch_assoc()) {
            $kierowcy[] = $row;
        }
    }
}

if (empty($kierowcy)) {
    $kierowcy = [
        ['id' => 1, 'imie_nazwisko' => 'Jan Kowalski', 'status' => 'Wolny'],
        ['id' => 2, 'imie_nazwisko' => 'Piotr Nowak', 'status' => 'Wolny'],
        ['id' => 3, 'imie_nazwisko' => 'Mariusz Lewandowski', 'status' => 'Wolny']
    ];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System TMS - Główny Panel Zarządzania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .welcome-header { background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); color: white; padding: 60px 0; text-align: center; border-bottom: 5px solid #34495e; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .menu-card { transition: all 0.3s ease; border: none; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); min-height: 250px; cursor: pointer; }
        .menu-card:hover { transform: translateY(-10px); box-shadow: 0 12px 20px rgba(0,0,0,0.15); }
        .icon-box { font-size: 3.5rem; margin-bottom: 15px; }
        .bg-ewelina { color: #2ecc71; }
        .bg-spedytor { color: #3498db; }
        .bg-kierowca { color: #e67e22; }
    </style>
</head>
<body>

<div class="welcome-header mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold"><i class="bi bi-shield-check"></i> Zintegrowany System TMS</h1>
        <p class="lead mt-2" style="color: #bdc3c7;">Platforma Logistyczno-Spedycyjna | Projekt Zespołowy</p>
    </div>
</div>

<div class="container">
    <div class="row g-4 justify-content-center">
        
        <div class="col-lg-4 col-md-6" onclick="window.location.href='magazyn.php'">
            <div class="card menu-card text-center p-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div class="icon-box bg-ewelina"><i class="bi bi-file-earmark-plus-fill"></i></div>
                    <h4 class="card-title fw-bold text-dark">Kreator Handlowy</h4>
                    <p class="card-text text-muted small">Okna 1, 2, 3: Wprowadzanie kontrahentów z bazy, specyfikacji towaru oraz kalkulacja marży.</p>
                    <span class="badge bg-success px-3 py-2">Dostęp Ewelina</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6" onclick="window.location.href='spedytor.php'">
            <div class="card menu-card text-center p-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div class="icon-box bg-spedytor"><i class="bi bi-truck-flatbed"></i></div>
                    <h4 class="card-title fw-bold text-dark">Panel Spedytora</h4>
                    <p class="card-text text-muted small">Kalkulator LDM, dobór floty transportowej, wirtualna naczepa oraz generowanie specyfikacji PDF.</p>
                    <span class="badge bg-primary px-3 py-2">Dostęp Ada</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6" data-bs-toggle="modal" data-bs-target="#wybierzKierowceModal">
            <div class="card menu-card text-center p-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div class="icon-box bg-kierowca"><i class="bi bi-phone-vibrate-fill"></i></div>
                    <h4 class="card-title fw-bold text-dark">Panel Kierowcy</h4>
                    <p class="card-text text-muted small">Widok mobilny dla kierowcy: akceptacja i odrzucanie zleceń, statusy tachografu, start trasy oraz zgłoszenia SOS.</p>
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Dostęp Natalia</span>
                </div>
            </div>
        </div>

    </div>
    
    <footer class="mt-5 text-center text-muted small py-4 border-top">
        <p>&copy; 2026 System Zarządzania Transportem (TMS). Wszelkie prawa zastrzeżone.</p>
    </footer>
</div>

<div class="modal fade" id="wybierzKierowceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content text-center">
      <div class="modal-header bg-dark text-white justify-content-center">
        <h5 class="modal-title fw-bold"><i class="bi bi-person-bounding-box"></i> Wybierz kierowcę</h5>
      </div>
      <div class="modal-body p-4">
          <p class="text-muted small mb-3">Wybierz profil pracownika, aby zalogować się do jego mobilnego terminalu pokładowego.</p>
          
          <div class="d-grid gap-2">
              <?php foreach($kierowcy as $k): ?>
                  <button class="btn btn-outline-dark fw-bold py-2" onclick="otworzPanelKierowcy(<?php echo $k['id']; ?>)">
                      <i class="bi bi-person"></i> <?php echo $k['imie_nazwisko']; ?>
                  </button>
              <?php endforeach; ?>
          </div>
      </div>
      <div class="modal-footer border-0 justify-content-center pt-0">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Anuluj</button>
      </div>
    </div>
  </div>
</div>

<script>
function otworzPanelKierowcy(idKierowcy) {
    window.location.href = 'kierowca.php?id=' + idKierowcy;
}
</script>

</body>
</html>
