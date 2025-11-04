<?php

/**
 * Clase que me permitirá llevar un registro de los errores que se producen
 */
class GestorErrores {
    
    /**
     * Lista en la que guardamos los errores. Solo se podrá almacenar una descripción
     * por campo
     * @var array
     */
    private $errores=array();
    
    private $format_prefix;
    private $format_suffix;
    
    /**
     * Crea el gestor de errores y anota las etiquetas que se muestran antes
     * y después en caso de queramos salida formateada
     * @param type $format_prefix
     * @param type $format_sufix
     */
    public function __construct($format_prefix='', $format_sufix='') {
        $this->format_prefix=$format_prefix;
        $this->format_suffix=$format_sufix;
    }
    
    /**
     * Anota un error para un campo en nuestro gestor de errores
     * @param type $campo
     * @param type $descripcion
     */
    public function AnotaError($campo, $descripcion)
    {
        $this->errores[$campo]=$descripcion;
    }
    
    /**
     * Indica si hay errores
     * @return boolean
     */
    public function HayErrores()
    {
        return count($this->errores)>0;
    }
    
    /**
     * Indica si hay error en un campo
     * @return boolean
     */
    public function HayError($campo)
    {
        return isset($this->errores[$campo]);
    }    
    
    /**
     * Devuelve la descripción de error para un campo o una cadaena vacia si no
     * hay
     * @param type $campo
     * @return string
     */
    public function Error($campo)
    {
        if (isset($this->errores[$campo]))
        {
            return $this->errores[$campo];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Devuelve la descripción del error o cadena vacia si no hay
     * @param type $campo
     * @return string
     */
    public function ErrorFormateado($campo)
    {
        if ($this->HayError($campo))
        {
            return $this->format_prefix.$this->Error($campo).$this->format_suffix;
        }
        else
        {
            return '';
        }
    }
    
}
