Pasar de breeze a laravel ui porque es más sencillo

Examen santi

Procesar un formulario validando las entradas, mostrando los errores, hacer consultas en una tabla (crud), usar las relaciones que nos trae laravel

relaciones
validacion
controlador

nos dara dos tablas con datos relacionadas y tendremos que mostrar la lista , paginarla , filtrarla,

nos dara el modelo de workbench , el esquema de proyecto y nos pondra la imagen

crud usando todas las funciones de laravel y sus relaciones, nos da la base de datos

### Login register vista como admin y tablas y demas

Primer paso
```bash
composer create-project laravel/laravel:^12.0 proyecto-filament
```
En el env tenemos que tner lo siguiente
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_filament
DB_USERNAME=root
DB_PASSWORD=
```

Entramos en la carpeta de xamp php y buscamos el php.ini y descomentamos el intl y el zip
Y ahora ejecutamos lo siguiente
```bash
composer require filament/filament:3.x-dev -W --ignore-platform-reqs
```
Y ahora creamos los modelos y migraciones con lo siguiente
```bash
php artisan filament:install --panels
```
Nos preguntara varias cosas la rpimera la dejamos predeterminada a la segunda le damos que no

Hacemos ahora el migrate
```bash
php artisan migrate
```

Ahora creamos lo del usuario
```bash
php artisan make:filament-user
```

Ahora pasamos acrear el resource para tener el crud
```bash
php artisan make:filament-resource Product
```

Abre app/Providers/Filament/AdminPanelProvider.php.
Añade la función registration() dentro de panel():
```bash
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->login() // Ya debería estar
        ->registration() // <--- AÑADE ESTA LÍNEA
        ...
}
```

Ahora agregamos el starter kit de breezer
```bash
composer require laravel/breeze --dev -W --ignore-platform-reqs
php artisan breeze:install blade
php artisan migrate
```

Y por ultimo el
```bash
npm install
npm run dev
```

Todo esto era solo para el auth y tablas pero ahora viene el crud
```bash
php artisan make:filament-resource User
```
Y ya podríamos entrar en /login para acceder como un usuario o a /admin/login para las vistas como administrador

Si no se muestran es porque nos falta crear el modelo y la migracion
```bash
php artisan make:model Product -m
```
Ahora tendríamos que entrar a las migracion que acabamos de crear y pegar lo siguiente (xxxx_xx_xx_create_products_table.php)
```bash
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');          // Nombre del producto
        $table->text('description');     // Descripción
        $table->decimal('price', 10, 2); // Precio
        $table->timestamps();
    });
}
```

Y luego pegamos
```bash
php artisan migrate
```

Ahora entramos al modelo de producto en app model product
```bash
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Esto permite que todos los campos se puedan rellenar desde Filament
    protected $guarded = [];
}
```
Ahora vamos a poner los inputs del formulario Abre el archivo app/Filament/Resources/ProductResource.php.
Buscamos public static function form(Form $form): Form
```bash
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

// ...

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->label('Nombre del Producto')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Descripción')
                ->rows(3),

            TextInput::make('price')
                ->label('Precio')
                ->numeric()
                ->prefix('€') // O el símbolo que prefieras
                ->required(),
        ]);
}
```
Ahora para ver la lista funcion table
```bash
use Filament\Tables\Columns\TextColumn;

// ...

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('price')->money('eur')->sortable(),
            TextColumn::make('created_at')->label('Fecha')->dateTime(),
        ])
        // ... el resto del código (filters, actions, etc.)
}
```

Ahora para ver a los usuarios vamos a modificar el user resource
app/Filament/Resources/UserResource.php
```bash
use Filament\Forms\Components\TextInput;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->dehydrated(fn ($state) => filled($state)) // Solo guarda si escribes algo
                ->required(fn (string $context): bool => $context === 'create') // Obligatoria solo al crear
                ->maxLength(255),
        ]);
}
```
Misma dinámica configuramos la lista de la tabla
```bash
use Filament\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
        ]);
}
```

# Relaciones
## Creamos la migracion
```bash
php artisan make:migration add_user_id_to_products_table
```
Entramos a la migracion creada database/migrations/xxxx_add_user_id...
```bash
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
    });
}
```
Entramos a app/Models/Product.php
```bash
public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

# En app/Models/User.php
public function products()
{
    return $this->hasMany(\App\Models\Product::class);
}
```



Implementar el Select en ProductResource.php
```bash
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select; // No olvides este import arriba

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // 1. Selector de Usuario (Relación)
            Select::make('user_id')
                ->relationship('user', 'name') // 'user' es el nombre de la función en el modelo
                ->label('Asignar a Usuario')
                ->searchable()
                ->preload()
                ->required(),

            // 2. Campo Nombre
            TextInput::make('name')
                ->label('Nombre del Producto')
                ->required()
                ->maxLength(255),

            // 3. Campo Descripción
            Textarea::make('description')
                ->label('Descripción')
                ->rows(3),

            // 4. Campo Precio
            TextInput::make('price')
                ->label('Precio')
                ->numeric()
                ->prefix('€')
                ->required(),
        ]);
}
```


# Filtro
```bash
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;


public static function table(Table $table): Table
{
    return $table
        ->columns([
            // 1. Añadimos la columna de la relación (Muestra el nombre del Usuario)
            TextColumn::make('user.name')
                ->label('Propietario')
                ->sortable()
                ->searchable(),

            TextColumn::make('name')
                ->label('Producto')
                ->sortable()
                ->searchable(),

            TextColumn::make('price')
                ->label('Precio')
                ->money('eur')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Fecha')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            // 2. Añadimos el filtro por relación (Cumple el requisito de filtrar)
            SelectFilter::make('user_id')
                ->label('Filtrar por Propietario')
                ->relationship('user', 'name'),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            \Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}
```
## ¿Qué has logrado con esto para tu examen/ejercicio?

Relaciones: Has usado belongsTo y hasMany (Relaciones de Laravel).
CRUD completo: Puedes crear productos asociados a usuarios.
Filtrado: Tienes un botón de embudo en la tabla que permite filtrar productos por el usuario que los creó.
Paginación: Filament la incluye automáticamente al pie de la tabla (verás que pone "Showing 1 to 10...").
Validación: El método ->required() y ->maxLength() del formulario procesan las entradas y muestran errores automáticamente si el usuario se equivoca.

Prueba final: Ve a tu panel /admin/products, crea un producto nuevo y verás que ahora el primer campo es una lista desplegable con los nombres de tus usuarios. Guía de relaciones en Filament.

---

### Arreglar el login y register para que no se peleen breezer y filament, como usamos filament pasaré todo allí
Comentamos el bloque de dashboard y ponemos el siguiente

```bash
Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware(['auth', 'verified']);
```

Y ahora cambiamos los botones para que nos redirijan a /admin entramos a la vista de la vista de welcome y cambiamos

```bash
@if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/admin') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/admin/login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Log in
                    </a>

                    <a href="{{ url('/register') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Register
                    </a>
                @endauth
            </nav>
        @endif
```
