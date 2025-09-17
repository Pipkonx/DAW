package vista;

import javafx.scene.layout.GridPane;
import modelo.Cartas;

public class JuegoPane extends GridPane {
    final private Cartas[][] cartas;

    // en el pane los elementos y en el controlador metemos la funcionalidad
    public JuegoPane() {
        cartas = new Cartas[2][4];
        
        for (int i = 0; i < 2; i++) {
            for (int j = 0; j < 4; j++) {
                cartas[i][j] = new Cartas();
                this.add(cartas[i][j], j, i);
            }
        }
        this.setHgap(10);
        this.setVgap(10);
    }
    
    public Cartas getCartaAt(int row, int col) {
        return cartas[row][col];
    }
    
    public Cartas[][] getCartas() {
        return cartas;
    }
}