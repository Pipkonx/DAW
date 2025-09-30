# Define una variable global curso = "Python".
# Crea una función mostrar_curso() que imprima la variable global.
# Crea otra función cambiar_curso() que defina una variable local con el mismo nombre y muestre su valor.
# Ejecuta ambas funciones y observa la diferencia.

curso = "Python"


def mostrar_curso():
    print(curso)


def cambiar_curso():
    curso = "Java"
    print(curso)

mostrar_curso()
cambiar_curso()