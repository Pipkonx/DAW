package db;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import modelo.Usuario;

public class DbTeatro extends Conectar {

	Statement stm;

	public DbTeatro() throws SQLException {
		super();
		this.stm = null;
	}

	public Usuario getUser(String email, String passw) {
		try {
			String sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
			PreparedStatement pstmt = this.con.prepareStatement(sql);
			pstmt.setString(1, email.trim());
			pstmt.setString(2, passw.trim());

			ResultSet rs = pstmt.executeQuery();

			if (rs.next()) {
				String em = rs.getString("email");
				String pw = rs.getString("password");
				String nombre = rs.getString("nombre");
				return new Usuario(nombre, em, pw);
			} else {
				return null;
			}
		} catch (SQLException e) {
			System.err.println("Error en autenticación: " + e.getMessage());
			e.printStackTrace(); // Para ver el error completo
			return null;
		}
	}


	public boolean newUser(Usuario user) {
		try {
			String insertar = "INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)";
			PreparedStatement pstmt = this.con.prepareStatement(insertar);
			pstmt.setString(1, user.getEmail());
			pstmt.setString(2, user.getPassword());
			pstmt.setString(3, user.getName());
			pstmt.execute();
			return true;
		} catch (SQLException e) {
			e.printStackTrace();
			return false;
		}
	}
}
