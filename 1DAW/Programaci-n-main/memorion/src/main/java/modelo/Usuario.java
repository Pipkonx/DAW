package modelo;

public class Usuario {

	private String name;
	private String email;
	private String password;

	public Usuario(String name, String email, String pw) {
		this.name = name.toUpperCase();
		this.email = email;
		this.password = pw;
	}

	public String getName() {
		return this.name;
	}

	public String getEmail() {
		return this.email;
	}

	public void setEmail(String email) {
		this.email = email;
	}

	@Override
	public String toString() {
		return name + ", " + this.email + " password = " + this.password;
	}

	public String getPassword() {
		return password;
	}

	public void setPassword(String password) {
		this.password = password;
	}
}