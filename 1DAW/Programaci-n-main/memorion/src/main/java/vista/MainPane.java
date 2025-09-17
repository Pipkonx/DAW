package vista;

import java.util.ArrayList;

import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.VBox;
import modelo.Clasificacion;

public class MainPane extends BorderPane {
    private Button btnJugar;
    private Button btnSalir;
    private Button btnLogs;
    private ClasificacionGlobalPane clasificacionPane;

    public MainPane() {
        btnJugar = new Button("Jugar");
        btnSalir = new Button("Cerrar Sesión");
        btnLogs = new Button("Mis Tiempos");

        this.setId("mainPane");
        btnJugar.setId("btn");
        btnSalir.setId("btn");
        btnLogs.setId("btn");

        // Botones derecha
        VBox botonesBox = new VBox(10);
        botonesBox.getChildren().addAll(btnJugar, btnLogs, btnSalir);
        
        clasificacionPane = new ClasificacionGlobalPane();
        
        // El layout
        setRight(botonesBox);
        setLeft(clasificacionPane);
        
        Label lblBienvenida = new Label("Memorion");

        lblBienvenida.setId("lblBienvenida");
        botonesBox.setId("botonesBox");

        setCenter(lblBienvenida);
    }

    public Button getBtnJugar() {
        return btnJugar;
    }

    public Button getBtnSalir() {
        return btnSalir;
    }

    public Button getBtnLogs() {
        return btnLogs;
    }
    
    public void actualizarClasificacion(ArrayList<Clasificacion> clasificaciones) {
        clasificacionPane.actualizarClasificacion(clasificaciones);
    }

    public void mostrarRanking(ArrayList<Clasificacion> clasificaciones) {
        // Simplemente actualizamos la tabla de clasificación y mostramos un mensaje
        actualizarClasificacion(clasificaciones);
        
        Label lblBienvenida = new Label("¡Bienvenido a Memorion!");
        lblBienvenida.setStyle("-fx-font-size: 24px; -fx-font-weight: bold;");
        setCenter(lblBienvenida);
    }

    public void mostrarLogs(ArrayList<Clasificacion> clasificaciones) {
        // Un VBox es un contenedor que organiza sus elementos en una columna
        VBox logsBox = new VBox(10);
        this.setId("mostrarLogs");


        Label titulo = new Label("Mis Tiempos");
        titulo.setId("titulo");

        // Añadir a mis tiempos
        logsBox.getChildren().add(titulo);

        if (clasificaciones != null && !clasificaciones.isEmpty()) {
            for (Clasificacion c : clasificaciones) {
                Label lblLog = new Label(
                        "Tiempo: " + c.getTiempo() + "s | Fecha: " + c.getFecha());
                logsBox.getChildren().add(lblLog);
            }
        } else {
            Label lblNoData = new Label("No has jugado ninguna partida aún");
            logsBox.getChildren().add(lblNoData);
        }

        setCenter(logsBox);
    }
}