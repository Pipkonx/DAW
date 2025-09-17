package db;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Conectar {
    public Connection con = null;

    public Conectar() throws SQLException {

        String db = "correo";
        String password = "";
        String usuario = "root";
        String url = "jdbc:mysql://localhost:3306/" + db;

        con = DriverManager.getConnection(url, usuario, password);
    }

    // Método para cerrar la conexión
    public void cerrarConexion() {
        if (con != null) {
            try {
                con.close();
                System.out.println("Conexión cerrada correctamente");
            } catch (SQLException e) {
                System.err.println("Error al cerrar la conexión: " + e.getMessage());
            }
        }
    }
}
