package vista;

import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.GridPane;

public class RegisterPane extends GridPane {
    final private TextField txtNombre;
    final private TextField txtEmail;
    final private PasswordField txtPassword;
    final private PasswordField txtConfirmPassword;
    final private Button btnRegistrar;
    final private Button btnVolver;
    final private Label lblTitulo;

    public RegisterPane() {
        this.setId("loginpane");

        this.lblTitulo = new Label("Registro de Usuario");
        this.lblTitulo.setId("lblTitulo");

        Label lblNombre = new Label("Nombre:");
        this.txtNombre = new TextField();
        this.txtNombre.setPromptText("Tu nombre");

        Label lblEmail = new Label("Correo electrónico:");
        this.txtEmail = new TextField();
        this.txtEmail.setPromptText("ejemplo@");

        Label lblPassword = new Label("Contraseña:");
        this.txtPassword = new PasswordField();
        this.txtPassword.setPromptText("Contraseña");

        Label lblConfirmPassword = new Label("Confirmar contraseña:");
        this.txtConfirmPassword = new PasswordField();
        this.txtConfirmPassword.setPromptText("Confirmar contraseña");

        this.btnRegistrar = new Button("Registrar");
        this.btnRegistrar.setId("btn");
        this.btnVolver = new Button("Volver");
        this.btnVolver.setId("btn");

        this.add(lblTitulo, 0, 0, 2, 1);
        this.add(lblNombre, 0, 2);
        this.add(txtNombre, 1, 2);
        this.add(lblEmail, 0, 3);
        this.add(txtEmail, 1, 3);
        this.add(lblPassword, 0, 4);
        this.add(txtPassword, 1, 4);
        this.add(lblConfirmPassword, 0, 5);
        this.add(txtConfirmPassword, 1, 5);
        this.add(btnVolver, 0, 6);
        this.add(btnRegistrar, 1, 6);

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

    public PasswordField getTxtConfirmPassword() {
        return txtConfirmPassword;
    }

    public Button getBtnRegistrar() {
        return btnRegistrar;
    }

    public Button getBtnVolver() {
        return btnVolver;
    }
}