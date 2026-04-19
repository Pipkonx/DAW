from flask import Flask, jsonify, request

app = Flask(__name__)

# Lista en memoria para almacenar las tareas
# Cada tarea es un diccionario con id, título y estado
tasks = [
    {"id": 1, "title": "Estudiar Python", "status": "pendiente"},
    {"id": 2, "title": "Comprar comida", "status": "completado"}
]

# 1. GET /tasks: Devuelve todas las tareas
@app.route('/tasks', methods=['GET'])
def get_tasks():
    return jsonify(tasks), 200

# 2. POST /tasks: Agrega una nueva tarea
@app.route('/tasks', methods=['POST'])
def add_task():
    # Obtenemos los datos del JSON enviado por el cliente
    data = request.get_json()
    
    # Validación básica: debe tener 'title'
    if not data or 'title' not in data:
        return jsonify({"error": "Falta el título de la tarea"}), 400
    
    # Generamos un nuevo ID (basado en el máximo actual + 1)
    new_id = max([t['id'] for t in tasks], default=0) + 1
    
    new_task = {
        "id": new_id,
        "title": data['title'],
        "status": data.get('status', 'pendiente')  # Predeterminado 'pendiente'
    }
    
    tasks.append(new_task)
    return jsonify(new_task), 201

# 3. PUT /tasks/<id>: Actualiza el estado de una tarea
@app.route('/tasks/<int:task_id>', methods=['PUT'])
def update_task(task_id):
    # Buscamos la tarea por su ID
    task = next((t for t in tasks if t['id'] == task_id), None)
    
    if task is None:
        return jsonify({"error": "Tarea no encontrada"}), 404
    
    data = request.get_json()
    
    # Actualizamos solo los campos permitidos si se proporcionan
    if 'title' in data:
        task['title'] = data['title']
    if 'status' in data:
        task['status'] = data['status']
        
    return jsonify(task), 200

# 4. DELETE /tasks/<id>: Elimina una tarea
@app.route('/tasks/<int:task_id>', methods=['DELETE'])
def delete_task(task_id):
    global tasks
    # Buscamos si existe
    task = next((t for t in tasks if t['id'] == task_id), None)
    
    if task is None:
        return jsonify({"error": "Tarea no encontrada"}), 404
    
    # Filtramos la lista para quitar la tarea
    tasks = [t for t in tasks if t['id'] != task_id]
    
    return jsonify({"message": f"Tarea {task_id} eliminada correctamente"}), 200

# Inicio del servidor
if __name__ == '__main__':
    print("🚀 Servidor API de Tareas corriendo en http://127.0.0.1:5000")
    print("Endpoints disponibles:")
    print(" - GET    /tasks")
    print(" - POST   /tasks")
    print(" - PUT    /tasks/<id>")
    print(" - DELETE /tasks/<id>")
    app.run(debug=True)
