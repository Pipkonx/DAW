<?php
include (LIB_PATH.'GestorErrores.php');
include (HELPERS_PATH.'form.php');
include (MODEL_PATH.'tareas.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tareas
 *
 * @author santi
 */
class Tareas {
    protected $model=NULL;
    protected $errores=NULL;
    
    public function __construct() {
        $this->model=new Tareas_Model();
        
        // El gestor solo sería necesario crearlo si editamos o insertamos
        // Inicializamos el gestor de errores que utilizaremos en la vista
        $this->errores=new GestorErrores(
                '<span style="color:red; background:#EEE; padding:.2em 1em; margin:1em">', '</span>');
    }
    
    /**
     * Vamos a la página de inicio igualmente
     */
    public function Index()
    {
        $this->Inicio();
    }
    
    /**
     * Muestra la página de Inicio
     */
    public function Inicio()
    {
        // En un controlador real esto haría más cosas
        $this->Ver('Página de inicio', CargaVista('inicio'));
    }
    
   /**
     * Muestra la página Pag1
     */
    public function Pag1()
    {
        // En un controlador real esto haría más cosas
        $this->Ver('Página 1ª', CargaVista('pag1'));
    }
    
    /**
     * Muestra la lista de tareas
     */
    public function Listar()
    {
        $tareas = $this->model->GetTareas();

        // En un planteamiento real puede que incluyesemos más cosas
        $this->Ver('Listado de tarea', CargaVista('listar', array('tareas'=>$tareas)));
    }
    
    
    public function Edit()
    {
        if (! isset($_GET['id']))
        {
            // No existe la tarea, error
            $this->Ver('Error en edición', 
                    CargaVista('edit_error', array(
                        'descripcion_error'=>'No existe la tarea seleccionada'
                        ))
            );
            return;
        }
        
         // Han indicado el id
        $id=$_GET['id'];


        if (! $_POST)
        {
            // Primera vez.
            // Leo el regitro y muestro los datos
            $tarea=$this->model->GetTarea($id);
            if (! $tarea )
            {
                // No existe la tarea, error
                $this->Ver('Error en edición', 
                        CargaVista('edit_error', array(
                            'descripcion_error'=>'No existe la tarea seleccionada'
                            ))
                );
                return;
            }
            else
            {
                // Mostramos los datos
                $this->Ver('Edición', CargaVista('edit', array(
                    'operacion'=>'Edición',
                    'tarea'=>$tarea, 
                    'errores'=>$this->errores))
                );            
            }
        }
         else {
             // Filtrar datos
             $this->FiltraCamposPost();

            // Creamos el objeto tarea que es el que se utiliza en el formulario
            // Lo creamos a partir de los datos recibidos del POST
            $tarea=array(
                'nombre'=>  VPost('nombre'),
                'prioridad'=>VPost('prioridad')
            );

            if ($this->errores->HayErrores())
            {
                // Mostrar ventana de nuevo
               $this->Ver('Edición', CargaVista('edit', array(
                   'operacion'=>'Edición', 
                   'tarea'=>$tarea, 
                   'errores'=>$this->errores)));            
            }
            else
            {
                // Guardamos la tarea
                $this->model->Update($id, $tarea);
                $this->Ver('Edición', "<p>Se ha guardado la tarea ....</p>");            
            }

        }
    }
    
    /**
     * Añade una nueva tarea
     * @return type
     */
    public function Add()
    {
        if (! $_POST)
        {
            // Primera vez.
            $tarea=array(
                'nombre'=>  '',
                'prioridad'=>''
            );
        }
        else 
        {
             // Filtrar datos
             $this->FiltraCamposPost();

            // Creamos el objeto tarea que es el que se utiliza en el formulario
            // Lo creamos a partir de los datos recibidos del POST
            $tarea=array(
                'nombre'=>  VPost('nombre'),
                'prioridad'=>VPost('prioridad')
            );

            if (! $this->errores->HayErrores())
            {
                // Guardamos la tarea y finalizamos
                $this->model->Add($tarea);
                $this->Ver('Insertar', "<p>Se ha guardado la tarea ....</p>"); 
                return;
            }
        }
        // Mostramos los datos
        $this->Ver('Añadir', CargaVista('edit', array(
            'operacion'=>'Insertar',
            'tarea'=>$tarea, 
            'errores'=>$this->errores))
        );            
        
    }
    
    /**
     * Muestra el resultado del controlador dentro de la plantilla
     * @param type $html
     */
    protected function Ver($titulo, $html)
    {
        MuestraVista('plantilla/layout', array(
            'titulo'=>$titulo,
            'menu'=>CargaVista('plantilla/menu'),
            'cuerpo'=>$html,
        ));
    }
    
    
    /**
     * Realiza el filtrado de campos y almacena los errores en el gestor de errores
     * @param GestorErrores $this->errores
     */
    function FiltraCamposPost()
    {
        // Filtramos el nombre
        if (VPost('nombre')=='')
        {
            $this->errores->AnotaError('nombre', 'Se debe introducir texto');
        } 
        else if ( strlen(VPost('nombre'))<5)
        {
            $this->errores->AnotaError('nombre', 'El nombre debe tener al menos 5 letras');
        }

        // Filtramos la prioridad
        $prioridad=VPost('prioridad');
        if ($prioridad=='')
        {
            $this->errores->AnotaError('prioridad', 'Se debe introducir texto');
        } 
        else if ( !is_numeric($prioridad) || ($prioridad<1 || $prioridad>5))
        {
            $this->errores->AnotaError('prioridad', 'La prioridad debe ser un número entre 1 y 5');
        }
    }    
}
