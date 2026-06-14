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

    private static final String UPLOAD_DIR = "uploads/cmr/";

    public DokumentService(DokumentRepository dokumentRepository, ZlecenieRepository zlecenieRepository) {
        this.dokumentRepository = dokumentRepository;
        this.zlecenieRepository = zlecenieRepository;
    }

    // KIEROWCA - NATALIA
    public Dokument zapiszCmrIAktualizujStatus(Integer idZlecenia, MultipartFile plikCmr) throws IOException {
        Zlecenie zlecenie = zlecenieRepository.findById(idZlecenia)
                .orElseThrow(() -> new IllegalArgumentException("Nie znaleziono zlecenia o ID: " + idZlecenia));

        if (plikCmr.isEmpty()) {
            throw new IllegalArgumentException("Przesłany plik jest pusty!");
        }

        Path katalogZapisu = Paths.get(UPLOAD_DIR);
        if (!Files.exists(katalogZapisu)) {
            Files.createDirectories(katalogZapisu);
        }

        String nazwaPliku = "cmr_" + idZlecenia + "_" + plikCmr.getOriginalFilename();
        Path sciezkaPliku = katalogZapisu.resolve(nazwaPliku);

        Files.write(sciezkaPliku, plikCmr.getBytes());

        Dokument nowyDokument = new Dokument();
        nowyDokument.setIdZlecenia(idZlecenia);
        nowyDokument.setTypDokumentu("CMR");
        nowyDokument.setCzyZweryfikowany(false);
        nowyDokument.setDataPrzeslania(LocalDateTime.now());

        Dokument zapisanyDokument = dokumentRepository.save(nowyDokument);

        zlecenie.setStatus("In transit");
        zlecenieRepository.save(zlecenie);

        return zapisanyDokument;
    }
}