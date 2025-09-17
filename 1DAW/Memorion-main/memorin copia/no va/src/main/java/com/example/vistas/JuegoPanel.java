package com.example.vistas;
import com.example.Controlador.Controlador;
import com.example.Main;

import javafx.geometry.Pos;
import javafx.scene.control.Button;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.HBox;

public class JuegoPanel extends BorderPane {
    private final Controlador controlador;
    private final GridPane tablero;

    public JuegoPanel() {
        this.controlador = new Controlador();
        this.tablero = new GridPane();

        tablero.setHgap(15); // Aumentar el espacio horizontal entre cartas
        tablero.setVgap(15); // Aumentar el espacio vertical entre cartas
        tablero.setAlignment(Pos.CENTER);

        // Configurar el tablero según la dificultad actual
        int dificultad = Main.getDificultad();
        Main.setFilas(dificultad);
        Main.setColumnas(dificultad);
        
        // Iniciar el juego con el tablero
        controlador.iniciarJuego(tablero);

        Button volverMenu = new Button("Volver al Menú");
        volverMenu.setOnAction(e -> {
            com.example.Main.setView(new MainPanel());
        });

        HBox topBar = new HBox(volverMenu);
        topBar.setAlignment(Pos.CENTER);
        topBar.setSpacing(10);

        setTop(topBar);
        setCenter(tablero);
    }
}