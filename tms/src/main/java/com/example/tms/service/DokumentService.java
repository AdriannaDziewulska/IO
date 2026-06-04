package com.example.tms.service;

import com.example.tms.model.Dokument;
import com.example.tms.model.Zlecenie;
import com.example.tms.repository.DokumentRepository;
import com.example.tms.repository.ZlecenieRepository;
import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.LocalDateTime;

@Service
public class DokumentService {

    private final DokumentRepository dokumentRepository;
    private final ZlecenieRepository zlecenieRepository;

    // Folder na serwerze, gdzie fizycznie będą zapisywane zdjęcia CMR przesyłane przez kierowcę
    private static final String UPLOAD_DIR = "uploads/cmr/";

    public DokumentService(DokumentRepository dokumentRepository, ZlecenieRepository zlecenieRepository) {
        this.dokumentRepository = dokumentRepository;
        this.zlecenieRepository = zlecenieRepository;
    }

    /**
     * Metoda realizująca proces Natalii: Odbiór pliku CMR od kierowcy i zmiana statusu
     */
    public Dokument zapiszCmrIAktualizujStatus(Integer idZlecenia, MultipartFile plikCmr) throws IOException {
        // 1. Sprawdzenie, czy zlecenie w ogóle istnieje w bazie
        Zlecenie zlecenie = zlecenieRepository.findById(idZlecenia)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono zlecenia o ID: " + idZlecenia));

        // 2. Obsługa fizycznego zapisu pliku na dysku serwera
        if (plikCmr.isEmpty()) {
            throw new IllegalArgumentException("Przesłany plik jest pusty!");
        }

        // Tworzenie katalogu uploads/cmr/ jeśli jeszcze nie istnieje
        Path katalogZapisu = Paths.get(UPLOAD_DIR);
        if (!Files.exists(katalogZapisu)) {
            Files.createDirectories(katalogZapisu);
        }

        // Unikalna nazwa pliku (np. cmr_1_zdjecie.jpg), żeby kierowcy nie nadpisywali sobie plików
        String nazwaPliku = "cmr_" + idZlecenia + "_" + plikCmr.getOriginalFilename();
        Path sciezkaPliku = katalogZapisu.resolve(nazwaPliku);

        // Zapisanie bajtów obrazu na dysku
        Files.write(sciezkaPliku, plikCmr.getBytes());

        // 3. Zapis informacji o dokumencie do bazy danych (Tabela Dokumenty)
        Dokument nowyDokument = new Dokument();
        nowyDokument.setIdZlecenia(idZlecenia);
        nowyDokument.setTypDokumentu("CMR");
        nowyDokument.setCzyZweryfikowany(false); // Spedytor zweryfikuje go później w panelu
        nowyDokument.setDataPrzeslania(LocalDateTime.now());

        Dokument zapisanyDokument = dokumentRepository.save(nowyDokument);

        // 4. Automatyczna zmiana statusu transportu na "In transit" - kluczowy krok z procesu Natalii
        zlecenie.setStatus("In transit");
        zlecenieRepository.save(zlecenie);

        return zapisanyDokument;
    }
}