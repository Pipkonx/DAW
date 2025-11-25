<?php

return [
    // Mensajes genéricos
    'required' => 'El campo :attribute es obligatorio.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'exists' => 'El valor seleccionado de :attribute no es válido.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'in' => 'El valor seleccionado de :attribute no es válido.',
    'confirmed' => 'La confirmación de :attribute no coincide.',

    // Reglas con parámetros
    'min' => [
        'numeric' => 'El campo :attribute debe ser como mínimo :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],

    // Nombres de atributos amigables
    'attributes' => [
        'categori-id' => 'categoría',
        'categoria' => 'categoría',
        'category_id' => 'categoría',
        'monto' => 'monto',
        'descripcion' => 'descripción',
        'fecha' => 'fecha',
        'tipo' => 'tipo',
        'name' => 'nombre',
        'current_password' => 'contraseña actual',
        'new_password' => 'nueva contraseña',
        'new_password_confirmation' => 'confirmación de la nueva contraseña',
    ],
];

