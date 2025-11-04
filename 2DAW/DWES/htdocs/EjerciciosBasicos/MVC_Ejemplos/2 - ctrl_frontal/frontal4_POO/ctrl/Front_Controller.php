<?php
/**
 * Description of Front_Controller
 *
 * @author santi
 */
class Front_Controller {
    
    const CTRL='c';
    const ACTION='a';
    
    static private $instance=NULL; 
    
    protected $defaultController='';
    protected $defaultAction='';
    
    /**
     * Inicializamos controlador frontal indicando cual es el controlador
     * que arranca
     * @param type $defaultController
     * @param type $defaultAction
     */
    private function __construct($defaultController, $defaultAction='Index') {
        $this->defaultController=$defaultController;
        $this->defaultAction=$defaultAction;
    }
    
    /**
     * Patrón Singleton
     * 
     * @param type $defaultController
     * @param type $defaultAction
     */
    public static function getInstance($defaultController, $defaultAction='Index')
    {
        self::$instance=new self($defaultController, $defaultAction);
        return self::$instance;
    }
    
    /**
     * Selecciona la acción a realizar
     */
    public function Run()
    {
        // Cuerpo del controlador frontal
        $ctrlName = isset($_GET[self::CTRL]) ? $_GET[self::CTRL] : $this->defaultController;
        $accName = isset($_GET[self::ACTION]) ? $_GET[self::ACTION] : $this->defaultAction;

        // Nombre del fichero a incluir
        $ctrl_file=CTRL_PATH.$ctrlName.'.php';
        if (file_exists($ctrl_file))
        {
            include($ctrl_file);
            
            $ctrl=new $ctrlName();
            $ctrl->{$accName}();
        }
        else
        {   // Error 404
            $this->Error404('<p>No existe el fichero :$ctrl_file</p>');
        }
    }

    /**
     * Página de error
     * @param type $msg
     */
    public function Error404($msg)
    {
        // Error 404
         VerVista('plantilla/layout', array(
             'titulo'=>'Acción inexistente',
             'menu'=>CargaVista('plantilla/menu'),
             'cuerpo'=>'<div>No está definida la acción indicada</div>',
         ));
    }

    /**
     * 
     * @param type $ctrl
     * @param type $action
     * @param type $parameters
     * @return string
     */
    static public function MakeURL($ctrl, $action='', $parameters='')
    {
        if ($action=='')
        {
            $action=self::$instance->defaultAction;
        }
        $url="?".self::CTRL."=$ctrl&".self::ACTION."=$action";
        if ($parameters)
        {
            $url.="&".$parameters;
        }
        return $url;
    }
    
}
