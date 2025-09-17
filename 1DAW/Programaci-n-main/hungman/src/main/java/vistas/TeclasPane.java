package vistas;

import java.util.ArrayList;

import javafx.scene.control.Button;
import javafx.scene.layout.GridPane;

public class TeclasPane extends GridPane {
    private ArrayList<Button> teclas;

    public TeclasPane() {
        teclas = new ArrayList<Button>();
        char letter = 'A';
        this.setId("teclasPane");
        
        for (int row = 0; row < 3; row++) {
            for (int col = 0; col < 8; col++) {
                Button tecla = new Button("" + letter);
                teclas.add(tecla);
                this.add(tecla, col, row);
                letter++;
            }
        }
    }

    public ArrayList<Button> getTeclas() {
        return teclas;
    }
}
