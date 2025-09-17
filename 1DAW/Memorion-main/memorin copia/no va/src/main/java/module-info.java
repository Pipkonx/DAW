module com.example {
    requires javafx.controls;
    requires javafx.fxml;

    opens com.example to javafx.fxml; // Abre el paquete para javafx.fxml
    opens com.example.Controlador to javafx.fxml;
    opens com.example.modelo to javafx.fxml;

    exports com.example;
    exports com.example.Controlador;
    exports com.example.modelo;
}
