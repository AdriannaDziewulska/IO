package com.example.tms.model;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "Dokumenty")
public class Dokument {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column(name = "id_zlecenia", nullable = false)
    private Integer idZlecenia;

    @Column(name = "typ_dokumentu", length = 50)
    private String typDokumentu;

    @Column(name = "czy_zweryfikowany")
    private Boolean czyZweryfikowany;

    @Column(name = "data_przeslania")
    private LocalDateTime dataPrzeslania;

    // --- GETTERY I SETTERY ---
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public Integer getIdZlecenia() { return idZlecenia; }
    public void setIdZlecenia(Integer idZlecenia) { this.idZlecenia = idZlecenia; }

    public String getTypDokumentu() { return typDokumentu; }
    public void setTypDokumentu(String typDokumentu) { this.typDokumentu = typDokumentu; }

    public Boolean getCzyZweryfikowany() { return czyZweryfikowany; }
    public void setCzyZweryfikowany(Boolean czyZweryfikowany) { this.czyZweryfikowany = czyZweryfikowany; }

    public LocalDateTime getDataPrzeslania() { return dataPrzeslania; }
    public void setDataPrzeslania(LocalDateTime dataPrzeslania) { this.dataPrzeslania = dataPrzeslania; }
}