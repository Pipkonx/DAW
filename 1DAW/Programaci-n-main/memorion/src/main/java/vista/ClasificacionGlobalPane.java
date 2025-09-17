package vista;

import java.util.ArrayList;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.geometry.Insets;
import javafx.scene.control.Label;
import javafx.scene.control.TableCell;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.VBox;
import modelo.Clasificacion;

public class ClasificacionGlobalPane extends VBox {
    
    private TableView<Clasificacion> tablaClasificacion;
    
    public ClasificacionGlobalPane() {
        setPadding(new Insets(10));
        setSpacing(10);
        
        Label titulo = new Label("Mejores Tiempos");
        titulo.setId("titulo");
        
        // Creamos la tabla
        //set columnresize policy es para que se ajuste el tamaño de las columnas a la tabla
        // CONSTRAINED_RESIZE_POLICY es para que se ajuste el tamaño de las columnas a la tabla
        tablaClasificacion = new TableView<>();
        tablaClasificacion.setColumnResizePolicy(TableView.CONSTRAINED_RESIZE_POLICY);
        
        // TODO FALTA AGREGAR LA COLUMNA DE LA POSICION
        
        // Columna nombre del jugador
        TableColumn<Clasificacion, String> nombreCol = new TableColumn<>("Jugador");
        nombreCol.setCellValueFactory(new PropertyValueFactory<>("nombreJugador"));
        
        // Columna tiempo
        TableColumn<Clasificacion, Long> tiempoCol = new TableColumn<>("Tiempo (s)");
        tiempoCol.setCellValueFactory(new PropertyValueFactory<>("tiempo"));
        
        // Formatear el tiempo
        tiempoCol.setCellFactory(column -> new TableCell<Clasificacion, Long>() {
            @Override
            protected void updateItem(Long item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setText(null);
                } else {
                    setText(String.valueOf(item) + "s");
                }
            }
        });
        
        // Columna para la fecha
        //setCellValueFactory es para que sepa de donde sacar los datos para la columna fecha y que sepa como formatearlos
        TableColumn<Clasificacion, String> fechaCol = new TableColumn<>("Fecha");
        fechaCol.setCellValueFactory(new PropertyValueFactory<>("fecha"));
        
        tablaClasificacion.getColumns().addAll(nombreCol, tiempoCol, fechaCol);
        getChildren().addAll(titulo, tablaClasificacion);
    }
    
    public void actualizarClasificacion(ArrayList<Clasificacion> clasificaciones) {
        if (clasificaciones != null && !clasificaciones.isEmpty()) {
            ObservableList<Clasificacion> data = FXCollections.observableArrayList(clasificaciones);
            tablaClasificacion.setItems(data);
        } else {
            tablaClasificacion.setItems(FXCollections.observableArrayList());
        }
    }
}