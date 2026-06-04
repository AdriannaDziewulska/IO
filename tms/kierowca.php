<?php
error_reporting(0);
ini_set('display_errors', 0);

$mysqli = new mysqli("localhost", "root", "", "tms_logistyka");

// NOWOŚĆ: Pobieramy ID kierowcy przekazane ze strony głównej (domyślnie 1, jeśli wejdziemy bezpośrednio)
$idKierowcy = isset($_GET['id']) ? intval($_GET['id']) : 1;

$zlecenie = null;
$imieNazwiskoKierowcy = "Kierowca Testowy";

if (!$mysqli->connect_error) {
    // 1. Pobieramy imię wybranego kierowcy
    $wynikK = $mysqli->query("SELECT imie_nazwisko FROM kierowca WHERE id = $idKierowcy");
    if($wynikK && $rk = $wynikK->fetch_assoc()) {
        $imieNazwiskoKierowcy = $rk['imie_nazwisko'];
    }

    // 2. Pobieramy ostatnie zlecenie przypisane konkretnie do tego kierowcy!
    $wynik = $mysqli->query("SELECT * FROM zlecenia WHERE id_kierowcy = $idKierowcy ORDER BY id DESC LIMIT 1");
    if ($wynik) { $zlecenie = $wynik->fetch_assoc(); }
}

if (!$zlecenie) {
    // Dane demonstracyjne, jeśli kierowca nie ma jeszcze przypisanego zlecenia przez Adę
    $zlecenie = null;
}
$status = $zlecenie ? $zlecenie['status'] : 'Brak';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS Mobile - Terminal Kierowcy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .mobile-container { max-width: 450px; margin: 20px auto; padding: 15px; }
        .card { border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; border: none; }
        .card-header { background-color: #2c3e50; color: white; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; font-weight: bold; }
        .tacho-display { font-size: 2.2rem; font-family: 'Courier New', monospace; font-weight: bold; color: #2c3e50; }
        .route-box { background-color: #f8f9fa; border-left: 4px solid #2c3e50; padding: 12px; border-radius: 6px; }
        .btn-custom-gray { background-color: #6c757d; color: white; font-weight: 600; }
        .btn-custom-gray:hover { background-color: #5a6268; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold" style="font-size: 11px; padding: 2px 6px;">
            <i class="bi bi-box-arrow-left"></i> Wyjdź
        </a>
        <span class="navbar-brand mb-0 h1 m-0" style="font-size: 15px;"><i class="bi bi-person-circle"></i> TMS Mobile</span>
        <div style="width: 60px;"></div> </div>
</nav>

<div class="container mobile-container">

    <div class="card text-center p-3 mb-3">
        <div class="small text-muted mb-1 fw-bold text-uppercase" id="lblLicznik">Czas pozostały do przerwy:</div>
        <div class="tacho-display my-2" id="txtTimer">04:30:00</div>
        
        <div class="d-flex gap-2 justify-content-center mt-2">
            <button id="btnPrzerwa" class="btn btn-warning fw-bold px-4" onclick="togglePrzerwa()" disabled>Przerwa</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-center">Bieżące zadanie dla kierowcy</div>
        <div class="card-body">
            <div class="route-box mb-3">
                <h6 class="mb-1"><strong>Zlecenie:</strong> <?php echo $zlecenie['numer_zlecenia']; ?></h6>
                <h6 class="mb-1"><strong>Trasa:</strong> <?php echo $zlecenie['miejsce_zaladunku']; ?> -> <?php echo $zlecenie['miejsce_rozladunku']; ?></h6>
                <small class="text-danger d-block fw-bold mt-1">Uwagi: Załadunek rampa nr 4.</small>
            </div>
            
            <div id="akceptacjaSekcja" class="d-flex gap-2">
                <?php if($status == 'Zatwierdzone' || $status == 'Nowe'): ?>
                    <button id="btnZaakceptuj" class="btn btn-custom-gray w-50" onclick="akceptujZadanie(<?php echo $zlecenie['id']; ?>)">Zaakceptuj</button>
                    <button class="btn btn-custom-gray w-50" onclick="alert('Zlecenie odrzucone. Powiadomiono spedytora.')">Odrzuć</button>
                <?php else: ?>
                    <div class="alert alert-success text-center w-100 p-2 mb-0 small fw-bold">&#10004; ZLECENIE ZAAKCEPTOWANE</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card text-center">
        <div class="card-header">Zgłoś gotowość do wyjazdu</div>
        <div class="card-body py-3">
            <button id="btnStart" class="btn btn-custom-gray btn-lg w-75 fw-bold" 
                <?php echo ($status == 'Zaakceptowane' || $status == 'W realizacji') ? '' : 'disabled'; ?> 
                onclick="startTrasy(<?php echo $zlecenie['id']; ?>)">
                <?php echo ($status == 'W realizacji') ? 'W TRASIE...' : 'START'; ?>
            </button>
        </div>
    </div>

    <div class="card text-center">
        <div class="card-header bg-danger">Zgłoś incydent na trasie</div>
        <div class="card-body py-3">
            <button class="btn btn-danger btn-lg w-75 fw-bold" data-bs-toggle="modal" data-bs-target="#incydentModal">
                Zgłoś incydent / SOS
            </button>
        </div>
    </div>

</div>

<div class="modal fade" id="incydentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold">Zgłoszenie awarii / incydentu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <label class="form-label small fw-bold">Wybierz typ problemu:</label>
          <select id="incydentTyp" class="form-select mb-3">
              <option value="Wypadek">Wypadek</option>
              <option value="Awaria">Awaria</option>
              <option value="Korek">Korek</option>
          </select>
          
          <label class="form-label small fw-bold">Opis zdarzenia:</label>
          <textarea id="incydentOpis" class="form-control mb-3" rows="3" placeholder="Wpisz szczegóły sytuacji..."></textarea>
          
          <button class="btn btn-outline-secondary w-100 mb-3" onclick="alert('Kamera telefonu aktywna. Zdjęcie usterki zostało pomyślnie załączone.')">&#128247; Zrób i dodaj zdjęcie</button>
          <button class="btn btn-danger w-100 fw-bold" onclick="wyslijIncydent(<?php echo $zlecenie['id']; ?>)">Wyślij zgłoszenie do bazy</button>
      </div>
    </div>
  </div>
</div>

<script>
// PARAMETRY STARTOWE
const idK = "<?php echo $idKierowcy; ?>"; // Unikalne ID wybranego kierowcy z bazy
const PELNY_CZAS = 4.5 * 60 * 60; // 16200 sekund
let timerInterval = null;

// 1. INICJALIZACJA Z UNIKALNYM KLUCZEM DLA KAŻDEGO KIEROWCY
let totalSeconds = localStorage.getItem('tacho_seconds_' + idK) ? parseInt(localStorage.getItem('tacho_seconds_' + idK)) : PELNY_CZAS;
let isPaused = localStorage.getItem('tacho_paused_' + idK) === 'true';

let aktualnyStatusBazy = "<?php echo $status; ?>";

// Uruchomienie tacho na starcie w zależności od statusu
window.addEventListener('load', () => {
    aktualizujWyswietlacz(); // Pokazujemy zapamiętany czas od razu po wejściu
    
    if(aktualnyStatusBazy === 'Zaakceptowane' || aktualnyStatusBazy === 'W realizacji') {
        document.getElementById('btnPrzerwa').disabled = false;
        
        if(aktualnyStatusBazy === 'W realizacji') {
            // Przywracamy wygląd przycisku Przerwa z pamięci dedykowanej dla tego kierowcy
            if(isPaused) {
                ustawWygladPrzerwa();
            } else {
                ustawWygladJazda();
            }
            odpalLicznik();
        }
    }
});

function akceptujZadanie(id) {
    fetch('http://localhost:8080/api/kalkulator/akceptuj-zadanie?idZlecenia=' + id, { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        alert("Zlecenie przyjęte! Licznik gotowy.");
        document.getElementById('btnStart').disabled = false;
        document.getElementById('btnPrzerwa').disabled = false;
        document.getElementById('akceptacjaSekcja').innerHTML = `<div class="alert alert-success text-center w-100 p-2 mb-0 small fw-bold">&#10004; ZLECENIE ZAAKCEPTOWANE</div>`;
    });
}

function startTrasy(id) {
    fetch('http://localhost:8080/api/kalkulator/start-trasy?idZlecenia=' + id, { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        const btn = document.getElementById('btnStart');
        btn.innerText = "W TRASIE...";
        btn.className = "btn btn-primary btn-lg w-75 fw-bold";
        alert("Status: W realizacji. Szerokiej drogi!");
        
        isPaused = false;
        localStorage.setItem('tacho_paused_' + idK, 'false');
        odpalLicznik();
    });
}

function odpalLicznik() {
    if(timerInterval) clearInterval(timerInterval);
    
    timerInterval = setInterval(() => {
        if(!isPaused) {
            totalSeconds--;
            
            // ZAPISUJEMY KAŻDĄ SEKUNDĘ DLA KONKRETNEGO KIEROWCY
            localStorage.setItem('tacho_seconds_' + idK, totalSeconds);
            
            if(totalSeconds <= 0) {
                clearInterval(timerInterval);
                localStorage.removeItem('tacho_seconds_' + idK);
                alert("ALARM! Koniec czasu jazdy! Zjedź na parking!");
            }
            
            aktualizujWyswietlacz();
        }
    }, 1000);
}

function aktualizujWyswietlacz() {
    let hrs = Math.floor(totalSeconds / 3600);
    let mins = Math.floor((totalSeconds % 3600) / 60);
    let secs = totalSeconds % 60;
    
    let formattedTime = 
        (hrs < 10 ? "0" + hrs : hrs) + ":" + 
        (mins < 10 ? "0" + mins : mins) + ":" + 
        (secs < 10 ? "0" + secs : secs);
        
    const txtTimer = document.getElementById('txtTimer');
    txtTimer.innerText = formattedTime;
    
    if(totalSeconds < 900) { // Czerwony poniżej 15 minut
        txtTimer.style.color = '#e74c3c';
    } else {
        txtTimer.style.color = '#2c3e50';
    }
}

// FUNKCJA PRZERWY Z TRWAŁĄ I UNIKALNĄ PAMIĘCIĄ
function togglePrzerwa() {
    if(!isPaused) {
        // Start przerwy
        isPaused = true;
        localStorage.setItem('tacho_paused_' + idK, 'true');
        ustawWygladPrzerwa();
    } else {
        // Koniec przerwy -> Pełen reset czasu i czyszczenie pamięci kierowcy
        isPaused = false;
        localStorage.setItem('tacho_paused_' + idK, 'false');
        clearInterval(timerInterval);
        
        totalSeconds = PELNY_CZAS;
        localStorage.setItem('tacho_seconds_' + idK, totalSeconds); // Reset zapisu czasu
        
        aktualizujWyswietlacz();
        ustawWygladJazda();
        
        alert("Przerwa zakończona! Czas jazdy zresetowany do 4.5h.");
        odpalLicznik();
    }
}

function ustawWygladPrzerwa() {
    document.getElementById('btnPrzerwa').innerText = "Zakończ przerwę";
    document.getElementById('btnPrzerwa').className = "btn btn-success fw-bold px-4";
    document.getElementById('lblLicznik').innerText = "Trwa odpoczynek kierowcy...";
}

function ustawWygladJazda() {
    document.getElementById('btnPrzerwa').innerText = "Przerwa";
    document.getElementById('btnPrzerwa').className = "btn btn-warning fw-bold px-4";
    document.getElementById('lblLicznik').innerText = "Czas pozostały do przerwy:";
}

function wyslijIncydent(id) {
    const typ = document.getElementById('incydentTyp').value;
    const opis = document.getElementById('incydentOpis').value;
    
    fetch(`http://localhost:8080/api/kalkulator/incydent?idZlecenia=${id}&typ=${typ}&opis=${opis}&lat=52.2&lng=21.0`, { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        alert("System automatycznie pobrał aktualne współrzędne GPS telefonu.\n\n" + data.message);
        bootstrap.Modal.getInstance(document.getElementById('incydentModal')).hide();
    });
}
</script>

</body>
</html>