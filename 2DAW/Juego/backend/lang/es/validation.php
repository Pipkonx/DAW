<?php

return [
    'accepted' => 'Debes aceptar este campo.',
    'confirmed' => 'Las contraseñas no coinciden, por favor escríbelas igual.',
    'email' => 'Escribe un correo electrónico que sea válido (ejemplo@correo.com).',
    'exists' => 'Lo que has seleccionado no es válido.',
    'max' => [
        'numeric' => 'Este número no puede ser mayor que :max.',
        'file' => 'El archivo es demasiado grande (máximo :max KB).',
        'string' => 'Este texto es demasiado largo (máximo :max letras).',
        'array' => 'No puedes elegir más de :max elementos.',
    ],
    'min' => [
        'numeric' => 'Este número debe ser al menos :min.',
        'file' => 'El archivo es demasiado pequeño (mínimo :min KB).',
        'string' => 'La contraseña es muy corta, escribe al menos :min letras.',
        'array' => 'Debes elegir al menos :min elementos.',
    ],
    'required' => 'Este campo es obligatorio, no puedes dejarlo vacío.',
    'unique' => 'Este correo ya está registrado, intenta con otro o inicia sesión.',
    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
    ],
];
