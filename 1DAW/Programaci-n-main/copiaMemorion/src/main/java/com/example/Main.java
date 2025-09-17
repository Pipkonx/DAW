package com.example;

import java.io.IOException;
import java.sql.SQLException;

import controlador.Controlador;
import javafx.application.Application;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {
    @Override
    public void start(Stage stage) throws IOException {
        try {
            Controlador controlador = new Controlador();

            Scene scene = new Scene( controlador.getMainPane(), 800, 600);
            scene.getStylesheets().add(getClass().getResource("styles.css").toExternalForm());

            stage.setTitle("Juego memorion");
            stage.setScene(scene);
            stage.show();
        } catch (SQLException e) {
            System.err.println("Error de SQL: " + e.getMessage());
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch();
    }
}