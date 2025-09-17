package accesoADatos;

public class AccesoDatosInterface {
    public static void main(String[] args) {
        //* newShow .- recibe la fecha de la nueva función y da de alta en la base de
        //* datos a todas las butacas, indicando que están libres.
        //* updateSeat .- Cambia una butaca de libre a ocupada y viceversa
        //* getAllSeatsByShow .- devuelve una lista con todas las butacas del show, que
        //* identificamos por la fecha de representación

        AccesoDatosInterface accesoDatos = new AccesoDatosInterface();
        //* ejemplo */
        // accesoDatos.newShow("2023-02-02");
        // accesoDatos.updateSeat(1, "2023-02-02", true);
        // accesoDatos.getAllSeatsByShow("2023-02-02");

    }

    private void newShow(String string) {
        throw new UnsupportedOperationException("No lo soporta.");
    }

    private void getAllSeatsByShow(String string) {
        throw new UnsupportedOperationException("No lo soporta.");
    }

    private void updateSeat(int i, String string, boolean b) {
        throw new UnsupportedOperationException("No lo soporta.");
    }
}
