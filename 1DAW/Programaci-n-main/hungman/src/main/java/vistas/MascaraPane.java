package vistas;

import javafx.scene.control.Label;
import javafx.scene.layout.VBox;

public class MascaraPane extends VBox {
    private Label palabraLabel;
    private Label fallosLabel;

    public MascaraPane() {
        palabraLabel = new Label();
        this.setId("mascaraPane");
        palabraLabel.setId("palabraLabel");
        fallosLabel = new Label("Fallos: 0");
        this.getChildren().addAll(palabraLabel, fallosLabel);
    }

    public void actualizarPalabra(String mascara) {
        StringBuilder displayText = new StringBuilder();
        for (char c : mascara.toCharArray()) {
            // Agrega el carácter actual y un espacio después para construir el texto a mostrar
            // append(c) agrega el carácter de la máscara
            // append(" ") agrega un espacio después de cada carácter para mejor legibilidad
            displayText.append(c).append(" ");
        }
        palabraLabel.setText(displayText.toString());
    }

    public void actualizarFallos(int fallos) {
        fallosLabel.setText("Fallos: " + fallos);
    }
}
