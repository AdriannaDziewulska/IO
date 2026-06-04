package com.example.tms.repository;

import com.example.tms.model.Pojazd;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface PojazdRepository extends JpaRepository<Pojazd, Integer> {

    // Szukanie pojazdów po typie zabudowy i dostępności (do Twoich filtrów z interfejsu)
    List<Pojazd> findByTypZabudowyAndStatusDostepnosci(String typZabudowy, Boolean statusDostepnosci);
}