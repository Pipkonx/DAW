package vista;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import controlador.Controlador;
import javafx.application.Platform;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.scene.paint.Color;
import javafx.scene.shape.Rectangle;

public class JuegoPane extends BorderPane {
    private Button btnVolver;
    private GridPane tablero;
    private Label lblTiempo;
    private Label lblParejas;
    private Label lblMensaje;
    private Button btnReiniciar;
    private VBox panelFinal;

    private int totalParejas;
    private int parejasEncontradas;
    private int tiempoTranscurrido;
    private Timer timer;

    private List<TarjetaMemoria> tarjetas;
    private TarjetaMemoria tarjetaSeleccionada1;
    private TarjetaMemoria tarjetaSeleccionada2;
    private boolean bloqueado = false;

    private Controlador controlador;

    // Limites - constantes
    private final int FILAS = 4;
    private final int COLUMNA = 4;

    public JuegoPane() {
        this(null);
    }

    public JuegoPane(Controlador controlador) {
        this.controlador = controlador;
        this.totalParejas = (FILAS * COLUMNA) / 2;
        this.parejasEncontradas = 0;
        this.tiempoTranscurrido = 0;

        inicializarComponentes();
    }

    private void inicializarComponentes() {
        // Panel superior con información
        HBox panelInfo = new HBox(20);
        panelInfo.setId("panelInfo");

        lblTiempo = new Label("Tiempo: 0s");
        lblTiempo.setId("lblTiempo");

        lblParejas = new Label("Parejas: 0/" + totalParejas);

        panelInfo.getChildren().addAll(lblTiempo, lblParejas);

        // Panel central tablero
        tablero = new GridPane();
        tablero.setId("tablero");

        // Panel inferior botones
        HBox panelBotones = new HBox(20);
        panelBotones.setId("panelBotones");

        btnVolver = new Button("Volver al Menú");
        btnReiniciar = new Button("Reiniciar Juego");

        btnVolver.setId("btn");
        btnReiniciar.setId("btn");

        btnReiniciar.setOnAction(e -> iniciarJuego());
        btnVolver.setOnAction(e -> {
            if (controlador != null) {
                controlador.mostrarMainPane();
            }
        });

        panelBotones.getChildren().addAll(btnVolver, btnReiniciar);

        // panel victoria
        panelFinal = new VBox(20);
        panelFinal.setId("panel-final");
        panelFinal.setVisible(false);

        lblMensaje = new Label("Has ganado");
        lblMensaje.setId("lblMensaje");

        Button btnContinuar = new Button("Continuar");
        btnContinuar.setId("btn");

        btnContinuar.setOnAction(e -> {
            panelFinal.setVisible(false);
            if (controlador != null) {
                btnVolver.fire();
            }
        });

        // addall es para agregar varios elementos a la vez
        panelFinal.getChildren().addAll(lblMensaje, btnContinuar);

        setTop(panelInfo);
        setCenter(tablero);
        setBottom(panelBotones);

        // panel final con stackpane, te permite la superpocion de los elementos
        StackPane centroConOverlay = new StackPane();
        centroConOverlay.getChildren().addAll(tablero, panelFinal);
        setCenter(centroConOverlay);
    }

    public void iniciarJuego() {
        // lo detiene lo volvemos a 0
        if (timer != null) {
            timer.cancel();
        }
        parejasEncontradas = 0;
        tiempoTranscurrido = 0;
        bloqueado = false;
        tarjetaSeleccionada1 = null;
        tarjetaSeleccionada2 = null;
        lblTiempo.setText("Tiempo: 0s");
        lblParejas.setText("Parejas: 0/" + totalParejas);

        panelFinal.setVisible(false);
        crearTarjetas();
        iniciarTemporizador();
    }

    private void crearTarjetas() {
        tablero.getChildren().clear();
        tarjetas = new ArrayList<>();

        // Crear los pares de tarjetas
        List<Integer> valores = new ArrayList<>();
        for (int i = 1; i <= totalParejas; i++) {
            valores.add(i);
            valores.add(i);
        }

        Collections.shuffle(valores);

        // Crear los cuadrados
        int index = 0;
        for (int i = 0; i < FILAS; i++) {
            for (int j = 0; j < COLUMNA; j++) {
                TarjetaMemoria tarjeta = new TarjetaMemoria(valores.get(index));
                tarjeta.setOnMouseClicked(e -> seleccionarTarjeta(tarjeta));
                tarjetas.add(tarjeta);
                tablero.add(tarjeta, j, i);
                index++;
            }
        }
    }

    //en vez 
    private void iniciarTemporizador() {
        timer = new Timer();
        timer.scheduleAtFixedRate(new TimerTask() {
            @Override
            public void run() {
                tiempoTranscurrido++;
                Platform.runLater(() -> lblTiempo.setText("Tiempo: " + tiempoTranscurrido + "s"));
            }
        }, 1000, 1000);
    }

    private void seleccionarTarjeta(TarjetaMemoria tarjeta) {
        // para que no haga nada si esta volteada o bloqueado
        if (bloqueado || tarjeta.isVolteada()) {
            return;
        }

        tarjeta.voltear();

        // Procesar la selección
        if (tarjetaSeleccionada1 == null) {
            tarjetaSeleccionada1 = tarjeta;
        } else if (tarjetaSeleccionada2 == null) {
            tarjetaSeleccionada2 = tarjeta;

            bloqueado = true;

            if (tarjetaSeleccionada1.getValor() == tarjetaSeleccionada2.getValor()) {
                parejasEncontradas++;
                lblParejas.setText("Parejas: " + parejasEncontradas + "/" + totalParejas);

                tarjetaSeleccionada1.marcarEncontrada();
                tarjetaSeleccionada2.marcarEncontrada();

                tarjetaSeleccionada1 = null;
                tarjetaSeleccionada2 = null;
                bloqueado = false;

                // Comprobar si ha ganado
                if (parejasEncontradas == totalParejas) {
                    finalizarJuego();
                }
            } else {
                // Si no es pareja ocultarlo
                // .schedule es para que se ejecute despues de un tiempo
                new Timer().schedule(new TimerTask() {
                    @Override
                    public void run() {
                        Platform.runLater(() -> {
                            tarjetaSeleccionada1.voltear();
                            tarjetaSeleccionada2.voltear();
                            tarjetaSeleccionada1 = null;
                            tarjetaSeleccionada2 = null;
                            bloqueado = false;
                        });
                    }
                }, 1000);
            }
        }
    }

    private void finalizarJuego() {
        // Parar el tiempo
        if (timer != null) {
            timer.cancel();
        }

        lblMensaje.setText("Has ganado!\nTiempo: " + tiempoTranscurrido + "s");
        panelFinal.setVisible(true);
        if (controlador != null) {
            controlador.guardarTiempo(tiempoTranscurrido);
        }
    }

    public Button getBtnVolver() {
        return btnVolver;
    }

    // Clase interna para las tarjetas , el stackpane es para los cuadrados se vean
    // con el label
    private class TarjetaMemoria extends StackPane {
        private int valor;
        private boolean volteada = false;
        private boolean encontrada = false;

        private Rectangle fondo;
        private Label lblValor;

        public TarjetaMemoria(int valor) {
            this.valor = valor;

            // Creamos los cuadrados
            fondo = new Rectangle(80, 80);
            fondo.setId("fondoTarjeta");

            lblValor = new Label(String.valueOf(valor));
            lblValor.setId("lblValor");
            lblValor.setVisible(false);

            getChildren().addAll(fondo, lblValor);

            setMinSize(80, 80);
            setMaxSize(80, 80);
            setPrefSize(80, 80);
        }

        public int getValor() {
            return valor;
        }

        public boolean isVolteada() {
            return volteada;
        }

        public void voltear() {
            if (!encontrada) {
                volteada = !volteada;
                lblValor.setVisible(volteada);
                fondo.setFill(volteada ? Color.WHITE : Color.LIGHTBLUE);
            }
        }

        public void marcarEncontrada() {
            encontrada = true;
            fondo.setId("parejaValida");
        }
    }
}