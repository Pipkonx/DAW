package com.example.modelo;

public class Juego {

    public enum Nivel {
        FACIL(3),    // 3x3
        MEDIO(6),    // 6x6
        DIFICIL(12); // 12x12

        private final int size;

        Nivel(int size) {
            this.size = size;
        }

        public int getSize() {
            return size;
        }
    }

    private Nivel nivel;

    public Juego(Nivel nivel) {
        this.nivel = nivel;
    }

    public Nivel getNivel() {
        return nivel;
    }

    public int getTableroSize() {
        return nivel.getSize();
    }
}