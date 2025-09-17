package modelo;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.ArrayList;
import java.util.Random;
import java.util.Scanner;

public class Diccionario {
    private ArrayList<String> lista;

    public Diccionario() {
        File f = new File("src/main/java/modelo/palabras.txt");
        
        // * Con esto podemos saber la ruta del archivo
        // System.out.println(f.getAbsolutePath());

        try {
            Scanner dict = new Scanner(f);
            lista = new ArrayList<String>();
            while (dict.hasNext()) {
                lista.add(dict.next());
            }
            dict.close();
        } catch (FileNotFoundException e) {
            e.printStackTrace();
        }
    }

    // ! palabra aleatoria
    public String getRandomWord() {
        Random r = new Random();
        int ind = r.nextInt(this.lista.size());
        return this.lista.get(ind);
    }

}