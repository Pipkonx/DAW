package controlador;

import java.sql.SQLException;

import db.DbTeatro;
import modelo.Usuario;
import vista.LoginPane;
import vista.MainPane;
import vista.RegisterPane;


public class Controlador {
    final private DbTeatro dbCorreo;
    private MainPane mainPane;
    private Usuario usuarioActual;
    private LoginPane loginPane;
    private RegisterPane registerPane;

    public Controlador() throws SQLException {
        this.dbCorreo = new DbTeatro();
        this.loginPane = new LoginPane();
        this.registerPane = new RegisterPane();

        // Configurar botón de inicio de sesión
        loginPane.getBtn().setOnAction(e -> {
            String email = loginPane.getTxtEmail().getText();
            String password = loginPane.getTxtPassword().getText();
            usuarioActual = autenticarUsuario(email, password);

            System.out.println("Botón de inicio de sesión pulsado - Email: " + email + ", Contraseña: " + password);
            if (usuarioActual != null) {
                System.out.println("Autenticación exitosa para el usuario: " + usuarioActual.getEmail());
                iniciarAplicacionPrincipal();
            } else {
                System.out.println("Autenticación fallida - Credenciales inválidas");
                loginPane.getLblStatus().setText("Credenciales inválidas. Inténtalo de nuevo.");
            }
        });

        loginPane.getBtnRegistrarlo().setOnAction(e -> {
            loginPane.getScene().setRoot(registerPane);
        });


        // * El registro
        registerPane.getBtnRegistrar().setOnAction(e -> {
            String nombre = registerPane.getTxtNombre().getText();
            String email = registerPane.getTxtEmail().getText();
            String password = registerPane.getTxtPassword().getText();
            
            // Crear nuevo usuario
            Usuario nuevoUsuario = new Usuario(nombre, email, password);
            boolean registrado = registrarUsuario(nuevoUsuario);

            if (registrado) {
                // Volver a la pantalla de login
                registerPane.getScene().setRoot(loginPane);
                loginPane.getLblStatus().setText("Usuario registrado. Inicia sesión para continuar.");
            }
        });

        registerPane.getBtnVolver().setOnAction(e -> {
            registerPane.getScene().setRoot(loginPane);
        });

        mainPane.getBtnCompraEntrada().setOnAction(e -> {
            mainPane.getScene().setRoot(loginPane);
        });
    }

    private void iniciarAplicacionPrincipal() {
        mainPane = new MainPane();

        // Configurar boton de cerrar sesion
        mainPane.getBtnCerrarSesion().setOnAction(e -> {
            usuarioActual = null;
            loginPane.getTxtEmail().clear();
            loginPane.getTxtPassword().clear();
            mainPane.getScene().setRoot(loginPane);
        });

        loginPane.getScene().setRoot(mainPane);
    }

    public Usuario autenticarUsuario(String email, String password) {
        return dbCorreo.getUser(email, password);
    }

    public boolean registrarUsuario(Usuario usuario) {
        return dbCorreo.newUser(usuario);
    }

    // asientos con botones


    //todo get y sets

    public Usuario getUsuarioActual() {
        return usuarioActual;
    }

    public LoginPane getLoginPane() {
        return loginPane;
    }
}
