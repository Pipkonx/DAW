package controlador;

import java.sql.SQLException;
import java.util.ArrayList;

import db.DbMemorion;
import modelo.Clasificacion;
import modelo.Usuario;
import vista.JuegoPane;
import vista.LoginPane;
import vista.MainPane;
import vista.RegisterPane;

public class Controlador {
    private MainPane mainPane;
    private DbMemorion dbMemorion;
    private Usuario usuarioActual;
    private LoginPane loginPane;
    private RegisterPane registerPane;
    private JuegoPane juegoPane;

    public Controlador() throws SQLException {
        this.dbMemorion = new DbMemorion();
        this.loginPane = new LoginPane();
        this.registerPane = new RegisterPane();
        this.juegoPane = new JuegoPane(this);

        // Configurar botón de inicio de sesión
        loginPane.getBtn().setOnAction(e -> {
            String email = loginPane.getTxtEmail().getText();
            String password = loginPane.getTxtPassword().getText();

            boolean loginExitoso = login(email, password);

            if (!loginExitoso) {
                loginPane.getLblStatus().setText("Credenciales inválidas. Inténtalo de nuevo.");
            }
        });

        loginPane.getBtnRegistrarlo().setOnAction(e -> {
            loginPane.getScene().setRoot(registerPane);
        });

        registerPane.getBtnRegistrar().setOnAction(e -> {
            String nombre = registerPane.getTxtNombre().getText();
            String email = registerPane.getTxtEmail().getText();
            String password = registerPane.getTxtPassword().getText();

            // Comprobar
            if (nombre.isEmpty() || email.isEmpty() || password.isEmpty()) {
                registerPane.getLblStatus().setText("Todos los campos son obligatorios");
                return;
            }

            // Crear nuevo usuario
            Usuario nuevoUsuario = new Usuario(nombre, email, password);
            boolean registrado = registrarUsuario(nuevoUsuario);

            if (registrado) {
                // Volvemos a la pantala de login
                registerPane.getLblStatus().setText("Usuario registrado correctamente");
                registerPane.getScene().setRoot(loginPane);
                loginPane.getLblStatus().setText("Usuario registrado. Inicia sesión para continuar.");
            } else {
                registerPane.getLblStatus().setText("Error al registrar usuario. El email ya podría estar en uso.");
            }
        });

        registerPane.getBtnVolver().setOnAction(e -> {
            registerPane.getScene().setRoot(loginPane);
        });
    }

    public Usuario autenticarUsuario(String email, String password) {
        return dbMemorion.getUser(email, password);
    }

    public boolean registrarUsuario(Usuario usuario) {
        return dbMemorion.newUser(usuario);
    }

    public Usuario getUsuarioActual() {
        return usuarioActual;
    }

    public LoginPane getLoginPane() {
        return loginPane;
    }

    public boolean login(String email, String password) {
        Usuario usuario = autenticarUsuario(email, password);
        if (usuario != null) {
            this.usuarioActual = usuario;

            if (mainPane == null) {
                mainPane = new MainPane();

                mainPane.getBtnJugar().setOnAction(e -> mostrarJuego());
                mainPane.getBtnSalir().setOnAction(e -> cerrarSesion());
                mainPane.getBtnLogs().setOnAction(e -> mostrarLogs());
            }

            mostrarRanking();
            loginPane.getScene().setRoot(mainPane);
            return true;
        }
        return false;
    }

    private void mostrarRanking() {
        try {
            ArrayList<Clasificacion> clasificaciones = dbMemorion.getClasificaciones();
            if (clasificaciones != null) {
                mainPane.mostrarRanking(clasificaciones);
            }
        } catch (Exception e) {
        }
    }

    private void mostrarLogs() {
        try {
            // Obtener las clasificaciones del usuario actual
            ArrayList<Clasificacion> todasClasificaciones = dbMemorion.getClasificaciones();
            ArrayList<Clasificacion> clasificacionesUsuario = new ArrayList<>();

            if (todasClasificaciones != null) {
                for (Clasificacion c : todasClasificaciones) {
                    if (c.getEmail().equals(usuarioActual.getEmail())) {
                        clasificacionesUsuario.add(c);
                    }
                }
                mainPane.mostrarLogs(clasificacionesUsuario);
            }
        } catch (Exception e) {
        }
    }

    private void mostrarJuego() {
        try {
            if (juegoPane != null && mainPane != null && mainPane.getScene() != null) {
                juegoPane.iniciarJuego();
                mainPane.getScene().setRoot(juegoPane);
            } else {
                throw new IllegalStateException("Error al inicializar el juego");
            }
        } catch (Exception e) {
        }
    }

    public void guardarTiempo(long tiempo) { // Cambiar de int a long
        try {
            Clasificacion clasificacion = new Clasificacion(
                    usuarioActual.getEmail(),
                    usuarioActual.getName(),
                    tiempo);

            boolean guardado = dbMemorion.guardarClasificacion(clasificacion);
            if (guardado) {
                mostrarRanking();
            }
        } catch (Exception e) {
        }
    }

    private void cerrarSesion() {
        usuarioActual = null;
        loginPane.getTxtEmail().clear();
        loginPane.getTxtPassword().clear();
        mainPane.getScene().setRoot(loginPane);
    }

    public void mostrarMainPane() {
        if (juegoPane != null && juegoPane.getScene() != null) {
            juegoPane.getScene().setRoot(mainPane);
            mostrarRanking();
        }
    }
}