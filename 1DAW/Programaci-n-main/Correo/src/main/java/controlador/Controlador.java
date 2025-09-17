package controlador;

import java.sql.SQLException;
import java.util.ArrayList;

import db.DbCorreo;
import modelo.Mensaje;
import modelo.Usuario;
import vista.LoginPane;
import vista.MainPane;
import vista.RegisterPane;

public class Controlador {
    private MainPane mainPane;
    private DbCorreo dbCorreo;
    private Usuario usuarioActual;
    private LoginPane loginPane;
    private RegisterPane registerPane;

    public Controlador() throws SQLException {
        this.dbCorreo = new DbCorreo();
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
        
        registerPane.getBtnRegistrar().setOnAction(e -> {
            String nombre = registerPane.getTxtNombre().getText();
            String email = registerPane.getTxtEmail().getText();
            String password = registerPane.getTxtPassword().getText();
            String confirmPassword = registerPane.getTxtConfirmPassword().getText();
            
            // Comprobar
            if (nombre.isEmpty() || email.isEmpty() || password.isEmpty()) {
                registerPane.getLblStatus().setText("Todos los campos son obligatorios");
                return;
            }
            
            if (!password.equals(confirmPassword)) {
                registerPane.getLblStatus().setText("Las contraseñas no coinciden");
                return;
            }
            
            // Crear nuevo usuario
            Usuario nuevoUsuario = new Usuario(nombre, email, password);
            boolean registrado = registrarUsuario(nuevoUsuario);
            
            if (registrado) {
                // Volver a la pantalla de login
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
    
    private void iniciarAplicacionPrincipal() {
        mainPane = new MainPane();
        mainPane.getBtnRecibidos().setOnAction(e -> mostrarMensajesRecibidos());
        mainPane.getBtnEnviados().setOnAction(e -> mostrarMensajesEnviados());
        
        // envios de mensajes
        mainPane.getBtnEnviar().setOnAction(e -> {
            String destinatario = mainPane.getTxtDestinatario().getText();
            String contenido = mainPane.getTxtContenido().getText();
            
            if (destinatario.isEmpty() || contenido.isEmpty()) {
                mainPane.mostrarMensajeError("El destinatario y el contenido no pueden estar vacíos");
                return;
            }
            
            boolean enviado = enviarMensaje(destinatario, contenido);
            if (enviado) {
                mainPane.mostrarMensajeExito("Mensaje enviado correctamente");
                mainPane.limpiarCamposEnvio();
                if (mainPane.esVistaEnviadosActiva()) {
                    mostrarMensajesEnviados();
                }
            } else {
                mainPane.mostrarMensajeError("Error al enviar el mensaje. Verifica que el destinatario exista.");
            }
        });
        
        // Configurar botón de cerrar sesión
        mainPane.getBtnCerrarSesion().setOnAction(e -> {
            usuarioActual = null;
            loginPane.getTxtEmail().clear();
            loginPane.getTxtPassword().clear();
            mainPane.getScene().setRoot(loginPane);
        });
        
        mostrarMensajesRecibidos();
        
        loginPane.getScene().setRoot(mainPane);
    }
    
    private void mostrarMensajesRecibidos() {
        ArrayList<Mensaje> mensajes = getMensajesRecibidos();
        mainPane.mostrarMensajes(mensajes, "Bandeja de entrada - " + usuarioActual.getEmail());
    }
    
    private void mostrarMensajesEnviados() {
        ArrayList<Mensaje> mensajes = getMensajesEnviados();
        mainPane.mostrarMensajes(mensajes, "Mensajes enviados - " + usuarioActual.getEmail());
    }

    public Usuario autenticarUsuario(String email, String password) {
        return dbCorreo.getUser(email, password);
    }

    public boolean registrarUsuario(Usuario usuario) {
        return dbCorreo.newUser(usuario);
    }

    public ArrayList<Mensaje> getMensajesRecibidos() {
        ArrayList<Mensaje> mensajesRecibidos = dbCorreo.getMessages(usuarioActual.getEmail());
        if (mensajesRecibidos != null && !mensajesRecibidos.isEmpty()) {
            mensajesRecibidos.sort((m1, m2) -> m2.getFecha().compareTo(m1.getFecha()));
        }
        return mensajesRecibidos;
    }

    public ArrayList<Mensaje> getMensajesEnviados() {
        ArrayList<Mensaje> mensajesEnviados = dbCorreo.getSentMessages(usuarioActual.getEmail());
        if (mensajesEnviados != null && !mensajesEnviados.isEmpty()) {
            mensajesEnviados.sort((m1, m2) -> m2.getFecha().compareTo(m1.getFecha()));
        }
        return mensajesEnviados;
    }

    public boolean enviarMensaje(String destinatario, String contenido) {
        Mensaje mensaje = new Mensaje(usuarioActual.getEmail(), destinatario, contenido);
        return dbCorreo.sendMessage(mensaje);
    }

    public Usuario getUsuarioActual() {
        return usuarioActual;
    }
    
    public LoginPane getLoginPane() {
        return loginPane;
    }
}
