package vista;

import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.GridPane;

public class RegisterPane extends GridPane {
    private TextField txtNombre;
    private TextField txtEmail;
    private PasswordField txtPassword;
    private Label lblStatus;
    private Button btnRegistrar;
    private Button btnVolver;
    private Label lblTitulo;

    public RegisterPane() {
        this.lblTitulo = new Label("Registro de Usuario");
        this.lblStatus = new Label("Completa el formulario para registrarte");

        Label lblNombre = new Label("Nombre:");
        this.txtNombre = new TextField();
        this.txtNombre.setPromptText("Tu nombre");

        Label lblEmail = new Label("Correo electrónico:");
        this.txtEmail = new TextField();
        this.txtEmail.setPromptText("ejemplo@");

        Label lblPassword = new Label("Contraseña:");
        this.txtPassword = new PasswordField();
        this.txtPassword.setPromptText("Contraseña");

        this.btnRegistrar = new Button("Registrar");
        this.btnVolver = new Button("Volver");
        
        this.setId("RegisterPane");
        this.lblTitulo.setId("tituloRegistro");
        this.btnRegistrar.setId("btn");
        this.btnVolver.setId("btn");

        this.add(lblTitulo, 0, 0, 2, 1);
        this.add(lblStatus, 0, 1, 2, 1);
        this.add(lblNombre, 0, 2);
        this.add(txtNombre, 1, 2);
        this.add(lblEmail, 0, 3);
        this.add(txtEmail, 1, 3);
        this.add(lblPassword, 0, 4);
        this.add(txtPassword, 1, 4);
        this.add(btnVolver, 0, 5);
        this.add(btnRegistrar, 1, 5);
    }

    public TextField getTxtNombre() {
        return txtNombre;
    }

    public TextField getTxtEmail() {
        return txtEmail;
    }

    public PasswordField getTxtPassword() {
        return txtPassword;
    }

    public Label getLblStatus() {
        return lblStatus;
    }

    public Button getBtnRegistrar() {
        return btnRegistrar;
    }

    public Button getBtnVolver() {
        return btnVolver;
    }
}