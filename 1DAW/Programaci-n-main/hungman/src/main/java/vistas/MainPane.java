package vistas;

import javafx.scene.layout.BorderPane;
import modelo.Ahorcado;

public class MainPane extends BorderPane {
    private TeclasPane teclasPane;
    private MascaraPane mascaraPane;
    private Ahorcado ahorcado;

    public MainPane() {
        this.teclasPane = new TeclasPane();
        this.setBottom(teclasPane);

        this.mascaraPane = new MascaraPane();
        this.setTop(mascaraPane);
    }

    public TeclasPane getTeclasPane() {
        return teclasPane;
    }

    public void setAhorcado(Ahorcado ahorcado) {
        this.ahorcado = ahorcado;
        mascaraPane.actualizarPalabra(ahorcado.mascaraToString());
        mascaraPane.actualizarFallos(ahorcado.getFallos());
    }

    public void setMascaraPane(MascaraPane mascaraPane) {
        this.mascaraPane = mascaraPane;
    }
    
    public MascaraPane getMascaraPane() {
        return mascaraPane;
    }
}

