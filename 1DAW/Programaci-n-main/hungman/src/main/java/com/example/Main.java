package com.example;

import java.io.IOException;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.stage.Stage;
import modelo.Ahorcado;
import modelo.Diccionario;
import vistas.MainPane;

public class Main extends Application {
    @Override
    public void start(Stage stage) throws IOException {
        try {
            Ahorcado hungMan = new Ahorcado(new Diccionario().getRandomWord());
            MainPane root = new MainPane();
            root.setAhorcado(hungMan);
            Scene scene = new Scene(root, 1000, 800);
            scene.getStylesheets().add(getClass().getResource("styles.css").toExternalForm());

            stage.setTitle("Juego del Ahorcado");
            stage.setScene(scene);
            stage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch();
    }
}