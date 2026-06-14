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

    private static final double MAX_LDM = 13.6;

    public KalkulatorService(TowarRepository towarRepository, PojazdRepository pojazdRepository) {
        this.towarRepository = towarRepository;
        this.pojazdRepository = pojazdRepository;
    }

    // SPEDYTOR - ADA
    public Map<String, Object> kalkulujZaladunek(Integer idTowaru, Integer idPojazdu, int iloscJednostek) {
        Map<String, Object> wynik = new HashMap<>();

        Towar towar = towarRepository.findById(idTowaru)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono towaru o ID: " + idTowaru));
        Pojazd pojazd = pojazdRepository.findById(idPojazdu)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono pojazdu o ID: " + idPojazdu));

        double wyliczoneLdm = towar.getLdm() * iloscJednostek;
        double sumarycznaWaga = towar.getWaga() * iloscJednostek;

        wynik.put("wyliczoneLdm", wyliczoneLdm);
        wynik.put("sumarycznaWaga", sumarycznaWaga);

        if (wyliczoneLdm > MAX_LDM) {
            wynik.put("statusMiejsca", "BLAD_BRAK_MIEJSCA");
            wynik.put("komunikatMiejsca", "Ładunek przekracza maksymalną długość naczepy (13.6m)!");
        } else {
            wynik.put("statusMiejsca", "OK");
            double procentZajetosci = (wyliczoneLdm / MAX_LDM) * 100;
            wynik.put("procentZajetosci", Math.round(procentZajetosci));
        }

        if (sumarycznaWaga > pojazd.getMaxLadownosc()) {
            wynik.put("statusWagi", "BLAD_PRZECIAZENIE");
            wynik.put("komunikatWagi", "Przekroczono maksymalną ładowność pojazdu o " + (sumarycznaWaga - pojazd.getMaxLadownosc()) + " kg!");
        } else {
            wynik.put("statusWagi", "OK");
        }

        return wynik;
    }
}