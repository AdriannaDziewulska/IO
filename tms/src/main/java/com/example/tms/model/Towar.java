package com.example.tms.model;

import jakarta.persistence.*;

@Entity
@Table(name = "Towary")
public class Towar {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column(nullable = false, length = 100)
    private String nazwa;

    @Column(nullable = false)
    private Float waga;

    @Column(nullable = false)
    private Float ldm;

    // --- GETTERY I SETTERY ---
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public String getNazwa() { return nazwa; }
    public void setNazwa(String nazwa) { this.nazwa = nazwa; }

    public Float getWaga() { return waga; }
    public void setWaga(Float waga) { this.waga = waga; }

    public Float getLdm() { return ldm; }
    public void setLdm(Float ldm) { this.ldm = ldm; }
}