package controlador;

import java.util.ArrayList;

import javafx.scene.control.Button;
import modelo.Ahorcado;
import vistas.MainPane;

public class Controlador {
    private MainPane root;
    private Ahorcado hungMan;
    private ArrayList<Button> teclas;

    public Controlador(MainPane root, Ahorcado hungMan) {
        this.root = root;
        this.hungMan = hungMan;
        this.teclas = root.getTeclasPane().getTeclas();

        for (Button tecla : teclas) {
            tecla.setOnAction(e -> {
                char letter = tecla.getText().toLowerCase().charAt(0);
                if (hungMan.CompruebaLetra(letter + "")) {
                    root.getMascaraPane().actualizarPalabra(hungMan.mascaraToString());
                } else {
                    hungMan.actualizarFallos();
                    root.getMascaraPane().actualizarFallos(hungMan.getFallos());
                }
                tecla.setDisable(true);
            });
        }
    }
}