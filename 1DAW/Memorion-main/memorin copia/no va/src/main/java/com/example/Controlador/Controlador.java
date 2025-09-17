package com.example.Controlador;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import com.example.Main;
import com.example.modelo.Cartas;

import javafx.application.Platform;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.image.Image;
import javafx.scene.layout.GridPane;

public class Controlador {

    // --- Elementos para el juego ---
    @FXML
    private GridPane tablero;

    @FXML
    private Button jugarFacilButton;

    @FXML
    private Button jugarMedioButton;

    @FXML
    private Button jugarDificilButton;

    @FXML
    private Button salirButton;

    private boolean bloqueado = false;
    private Cartas primeraSeleccion = null;
    private Button primeraCarta = null;
    private Button segundaCarta = null;

    // Guarda los valores de las cartas para comparar
    private String[][] valoresCartas;
    private final List<Image> imagenes = new ArrayList<>();

    // --- Inicialización del juego ---
    public void initialize(Cartas cartas) {
        // Solo inicializa el tablero si existe en el FXML actual
        if (tablero != null) {
            cargarImagenes();

            int size = Main.getDificultad(); // 3, 6 o 12
            int totalCartas = size * size;
            int numPares = totalCartas / 2;

            // Crear la lista de pares de imágenes
            List<Image> pares = new ArrayList<>();
            for (int i = 0; i < numPares; i++) {
                Image img = imagenes.get(i % imagenes.size());
                pares.add(img);
                pares.add(img);
            }
            // Si el número de cartas es impar, añade una carta extra
            if (totalCartas % 2 != 0) {
                Image img = imagenes.get(numPares % imagenes.size());
                pares.add(img);
            }
            Collections.shuffle(pares);

            // Cambiar back.png por elbicho.jpg
            Image trasera = new Image(getClass().getResourceAsStream("/imagenesCartas/elbicho.jpg"));

            int index = 0;
            tablero.getChildren().clear();
            for (int fila = 0; fila < size; fila++) {
                for (int col = 0; col < size; col++) {
                    if (index >= pares.size())
                        break;
                    Image img = pares.get(index++);
                    Cartas carta = new Cartas(String.valueOf(img.hashCode()), img, trasera);
                    carta.setOnMouseClicked(e -> manejarSeleccion(carta));
                    tablero.add(carta, col, fila);
                }
            }
        }
    }

    private void cargarImagenes() {
        imagenes.clear(); // Limpia la lista antes de cargar
        try {
            System.out.println("Intentando cargar imágenes...");
            
            // Cargar imágenes desde la carpeta de recursos
            String[] nombresImagenes = {
                "camavinga.jpg", "elbicho.jpg", "lamineyamal.jpg", 
                "mbappe.jpg", "messi.jpg", "rodrigodePaul.jpg", "vinicius.jpg"
            };
            
            for (String nombre : nombresImagenes) {
                try {
                    // Intento con ClassLoader - esta es la forma correcta de acceder a recursos
                    String ruta = "/imagenesCartas/" + nombre;
                    System.out.println("Intentando cargar: " + ruta);
                    
                    java.io.InputStream is = getClass().getResourceAsStream(ruta);
                    
                    if (is != null) {
                        Image img = new Image(is);
                        imagenes.add(img);
                        System.out.println("Imagen cargada correctamente: " + nombre);
                    } else {
                        System.err.println("No se pudo encontrar la imagen: " + nombre);
                        
                        // Intento con ruta absoluta como respaldo
                        String rutaAbsoluta = "c:/Users/Pipkon/Desktop/Memorion/memorin copia/no va/src/main/resources/imagenesCartas/" + nombre;
                        java.io.File file = new java.io.File(rutaAbsoluta);
                        if (file.exists()) {
                            is = new java.io.FileInputStream(file);
                            Image img = new Image(is);
                            imagenes.add(img);
                            System.out.println("Imagen cargada desde ruta absoluta: " + nombre);
                        } else {
                            // Crear una imagen de color como respaldo
                            javafx.scene.canvas.Canvas canvas = new javafx.scene.canvas.Canvas(100, 100);
                            javafx.scene.canvas.GraphicsContext gc = canvas.getGraphicsContext2D();
                            gc.setFill(javafx.scene.paint.Color.BLUE);
                            gc.fillRect(0, 0, 100, 100);
                            javafx.scene.image.WritableImage wi = new javafx.scene.image.WritableImage(100, 100);
                            canvas.snapshot(null, wi);
                            imagenes.add(wi);
                            System.out.println("Creada imagen de respaldo para: " + nombre);
                        }
                    }
                } catch (Exception e) {
                    System.err.println("Error al cargar la imagen " + nombre + ": " + e.getMessage());
                    e.printStackTrace();
                }
            }
            
            System.out.println("Total de imágenes cargadas: " + imagenes.size());
        } catch (Exception e) {
            System.err.println("Error general al cargar las imágenes: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void manejarSeleccion(Cartas seleccionada) {
        if (bloqueado || seleccionada.esDescubierta())
            return;

        seleccionada.mostrar();

        if (primeraSeleccion == null) {
            primeraSeleccion = seleccionada;
        } else {
            bloqueado = true;
            if (seleccionada.getImagenFrontal().equals(primeraSeleccion.getImagenFrontal())) {
                seleccionada.marcarEncontrada();
                primeraSeleccion.marcarEncontrada();
                primeraSeleccion = null;
                bloqueado = false;
            } else {
                Timer timer = new Timer();
                Cartas anterior = primeraSeleccion;
                timer.schedule(new TimerTask() {
                    @Override
                    public void run() {
                        Platform.runLater(() -> {
                            seleccionada.ocultar();
                            anterior.ocultar();
                            primeraSeleccion = null;
                            bloqueado = false;
                        });
                    }
                }, 1000);
            }
        }
    }

    private void manejarSeleccion(Button carta, int fila, int col) {
        if (bloqueado || !carta.getText().equals("?"))
            return;

        carta.setText(valoresCartas[fila][col]);

        if (primeraCarta == null) {
            primeraCarta = carta;
        } else if (segundaCarta == null && carta != primeraCarta) {
            segundaCarta = carta;
            bloqueado = true;

            // Comprobar si coinciden
            if (primeraCarta.getText().equals(segundaCarta.getText())) {
                // Coinciden, se quedan boca arriba
                primeraCarta = null;
                segundaCarta = null;
                bloqueado = false;
            } else {
                // No coinciden, voltearlas después de un tiempo
                new Thread(() -> {
                    try {
                        Thread.sleep(800);
                    } catch (InterruptedException ignored) {
                    }
                    Platform.runLater(() -> {
                        primeraCarta.setText("?");
                        segundaCarta.setText("?");
                        primeraCarta = null;
                        segundaCarta = null;
                        bloqueado = false;
                    });
                }).start();
            }
        }
    }

    // --- Métodos del menú ---
    @FXML
    public void jugarFacil(ActionEvent event) {
        abrirJuegoConDificultad(3); // 3x3
    }

    @FXML
    public void jugarMedio(ActionEvent event) {
        abrirJuegoConDificultad(6); // 6x6
    }

    @FXML
    public void jugarDificil(ActionEvent event) {
        abrirJuegoConDificultad(12); // 12x12
    }

    @FXML
    public void salirDelJuego() {
        System.exit(0);
    }

    public void iniciarJuego(GridPane tablero) {
        int filas = Main.getFilas();
        int columnas = Main.getColumnas();
        tablero.getChildren().clear();
    
        // Cargar imágenes
        cargarImagenes();
    
        // Verificar que se cargaron las imágenes correctamente
        if (imagenes.isEmpty()) {
            System.err.println("No se pudieron cargar las imágenes");
            return;
        }
    
        // Crear la lista de pares de imágenes
        List<Image> pares = new ArrayList<>();
        int numCartas = filas * columnas;
        int numPares = numCartas / 2;
    
        for (int i = 0; i < numPares; i++) {
            Image img = imagenes.get(i % imagenes.size());
            pares.add(img);
            pares.add(img);
        }
        
        // Si el número de cartas es impar, añade una carta extra
        if (numCartas % 2 != 0) {
            Image img = imagenes.get(numPares % imagenes.size());
            pares.add(img);
        }
        
        Collections.shuffle(pares);
    
        // Cargar imagen trasera
        Image trasera = null;
        try {
            // Intentar cargar la imagen trasera
            java.io.InputStream is = getClass().getResourceAsStream("/imagenesCartas/elbicho.jpg");
            
            if (is != null) {
                trasera = new Image(is);
                System.out.println("Imagen trasera cargada correctamente");
            } else {
                System.err.println("No se pudo cargar la imagen trasera, intentando ruta absoluta");
                
                // Intento con ruta absoluta
                String rutaAbsoluta = "c:/Users/Pipkon/Desktop/Memorion/memorin copia/no va/src/main/resources/imagenesCartas/elbicho.jpg";
                java.io.File file = new java.io.File(rutaAbsoluta);
                if (file.exists()) {
                    is = new java.io.FileInputStream(file);
                    trasera = new Image(is);
                    System.out.println("Imagen trasera cargada desde ruta absoluta");
                } else {
                    // Crear una imagen de respaldo
                    javafx.scene.canvas.Canvas canvas = new javafx.scene.canvas.Canvas(100, 100);
                    javafx.scene.canvas.GraphicsContext gc = canvas.getGraphicsContext2D();
                    gc.setFill(javafx.scene.paint.Color.GRAY);
                    gc.fillRect(0, 0, 100, 100);
                    javafx.scene.image.WritableImage wi = new javafx.scene.image.WritableImage(100, 100);
                    canvas.snapshot(null, wi);
                    trasera = wi;
                    System.out.println("Creada imagen trasera de respaldo");
                }
            }
        } catch (Exception e) {
            System.err.println("Error al cargar la imagen trasera: " + e.getMessage());
            e.printStackTrace();
            
            // Crear una imagen de respaldo
            javafx.scene.canvas.Canvas canvas = new javafx.scene.canvas.Canvas(100, 100);
            javafx.scene.canvas.GraphicsContext gc = canvas.getGraphicsContext2D();
            gc.setFill(javafx.scene.paint.Color.GRAY);
            gc.fillRect(0, 0, 100, 100);
            javafx.scene.image.WritableImage wi = new javafx.scene.image.WritableImage(100, 100);
            canvas.snapshot(null, wi);
            trasera = wi;
        }
    
        int index = 0;
        for (int fila = 0; fila < filas; fila++) {
            for (int col = 0; col < columnas; col++) {
                if (index >= pares.size())
                    break;
                
                Image img = pares.get(index++);
                Cartas carta = new Cartas(String.valueOf(img.hashCode()), img, trasera);
                carta.setPrefSize(100, 100);
                carta.setMaxSize(100, 100);
                carta.setMinSize(100, 100);
                carta.getStyleClass().add("carta"); // Aplicar el estilo CSS
                
                // Añadir borde de color a la carta
                carta.setStyle("-fx-border-color: #3498db; -fx-border-width: 3px; -fx-border-radius: 5px;");
                
                carta.setOnMouseClicked(e -> manejarSeleccion(carta));
                
                tablero.add(carta, col, fila);
            }
        }
    }

    public void abrirJuegoConDificultad(int size) {
        try {
            Main.setDificultad(size);
            Main.setView(new com.example.vistas.JuegoPanel());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}