package vista;

import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.GridPane;

public class LoginPane extends GridPane {
    final private TextField txtEmail;
    final private PasswordField txtPassword;
    final private Label lblStatus;
    final private Button btn;
    final private Button btnRegistrarlo;
    final private Label lblCorreo;
    final private Label lblContra;

    public LoginPane() {
        this.setId("loginpane");
        
        this.lblStatus = new Label("Inicia sesión para continuar");
        this.lblCorreo = new Label("Correo electrónico:");
        this.txtEmail = new TextField();
        this.txtEmail.setPromptText("ejemplo@");
        
        this.lblContra = new Label("Contraseña:");
        this.txtPassword = new PasswordField();
        this.txtPassword.setPromptText("Contraseña");
        
        this.btn = new Button("Iniciar sesión");
        this.btnRegistrarlo = new Button("Registrarse");
        
        this.lblStatus.setId("lblStatus");
        this.btn.setId("btn");
        this.btnRegistrarlo.setId("btn");
        this.lblStatus.getStyleClass().add("status-info");
        
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
