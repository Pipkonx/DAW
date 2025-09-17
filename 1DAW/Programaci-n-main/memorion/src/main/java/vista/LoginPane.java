package vista;

import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.GridPane;

public class LoginPane extends GridPane {
    private TextField txtEmail;
    private PasswordField txtPassword;
    private Label lblStatus;
    private Button btn;
    private Button btnRegistrarlo;
    private Label lblCorreo;
    private Label lblContra;

    public LoginPane() {
        this.lblStatus = new Label("Inicia sesión para continuar");
        this.lblCorreo = new Label("Correo electrónico:");
        this.txtEmail = new TextField();
        this.txtEmail.setPromptText("ejemplo@");
        
        this.lblContra = new Label("Contraseña:");
        this.txtPassword = new PasswordField();
        this.txtPassword.setPromptText("Contraseña");
        
        this.btn = new Button("Iniciar sesión");
        this.btnRegistrarlo = new Button("Registrarse");
        
        this.setId("LoginPane");
        this.lblStatus.setId("tituloLogin");
        this.btn.setId("btn");
        this.btnRegistrarlo.setId("btn");
        
        this.add(lblStatus, 0, 2, 2, 1);
        this.add(lblCorreo, 0, 3);
        this.add(txtEmail, 1, 3);
        this.add(lblContra, 0, 4);
        this.add(txtPassword, 1, 4);
        this.add(btn, 0, 5);
        this.add(btnRegistrarlo, 1, 5);
        
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

    public Button getBtn() {
        return btn;
    }

    public Button getBtnRegistrarlo() {
        return btnRegistrarlo;
    }
}
