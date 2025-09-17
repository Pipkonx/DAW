package db;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.util.ArrayList;

import modelo.Clasificacion;
import modelo.Usuario;

public class DbMemorion extends Conectar {

	public DbMemorion() throws SQLException {
		super();
	}

	public Usuario getUser(String email, String passw) {
		try (PreparedStatement pstmt = this.con.prepareStatement(
				"SELECT * FROM usuarios WHERE email = ? AND password = ?")) {
			pstmt.setString(1, email.trim());
			pstmt.setString(2, passw.trim());

			try (ResultSet rs = pstmt.executeQuery()) {
				if (rs.next()) {
					String em = rs.getString("email");
					String pw = rs.getString("password");
					String nombre = rs.getString("nombre");
					return new Usuario(nombre, em, pw);
				} else {
					return null;
				}
			}
		} catch (SQLException e) {
			System.err.println("Error en autenticación: " + e.getMessage());
			e.printStackTrace();
			return null;
		}
	}

	public boolean newUser(Usuario user) {
		try (PreparedStatement pstmt = this.con.prepareStatement(
				"INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)")) {
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

	public ArrayList<Clasificacion> getClasificaciones() {
		// Obtener clasificacion de la base de datos
		ArrayList<Clasificacion> clasificaciones = getClasificacionesFromDB();

		// Ordenar por tiempo
		if (clasificaciones != null && !clasificaciones.isEmpty()) {
			clasificaciones.sort((c1, c2) -> Long.compare(c1.getTiempo(), c2.getTiempo()));
		}

		return clasificaciones;
	}

	private ArrayList<Clasificacion> getClasificacionesFromDB() {
		ArrayList<Clasificacion> clasificaciones = new ArrayList<>();
		try {
			// Consulta modificada para usar la nueva estructura de la tabla
			String sql = "SELECT u.email, u.nombre, p.tiempo, p.Fecha FROM usuarios u " +
					"INNER JOIN puntuaciones p ON u.nombre = p.nombre " +
					"ORDER BY p.tiempo ASC LIMIT 10";

			PreparedStatement pstmt = this.con.prepareStatement(sql);
			ResultSet rs = pstmt.executeQuery();

			boolean hayResultados = false;
			while (rs.next()) {
				hayResultados = true;
				String email = rs.getString("email");
				String nombre = rs.getString("nombre");
				long tiempo = rs.getLong("tiempo");

				// ponemos bien la fecha
				Timestamp fechaTimestamp = rs.getTimestamp("Fecha");
				LocalDateTime fecha = fechaTimestamp.toLocalDateTime();

				clasificaciones.add(new Clasificacion(email, nombre, tiempo, fecha));
			}

			if (!hayResultados) {
				System.out.println("No se encontraron clasificaciones en la base de datos");

				// comprobamos que la tabla tenga datos
				String checkTable = "SELECT COUNT(*) as count FROM puntuaciones";
				PreparedStatement checkStmt = this.con.prepareStatement(checkTable);
				ResultSet checkRs = checkStmt.executeQuery();

				if (checkRs.next()) {
					int count = checkRs.getInt("count");
					System.out.println("La tabla 'puntuaciones' existe y contiene " + count + " registros");
				}
			}

			return clasificaciones;
		} catch (SQLException e) {
			System.err.println("Error al recuperar las puntuaciones: " + e.getMessage());
			e.printStackTrace();
			return new ArrayList<>(); // Devolver lista vacía en lugar de null
		}
	}

	public boolean guardarClasificacion(Clasificacion clasificacion) {
		// Guardamos directamente en la base de datos
		return guardarClasificacionEnDB(clasificacion);
	}

	private boolean guardarClasificacionEnDB(Clasificacion clasificacion) {
		try (PreparedStatement stmt = this.con.prepareStatement(
				"INSERT INTO puntuaciones (nombre, tiempo, Fecha) VALUES (?, ?, NOW())")) {
			stmt.setString(1, clasificacion.getNombreJugador());
			stmt.setLong(2, clasificacion.getTiempo());

			int filasAfectadas = stmt.executeUpdate();
			return filasAfectadas > 0;
		} catch (SQLException e) {
			return false;
		}
	}
}
