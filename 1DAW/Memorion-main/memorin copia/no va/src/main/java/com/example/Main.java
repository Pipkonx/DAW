package com.example;

import com.example.vistas.MainPanel;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {

    private static Scene scene;
    private static Stage primaryStage;
    private static int dificultad = 3;
    private static int filas = 2;
    private static int columnas = 3;

    @Override
    public void start(Stage stage) {
        primaryStage = stage;
        scene = new Scene(new MainPanel(), 640, 480);
        
        // Cargar el archivo CSS
        String css = getClass().getResource("/style.css").toExternalForm();
        scene.getStylesheets().add(css);
        
        stage.setTitle("Memorión");
        stage.setScene(scene);
        stage.show();
    }

    public static void setView(javafx.scene.Parent root) {
        scene.setRoot(root);
    }

    public static void setDificultad(int d) {
        dificultad = d;
    }

    public static int getDificultad() {
        return dificultad;
    }

    public static void setFilas(int f) { filas = f; }
    public static void setColumnas(int c) { columnas = c; }
    public static int getFilas() { return filas; }
    public static int getColumnas() { return columnas; }

    public static Stage getPrimaryStage() {
        return primaryStage;
    }

    public static void main(String[] args) {
        launch(args);
    }
}
