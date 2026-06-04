package com.example.tms.service;

import com.example.tms.model.Pojazd;
import com.example.tms.model.Towar;
import com.example.tms.repository.PojazdRepository;
import com.example.tms.repository.TowarRepository;
import org.springframework.stereotype.Service;

import java.util.HashMap;
import java.util.Map;

@Service
public class KalkulatorService {

    private final TowarRepository towarRepository;
    private final PojazdRepository pojazdRepository;

    // Stała określająca maksymalną długość ładownej naczepy standardowej (13.6m)
    private static final double MAX_LDM = 13.6;

    public KalkulatorService(TowarRepository towarRepository, PojazdRepository pojazdRepository) {
        this.towarRepository = towarRepository;
        this.pojazdRepository = pojazdRepository;
    }

    /**
     * Główna metoda kalkulatora LDM realizująca wymaganie RF 1.3
     */
    public Map<String, Object> kalkulujZaladunek(Integer idTowaru, Integer idPojazdu, int iloscJednostek) {
        Map<String, Object> wynik = new HashMap<>();

        // 1. Pobranie danych o towarze i pojeździe z bazy
        Towar towar = towarRepository.findById(idTowaru)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono towaru o ID: " + idTowaru));
        Pojazd pojazd = pojazdRepository.findById(idPojazdu)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono pojazdu o ID: " + idPojazdu));

        // 2. Obliczenie sumarycznych parametrów ładunku
        double wyliczoneLdm = towar.getLdm() * iloscJednostek;
        double sumarycznaWaga = towar.getWaga() * iloscJednostek;

        wynik.put("wyliczoneLdm", wyliczoneLdm);
        wynik.put("sumarycznaWaga", sumarycznaWaga);

        // 3. Walidacja dostępności miejsca (Czy LDM > 13.6m?) - Romb decyzyjny z diagramu aktywności
        if (wyliczoneLdm > MAX_LDM) {
            wynik.put("statusMiejsca", "BLAD_BRAK_MIEJSCA");
            wynik.put("komunikatMiejsca", "Ładunek przekracza maksymalną długość naczepy (13.6m)!");
        } else {
            wynik.put("statusMiejsca", "OK");
            // Wyliczenie zajętości procentowej do paska ProgressBar w interfejsie PHP
            double procentZajetosci = (wyliczoneLdm / MAX_LDM) * 100;
            wynik.put("procentZajetosci", Math.round(procentZajetosci));
        }

        // 4. Walidacja wagowa (Czy masa towaru > ładowność pojazdu?)
        if (sumarycznaWaga > pojazd.getMaxLadownosc()) {
            wynik.put("statusWagi", "BLAD_PRZECIAZENIE");
            wynik.put("komunikatWagi", "Przekroczono maksymalną ładowność pojazdu o " + (sumarycznaWaga - pojazd.getMaxLadownosc()) + " kg!");
        } else {
            wynik.put("statusWagi", "OK");
        }

        return wynik;
    }
}