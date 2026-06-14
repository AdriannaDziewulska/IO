package com.example.tms.controller;

import com.example.tms.model.Zlecenie;
import com.example.tms.repository.PojazdRepository;
import com.example.tms.repository.ZlecenieRepository;
import com.example.tms.service.KalkulatorService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.Map;

@RestController
@RequestMapping("/api/kalkulator")
@CrossOrigin(origins = "*")
public class KalkulatorController {

    private final KalkulatorService kalkulatorService;
    private final ZlecenieRepository zlecenieRepository;
    private final PojazdRepository pojazdRepository;

    public KalkulatorController(KalkulatorService kalkulatorService, ZlecenieRepository zlecenieRepository, PojazdRepository pojazdRepository) {
        this.kalkulatorService = kalkulatorService;
        this.zlecenieRepository = zlecenieRepository;
        this.pojazdRepository = pojazdRepository;
    }

    // SPEDYTOR - ADA
    @PostMapping("/oblicz")
    public ResponseEntity<Map<String, Object>> obliczZaladunek(
            @RequestParam Integer idTowaru,
            @RequestParam Integer idPojazdu,
            @RequestParam int iloscJednostek) {
        try {
            Map<String, Object> wynik = kalkulatorService.kalkulujZaladunek(idTowaru, idPojazdu, iloscJednostek);
            return ResponseEntity.ok(wynik);
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        }
    }

    // SPEDYTOR - ADA
    @PostMapping("/zatwierdz")
    public ResponseEntity<?> zatwierdzZlecenie(
            @RequestParam Integer idZlecenia,
            @RequestParam Integer idPojazdu,
            @RequestParam Integer idKierowcy) {
        return zlecenieRepository.findById(idZlecenia).map(zlecenie -> {
            zlecenie.setStatus("Zatwierdzone");
            zlecenie.setIdKierowcy(idKierowcy);

            zlecenieRepository.save(zlecenie);

            pojazdRepository.findById(idPojazdu).ifPresent(p -> {
                p.setIdZlecenia(idZlecenia);
                p.setStatusDostepnosci(false);
                pojazdRepository.save(p);
            });

            return ResponseEntity.ok(Map.of("status", "SUCCESS", "message", "Zlecenie #" + zlecenie.getNumerZlecenia() + " zostało zatwierdzone i przypisane do kierowcy!"));
        }).orElse(ResponseEntity.badRequest().body(Map.of("error", "Nie znaleziono zlecenia o ID: " + idZlecenia)));
    }

    // SPEDYTOR - ADA
    @PostMapping("/importuj")
    public ResponseEntity<?> importujZlecenie(
            @RequestParam String nrZlecenia,
            @RequestParam String miejsceZal,
            @RequestParam String miejsceRozl,
            @RequestParam double masa) {

        Zlecenie nowe = new Zlecenie();
        nowe.setNumerZlecenia(nrZlecenia);
        nowe.setMiejsceZaladunku(miejsceZal);
        nowe.setMiejsceRozladunku(miejsceRozl);
        nowe.setMasaTowaru(BigDecimal.valueOf(masa));
        nowe.setDataZaladunku(LocalDate.now());
        nowe.setStatus("Nowe");
        nowe.setTypTowaru("Standard");

        Zlecenie zapisane = zlecenieRepository.save(nowe);
        return ResponseEntity.ok(zapisane);
    }

    // KIEROWCA - NATALIA
    @PostMapping("/akceptuj-zadanie")
    public ResponseEntity<?> akceptujZadanie(@RequestParam Integer idZlecenia) {
        return zlecenieRepository.findById(idZlecenia).map(z -> {
            z.setStatus("Zaakceptowane");
            zlecenieRepository.save(z);
            return ResponseEntity.ok(Map.of("status", "SUCCESS", "message", "Zadanie przyjęte. Gotowość odblokowana."));
        }).orElse(ResponseEntity.badRequest().body(Map.of("error", "Nie znaleziono zlecenia")));
    }

    // KIEROWCA - NATALIA
    @PostMapping("/odrzuc-zadanie")
    public ResponseEntity<?> odrzucZadanie(@RequestParam Integer idZlecenia, @RequestParam String powod) {
        return zlecenieRepository.findById(idZlecenia).map(z -> {
            z.setStatus("Odrzucone");
            zlecenieRepository.save(z);
            return ResponseEntity.ok(Map.of("status", "SUCCESS", "message", "Zlecenie odrzucone. Powód: " + powod));
        }).orElse(ResponseEntity.badRequest().body(Map.of("error", "Nie znaleziono zlecenia")));
    }

    // KIEROWCA - NATALIA
    @PostMapping("/start-trasy")
    public ResponseEntity<?> startTrasy(@RequestParam Integer idZlecenia) {
        return zlecenieRepository.findById(idZlecenia).map(z -> {
            z.setStatus("W realizacji");
            zlecenieRepository.save(z);
            return ResponseEntity.ok(Map.of("status", "SUCCESS", "message", "Sygnał wysłany. Szerokiej drogi!"));
        }).orElse(ResponseEntity.badRequest().body(Map.of("error", "Nie znaleziono zlecenia")));
    }

    // KIEROWCA - NATALIA
    @PostMapping("/incydent")
    public ResponseEntity<?> zglosIncydent(
            @RequestParam Integer idZlecenia,
            @RequestParam String typ,
            @RequestParam String opis,
            @RequestParam double lat,
            @RequestParam double lng) {

        System.out.println("ALERT SOS! Zlecenie: " + idZlecenia + " | Typ: " + typ + " | Opis: " + opis + " | GPS: " + lat + "," + lng);

        return ResponseEntity.ok(Map.of(
                "status", "PROBLEM",
                "message", "Zgłoszenie SOS zostało wysłane do bazy danych. Spedytor otrzymał alert o problemie."
        ));
    }

    // ZLECENIE - EWELINA
    @PostMapping("/kreator-eweliny")
    public ResponseEntity<?> kreatorEweliny(
            @RequestParam String nrZlecenia,
            @RequestParam String kontrahent,
            @RequestParam String nip,
            @RequestParam String miejsceZal,
            @RequestParam String miejsceRozl,
            @RequestParam String nazwaTowaru,
            @RequestParam double waga,
            @RequestParam double stawkaKlienta,
            @RequestParam double kosztPrzewoznika) {

        try {
            Zlecenie nowe = new Zlecenie();
            nowe.setNumerZlecenia(nrZlecenia);
            nowe.setMiejsceZaladunku(miejsceZal);
            nowe.setMiejsceRozladunku(miejsceRozl);
            nowe.setMasaTowaru(BigDecimal.valueOf(waga));
            nowe.setDataZaladunku(LocalDate.now());
            nowe.setStatus("Nowe");
            nowe.setTypTowaru(nazwaTowaru);

            zlecenieRepository.save(nowe);

            System.out.println("Utworzono kompletne zlecenie od Eweliny: " + nrZlecenia + " dla " + kontrahent + " (Waga: " + waga + " kg)");

            return ResponseEntity.ok(Map.of(
                    "status", "SUCCESS",
                    "message", "Zlecenie " + nrZlecenia + " zostało pomyślnie utworzone i zapisane w bazie danych!"
            ));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        }
    }
}