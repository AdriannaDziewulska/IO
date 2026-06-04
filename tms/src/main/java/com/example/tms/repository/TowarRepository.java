package com.example.tms.repository;

import com.example.tms.model.Towar;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface TowarRepository extends JpaRepository<Towar, Integer> {
    // Podstawowe metody CRUD (Create, Read, Update, Delete) w zupełności tu wystarczą
}