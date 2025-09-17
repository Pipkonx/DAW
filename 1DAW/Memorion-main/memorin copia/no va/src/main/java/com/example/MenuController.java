package com.example;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import com.example.Main;

public class MenuController {

    @FXML
    private void jugarFacil(ActionEvent event) {
        abrirJuegoConDificultad(3);
    }

    @FXML
    private void jugarMedio(ActionEvent event) {
        abrirJuegoConDificultad(6);
    }

    @FXML
    private void jugarDificil(ActionEvent event) {
        abrirJuegoConDificultad(12);
    }

     private void abrirJuegoConDificultad(int size) {
        try {
            Main.setDificultad(size);
            Main.setView(new com.example.vistas.JuegoPanel());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void salirDelJuego(ActionEvent event) {
        System.exit(0);  // Salir del juego
    }
}
