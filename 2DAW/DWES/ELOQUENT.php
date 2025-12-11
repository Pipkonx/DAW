<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('prueba', function () {
    // return 'Hola desde la ruta de prueba';
    // ! CREAR NUEVO REGISTRTO
    // $post = new Post;

    // $post->title = 'Titulo de prueba 1';
    // $post->content = 'Contendio de prueba 1';
    // $post->categoria = 'Categoria de prueba 1';

    // $post->save();

    // return $post;




    // $post = Post::find(1);

    // ! ACTUALIZAR REGISTRO
    // $post = Post::where('title', 'Titulo de prueba 1')->first();

    // $post->categoria = 'Desarrollo web';

    // // el save para guardar en la base de datos
    // $post->save();

    // return $post;


    //! LISTAR TODOS LOS POSTS
    // // ? el get para varios registros, el first para uno
    // //? Con take podemos traer solo x registros
    //     // $posts = Post::where('id', '>=', '2')->get();
        $posts = Post::orderBy('id','asc')->select('id','title','categoria')->take(2)->get();
        return $posts;

    //! ELIMINAR UN REGISTRO
    // $post = Post::find(1);+
    // $post->delete();
    // return 'Eliminado correctamente';
});
