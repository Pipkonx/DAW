import utilidades.matematicas
import utilidades.cadenas

def main():
    print("Bienvenido a la mini-herramienta")
    numero = int(input("Dime un número: "))
    texto = input("Dime un texto: ")

    print(f"\nNúmero de letras en el texto: {utilidades.cadenas.contar_letras(texto)}")
    print(f"Texto invertido: {utilidades.cadenas.invertir(texto)}")
    print(f"Suma: {utilidades.matematicas.suma(numero, 5)}")
    print(f"Resta: {utilidades.matematicas.resta(numero, 5)}")
    print(f"Multiplicación: {utilidades.matematicas.multiplicar(numero, 5)}")
    print(f"División: {utilidades.matematicas.dividir(numero, 5)}")

if __name__ == "__main__":
    main()
