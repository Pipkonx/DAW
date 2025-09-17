package vista;

import java.util.ArrayList;

import javafx.geometry.Insets;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.GridPane;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import modelo.Mensaje;

public class MainPane extends BorderPane {
    private Button btnRecibidos;
    private Button btnEnviados;
    private Button btnEnviar;
    private Button btnCerrarSesion;
    private TextField txtDestinatario;
    private TextArea txtContenido;
    private ListView<Mensaje> listaMensajes;
    private Label lblEstado;
    private boolean vistaEnviadosActiva;
    
    public MainPane() {
        setPadding(new Insets(10));
        this.setId("mainPane");

        btnRecibidos = new Button("Mensajes Recibidos");
        btnRecibidos.setId("btn");
        btnEnviados = new Button("Mensajes Enviados");
        btnEnviados.setId("btn");
        btnCerrarSesion = new Button("Cerrar Sesión");
        btnCerrarSesion.setId("btn");
        
        GridPane topBar = new GridPane();
        topBar.setPadding(new Insets(10));
        topBar.setHgap(15);
        topBar.add(btnRecibidos, 1, 0);
        topBar.add(btnEnviados, 2, 0);
        topBar.add(btnCerrarSesion, 3, 0);
        
        // mensajes
        listaMensajes = new ListView<>();
        listaMensajes.setPrefHeight(400);
        
        GridPane enviarPanel = new GridPane();
        enviarPanel.setPadding(new Insets(10));
        enviarPanel.setVgap(10);
        
        Label lblNuevoMensaje = new Label("Nuevo Mensaje");
        lblNuevoMensaje.setFont(Font.font("Tahoma", FontWeight.BOLD, 14));
        
        txtDestinatario = new TextField();
        txtDestinatario.setPromptText("Destinatario (email)");
        
        txtContenido = new TextArea();
        txtContenido.setPromptText("Escribe tu mensaje aquí");
        txtContenido.setPrefHeight(200);
        
        btnEnviar = new Button("Enviar Mensaje");
        btnEnviar.setId("btn");

        
        lblEstado = new Label("");
        
        enviarPanel.add(lblNuevoMensaje, 0, 0);
        enviarPanel.add(txtDestinatario, 0, 1);
        enviarPanel.add(txtContenido, 0, 2);
        enviarPanel.add(btnEnviar, 0, 3);
        enviarPanel.add(lblEstado, 0, 4);
        
        setTop(topBar);
        setCenter(listaMensajes);
        setRight(enviarPanel);
        
        lblEstado.getStyleClass().add("status-error");
        
        vistaEnviadosActiva = false;
    }
    
    public void mostrarMensajes(ArrayList<Mensaje> mensajes, String titulo) {
        listaMensajes.getItems().clear();
        
        if (mensajes != null && !mensajes.isEmpty()) {
            for (Mensaje mensaje : mensajes) {
                listaMensajes.getItems().add(mensaje);
            }
            lblEstado.setText("");
        } else {
            lblEstado.setText("No hay mensajes para mostrar");
        }
        
        vistaEnviadosActiva = titulo.contains("enviados");
    }
    
    public void mostrarMensajeError(String mensaje) {
        lblEstado.getStyleClass().clear();
        lblEstado.getStyleClass().add("status-error");
        lblEstado.setText(mensaje);
    }
    
    public void mostrarMensajeExito(String mensaje) {
        lblEstado.getStyleClass().clear();
        lblEstado.getStyleClass().add("status-success");
        lblEstado.setText(mensaje);
    }
    
    public void limpiarCamposEnvio() {
        txtDestinatario.clear();
        txtContenido.clear();
    }
    
    public boolean esVistaEnviadosActiva() {
        return vistaEnviadosActiva;
    }

    public Button getBtnRecibidos() {
        return btnRecibidos;
    }

    public Button getBtnEnviados() {
        return btnEnviados;
    }

    public Button getBtnEnviar() {
        return btnEnviar;
    }

    public Button getBtnCerrarSesion() {
        return btnCerrarSesion;
    }

    public TextField getTxtDestinatario() {
        return txtDestinatario;
    }

    public TextArea getTxtContenido() {
        return txtContenido;
    }
}
