package com.example.tms.repository;

import com.example.tms.model.Dokument;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface DokumentRepository extends JpaRepository<Dokument, Integer> {

    // Szukanie dokumentów powiązanych z konkretnym zleceniem transportowym
    List<Dokument> findByIdZlecenia(Integer idZlecenia);
}