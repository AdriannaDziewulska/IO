package com.example.tms.model;

import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.LocalDate;

@Entity
@Table(name = "Zlecenia")
public class Zlecenie {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column(name = "id_klienta")
    private Integer idKlienta;

    @Column(name = "numer_zlecenia", nullable = false, length = 50)
    private String numerZlecenia;

    @Column(name = "data_zaladunku", nullable = false)
    private LocalDate dataZaladunku;

    @Column(nullable = false, length = 20)
    private String status;

    @Column(name = "miejsce_zaladunku", length = 255)
    private String miejsceZaladunku;

    @Column(name = "miejsce_rozladunku", length = 255)
    private String miejsceRozladunku;

    @Column(name = "masa_towaru")
    private BigDecimal masaTowaru;

    @Column(name = "typ_towaru", length = 50)
    private String typTowaru;

    @Column(name = "powod_anulowania")
    private String powodAnulowania;

    @Column(name = "stawka_frachtu")
    private BigDecimal stawkaFrachtu;

    @Column(length = 10)
    private String waluta;

    // --- TUTAJ OD NAS DODANA NOWA ZMIENNA DLA KIEROWCY ---
    @Column(name = "id_kierowcy")
    private Integer idKierowcy;

    // --- GETTERY I SETTERY ---
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public Integer getIdKlienta() { return idKlienta; }
    public void setIdKlienta(Integer idKlienta) { this.idKlienta = idKlienta; }

    public String getNumerZlecenia() { return numerZlecenia; }
    public void setNumerZlecenia(String numerZlecenia) { this.numerZlecenia = numerZlecenia; }

    public LocalDate getDataZaladunku() { return dataZaladunku; }
    public void setDataZaladunku(LocalDate dataZaladunku) { this.dataZaladunku = dataZaladunku; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getMiejsceZaladunku() { return miejsceZaladunku; }
    public void setMiejsceZaladunku(String miejsceZaladunku) { this.miejsceZaladunku = miejsceZaladunku; }

    public String getMiejsceRozladunku() { return miejsceRozladunku; }
    public void setMiejsceRozladunku(String miejsceRozladunku) { this.miejsceRozladunku = miejsceRozladunku; }

    public BigDecimal getMasaTowaru() { return masaTowaru; }
    public void setMasaTowaru(BigDecimal masaTowaru) { this.masaTowaru = masaTowaru; }

    public String getTypTowaru() { return typTowaru; }
    public void setTypTowaru(String typTowaru) { this.typTowaru = typTowaru; }

    public String getPowodAnulowania() { return powodAnulowania; }
    public void setPowodAnulowania(String powodAnulowania) { this.powodAnulowania = powodAnulowania; }

    public BigDecimal getStawkaFrachtu() { return stawkaFrachtu; }
    public void setStawkaFrachtu(BigDecimal stawkaFrachtu) { this.stawkaFrachtu = stawkaFrachtu; }

    public String getWaluta() { return waluta; }
    public void setWaluta(String waluta) { this.waluta = waluta; }

    // --- TUTAJ OD NAS DODANY GETTER I SETTER DLA KIEROWCY ---
    public Integer getIdKierowcy() {
        return idKierowcy;
    }

    public void setIdKierowcy(Integer idKierowcy) {
        this.idKierowcy = idKierowcy;
    }
}