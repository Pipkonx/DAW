package vista;


import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;

public class MainPane extends BorderPane {
    private Button btnReiniciar = new Button("Reiniciar");

    public MainPane() {
        this.setId("mainPane");

        Label lblBienvenida = new Label("El memorion");
        this.setTop(lblBienvenida);
        
        btnReiniciar.setId("Reiniciar");
        this.setBottom(btnReiniciar);
        
        JuegoPane juegoPane = new JuegoPane();
        this.setCenter(juegoPane);
    }

    public JuegoPane getJuegoPane() {
        return (JuegoPane) this.getCenter();
    }

    public Button getBtnReiniciar() {
        return btnReiniciar;
    }
}