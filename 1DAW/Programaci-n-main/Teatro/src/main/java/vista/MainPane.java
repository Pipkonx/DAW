package vista;

import javafx.geometry.Insets;
import javafx.scene.control.Button;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.GridPane;

public class MainPane extends BorderPane {
    final private Button btnCerrarSesion;
    final private Button btnNuevoEspectaculo;
    final private Button btnVentraEntrada;
    final private Button btnCompraEntrada;

    public MainPane() {
        this.setId("mainPane");

        btnCerrarSesion = new Button("Cerrar Sesión");
        btnCompraEntrada = new Button("ComprarEntrada");
        btnNuevoEspectaculo = new Button("Nuevo espectaculo");
        btnVentraEntrada = new Button("Venta entrada");
        
        btnCerrarSesion.setId("btn");
        btnCompraEntrada.setId("btn");
        btnNuevoEspectaculo.setId("btn");
        btnVentraEntrada.setId("btn");

        GridPane topBar = new GridPane();
        topBar.setPadding(new Insets(10));
        topBar.setHgap(15);

        topBar.add(btnCompraEntrada, 1, 0);
        topBar.add(btnNuevoEspectaculo, 2, 0);
        topBar.add(btnVentraEntrada, 3, 0);
        topBar.add(btnCerrarSesion, 4, 0);

        this.setTop(topBar);

        GridPane enviarPanel = new GridPane();
        enviarPanel.setPadding(new Insets(10));
        enviarPanel.setVgap(10);

    }

    public Button getBtnCerrarSesion() {
        return btnCerrarSesion;
    }

    public Button getBtnNuevoEspectaculo() {
        return btnNuevoEspectaculo;
    }

    public Button getBtnVentraEntrada() {
        return btnVentraEntrada;
    }

    public Button getBtnCompraEntrada() {
        return btnCompraEntrada;
    }
}
