package com.example.tms.controller;

import com.example.tms.model.Dokument;
import com.example.tms.service.DokumentService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.util.Map;

@RestController
@RequestMapping("/api/dokumenty")
@CrossOrigin(origins = "*")
public class DokumentController {

    private final DokumentService dokumentService;

    public DokumentController(DokumentService dokumentService) {
        this.dokumentService = dokumentService;
    }

    /**
     * Endpoint pod adresem: POST http://localhost:8080/api/dokumenty/upload-cmr
     */
    @PostMapping("/upload-cmr")
    public ResponseEntity<?> uploadCmr(
            @RequestParam("idZlecenia") Integer idZlecenia,
            @RequestParam("plik") MultipartFile plik) {

        try {
            Dokument zapisanyDokument = dokumentService.zapiszCmrIAktualizujStatus(idZlecenia, plik);
            return ResponseEntity.ok(Map.of(
                    "status", "SUCCESS",
                    "message", "Dokument CMR przesłany pomyślnie. Status zlecenia zmieniony na 'In transit'.",
                    "dokumentId", zapisanyDokument.getId()
            ));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (IOException e) {
            return ResponseEntity.internalServerError().body(Map.of("error", "Błąd zapisu pliku na serwerze: " + e.getMessage()));
        }
    }
}