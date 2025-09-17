package controlador;

import java.io.File;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import javafx.animation.PauseTransition;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.GridPane;
import javafx.util.Duration;
import modelo.Cartas;
import vista.JuegoPane;
import vista.MainPane;

// con image view podemos poner la imagen en el button
// el collection es para mezclar las cartas
public class Controlador extends GridPane {

    final private int[][] valoresCartas = new int[2][4];
    final private boolean[][] cartasVolteadas = new boolean[2][4];

    private int cartasSeleccionadas, ultimaCartaI, ultimaCartaJ;
    private Image reverso;
    private List<Image> imagenes;

    final private MainPane mainPane;
    final private JuegoPane juegoPane;

    public Controlador() throws SQLException {
        mainPane = new MainPane();
        juegoPane = mainPane.getJuegoPane();
        
        cargarImagenes();
        distribuirValores();
        inicializarCartas();

        mainPane.getBtnReiniciar().setOnAction(event -> reiniciarJuego());
    }

    private void inicializarCartas() {
        for (int i = 0; i < 2; i++) {
            for (int j = 0; j < 4; j++) {
                Cartas carta = juegoPane.getCartaAt(i, j);
                carta.setId(valoresCartas[i][j]);
                
                ImageView vistaReverso = new ImageView(reverso);
                vistaReverso.setFitWidth(90);
                vistaReverso.setFitHeight(90);
                carta.setGraphic(vistaReverso);
                
                final int fila = i;
                final int columna = j;
                carta.setOnAction(e -> manejarClic(fila, columna));
            }
        }
    }
    
    private void reiniciarJuego() {
        for (int i = 0; i < 2; i++) {
            for (int j = 0; j < 4; j++) {
                cartasVolteadas[i][j] = false;
                
                Cartas carta = juegoPane.getCartaAt(i, j);
                ImageView vistaReverso = new ImageView(reverso);
                vistaReverso.setFitWidth(90);
                vistaReverso.setFitHeight(90);
                carta.setGraphic(vistaReverso);
            }
        }
        
        distribuirValores();
        
        for (int i = 0; i < 2; i++) {
            for (int j = 0; j < 4; j++) {
                juegoPane.getCartaAt(i, j).setId(valoresCartas[i][j]);
            }
        }
        cartasSeleccionadas = 0;
    }

    public MainPane getMainPane() {
        return mainPane;
    }

    public JuegoPane getJuegoPane() {
        return juegoPane;
    }

    private void cargarImagenes() {
        File fileReverso = new File("src/main/java/imagenes/reverso.jpg");
        reverso = new Image(fileReverso.toURI().toString());

        // Las parejas
        imagenes = new ArrayList<>();
        for (int i = 1; i <= 4; i++) {
            File file = new File("src/main/java/imagenes/" + i + ".jpg");
            Image imagen = new Image(file.toURI().toString());
            imagenes.add(imagen);
        }
    }

    private void distribuirValores() {
        List<Integer> valores = new ArrayList<>();
        for (int i = 0; i < 4; i++) {
            valores.add(i);
            valores.add(i);
        }
        Collections.shuffle(valores);

        // ahora le ponemos los valores a cada una
        int index = 0;
        for (int i = 0; i < 2; i++) {
            for (int j = 0; j < 4; j++) {
                valoresCartas[i][j] = valores.get(index++);
            }
        }
    }

    private void manejarClic(int i, int j) {
    // no hacemos nad si la carta esta volteada
        if (cartasVolteadas[i][j]) {
            return;
        }

        // Mostrar la imagen
        int valorCarta = valoresCartas[i][j];
        ImageView vistaImagen = new ImageView(imagenes.get(valorCarta));
        vistaImagen.setFitWidth(90);
        vistaImagen.setFitHeight(90);
        juegoPane.getCartaAt(i, j).setGraphic(vistaImagen);

        cartasVolteadas[i][j] = true;

        // con ayuda el resto mas o menos
        if (cartasSeleccionadas == 0) {
            // Primera carta
            cartasSeleccionadas = 1;
            ultimaCartaI = i;
            ultimaCartaJ = j;
        } else {
            // Segunda carta
            cartasSeleccionadas = 0;

            // Verificar si forman pareja
            if (valoresCartas[i][j] != valoresCartas[ultimaCartaI][ultimaCartaJ]) {
                final int actualI = i;
                final int actualJ = j;
                final int anteriorI = ultimaCartaI;
                final int anteriorJ = ultimaCartaJ;

                // Usamos el pause transition para darle tiempo
                PauseTransition p = new PauseTransition(Duration.seconds(1));

                p.setOnFinished(e -> {
                    // reverso de nuevo
                    ImageView reversoView1 = new ImageView(reverso);
                    reversoView1.setFitWidth(90);
                    reversoView1.setFitHeight(90);
                    juegoPane.getCartaAt(anteriorI, anteriorJ).setGraphic(reversoView1);

                    ImageView reversoView2 = new ImageView(reverso);
                    reversoView2.setFitWidth(90);
                    reversoView2.setFitHeight(90);
                    juegoPane.getCartaAt(actualI, actualJ).setGraphic(reversoView2);

                    // No volteadas
                    cartasVolteadas[anteriorI][anteriorJ] = false;
                    cartasVolteadas[actualI][actualJ] = false;
                });
                p.play();
            }
        }
    }
}