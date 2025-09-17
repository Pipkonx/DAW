package db;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;

import modelo.Mensaje;
import modelo.Usuario;

import java.util.ArrayList;

public class DbCorreo extends Conectar {

	Statement stm;

	public DbCorreo() throws SQLException {
		super();
		this.stm = null;
	}

	public Usuario getUser(String email, String passw) {
		try {
			String sql = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(sql);
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

	private boolean getUserByEmail(String email) {
		try {
			String sql = "SELECT * FROM usuarios WHERE email = ?";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(sql);
			pstmt.setString(1, email);
			ResultSet rs = pstmt.executeQuery();
			return rs.next();
		} catch (SQLException e) {
			return false;
		}
	}

	public boolean sendMessage(Mensaje m) {
		if (!this.getUserByEmail(m.getReceptor()))
			return false;

		try {
			String insertar = "INSERT INTO mensajes VALUES (?, ?, ?, ?)";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(insertar);
			pstmt.setString(1, m.getEmisor());
			pstmt.setString(2, m.getReceptor());
			pstmt.setString(3, m.getMensaje());

			if (m.getFecha() == null) {
				pstmt.setTimestamp(4, new Timestamp(System.currentTimeMillis()));
			} else {
				pstmt.setTimestamp(4, m.getFecha());
			}

			pstmt.execute();
			System.out.println("Mensaje enviado correctamente a: " + m.getReceptor());
			return true;
		} catch (SQLException e) {
			System.err.println("Error al enviar mensaje: " + e.getMessage());
			e.printStackTrace();
			return false;
		}
	}

	public boolean newUser(Usuario user) {
		try {
			String insertar = "INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(insertar);
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

	public ArrayList<Mensaje> getSentMessages(String emisor) {
		ArrayList<Mensaje> mensajes = new ArrayList<Mensaje>();
		try {
			System.out.println("Buscando mensajes enviados por: " + emisor);
			String sql = "SELECT * FROM mensajes WHERE emisor = ? ORDER BY fecha DESC";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(sql);
			pstmt.setString(1, emisor);
			ResultSet rs = pstmt.executeQuery();
			while (rs.next()) {
				String em = rs.getString("emisor");
				String recep = rs.getString("receptor");
				String texto = rs.getString("mensaje");
				Timestamp fecha = rs.getTimestamp("fecha");
				mensajes.add(new Mensaje(em, recep, texto, fecha));
				System.out.println("Mensaje enviado encontrado: " + texto);
			}
			System.out.println("Total mensajes enviados encontrados: " + mensajes.size());
			return mensajes;
		} catch (SQLException e) {
			System.err.println("Error al obtener mensajes enviados: " + e.getMessage());
			e.printStackTrace();
			return mensajes;
		}
	}

	public ArrayList<Mensaje> getMessages(String receptor) {
		ArrayList<Mensaje> mensajes = new ArrayList<Mensaje>();
		try {
			System.out.println("Buscando mensajes recibidos por: " + receptor);
			String sql = "SELECT * FROM mensajes WHERE receptor = ? ORDER BY fecha DESC";
			java.sql.PreparedStatement pstmt = this.con.prepareStatement(sql);
			pstmt.setString(1, receptor);
			ResultSet rs = pstmt.executeQuery();
			while (rs.next()) {
				String emisor = rs.getString("emisor");
				String recep = rs.getString("receptor");
				String texto = rs.getString("mensaje");
				Timestamp fecha = rs.getTimestamp("fecha");
				mensajes.add(new Mensaje(emisor, recep, texto, fecha));
				System.out.println("Mensaje recibido encontrado: " + texto);
			}
			System.out.println("Total mensajes recibidos encontrados: " + mensajes.size());
			return mensajes;
		} catch (SQLException e) {
			System.err.println("Error al obtener mensajes recibidos: " + e.getMessage());
			e.printStackTrace();
			return mensajes;
		}
	}
}
