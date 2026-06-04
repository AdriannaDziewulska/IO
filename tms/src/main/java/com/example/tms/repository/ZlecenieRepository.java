package com.example.tms.repository;

import com.example.tms.model.Zlecenie;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface ZlecenieRepository extends JpaRepository<Zlecenie, Integer> {

    // Metoda przydatna dla Spedytora i Kierowcy - szukanie zleceń po ich aktualnym statusie
    List<Zlecenie> findByStatus(String status);
}