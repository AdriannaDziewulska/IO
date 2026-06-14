package com.example.tms.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "Pojazd")
public class Pojazd {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column(name = "id_zlecenia")
    private Integer idZlecenia;

    @Column(name = "nr_rejestracyjny", nullable = false, length = 50)
    private String nrRejestracyjny;

    @Column(name = "typ_zabudowy", nullable = false, length = 50)
    private String typZabudowy;

    @Column(name = "max_ladownosc", nullable = false)
    private Integer maxLadownosc;

    @Column(name = "status_dostepnosci", nullable = false)
    private Boolean statusDostepnosci;

    @Column(name = "ostatni_przeglad", nullable = false)
    private LocalDate ostatniPrzeglad;

    // --- GETTERY I SETTERY ---
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public Integer getIdZlecenia() { return idZlecenia; }
    public void setIdZlecenia(Integer idZlecenia) { this.idZlecenia = idZlecenia; }

    public String getNrRejestracyjny() { return nrRejestracyjny; }
    public void setNrRejestracyjny(String nrRejestracyjny) { this.nrRejestracyjny = nrRejestracyjny; }

    public String getTypZabudowy() { return typZabudowy; }
    public void setTypZabudowy(String typZabudowy) { this.typZabudowy = typZabudowy; }

    public Integer getMaxLadownosc() { return maxLadownosc; }
    public void setMaxLadownosc(Integer maxLadownosc) { this.maxLadownosc = maxLadownosc; }

    public Boolean getStatusDostepnosci() { return statusDostepnosci; }
    public void setStatusDostepnosci(Boolean statusDostepnosci) { this.statusDostepnosci = statusDostepnosci; }

    public LocalDate getOstatniPrzeglad() { return ostatniPrzeglad; }
    public void setOstatniPrzeglad(LocalDate ostatniPrzeglad) { this.ostatniPrzeglad = ostatniPrzeglad; }
}