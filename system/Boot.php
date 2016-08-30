<?php

/* function __autoload($class_name)
  //{
  //    require_once $class_name . '.php';
  //}
 */

class Boot
{

    public static function run()
    {
        self::init();
        self::autoload();
        self::dispatch();
    }

    private static function init()
    {
        // Define path constants

        define("DS", DIRECTORY_SEPARATOR);

        define("ROOT", getcwd() . DS);
        // define('ROOT', dirname(__FILE__));

        define("APP_PATH", ROOT . 'app' . DS);

        define("SYSTEM_PATH", ROOT . "system" . DS);

        define("PUBLIC_PATH", ROOT . "public" . DS);

        define("NAMESPACE_CONTROLLERS", "app\controllers\\");


        define("CONFIG_PATH", APP_PATH . "config" . DS);

        define("CONTROLLER_PATH", APP_PATH . "controllers" . DS);

        define("MODEL_PATH", APP_PATH . "models" . DS);

        define("VIEW_PATH", APP_PATH . "views" . DS);


        define("CORE_PATH", SYSTEM_PATH . "core" . DS);

        define('DB_PATH', SYSTEM_PATH . "database" . DS);

        define("LIB_PATH", SYSTEM_PATH . "libraries" . DS);

        define("HELPER_PATH", SYSTEM_PATH . "helpers" . DS);

        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);


        // Define platform, controller, action, for example:
        // index.php?p=admin&c=Goods&a=add

        define("PLATFORM", isset($_REQUEST['p']) ? $_REQUEST['p'] : 'home');

        define("DEFAULTCONTROLLER", 'Usuarios');

        define("DEFAULTMETHOD", 'index');


        define("CURR_CONTROLLER_PATH", CONTROLLER_PATH);

        define("CURR_VIEW_PATH", VIEW_PATH . PLATFORM . DS);

        session_start();
    }

    // Autoloading
    
    private static function autoload()
    {
        // Register the autoload of classes
        spl_autoload_register(function($class) {
            $class = explode("\\", $class);
            $class = end($class);
            $class = str_replace('\\', '/', $class);


            $paths = array(
                CORE_PATH,
                CONTROLLER_PATH,
                MODEL_PATH,
                HELPER_PATH,
                VIEW_PATH
            );


            foreach ($paths as $path) {

                if(file_exists($path . $class . '.php')){
                 require_once($path . $class . '.php');
                }
            }

        });
    }

    // Dispatcher

    private static function dispatch()
    {        
       // Get the URL and convert to array
        if (isset($_SERVER['REQUEST_URI']))
        {
            $url = explode("/", trim($_SERVER['REQUEST_URI']));
            array_shift($url);
        }

        // Parsing the data from REQUEST
        $controller = ($ctrl = array_shift($url)) ? $ctrl : DEFAULTCONTROLLER . "Controller";

        $method = ($mtd = array_shift($url)) ? $mtd : DEFAULTMETHOD . "Action";

        $args = (isset($url[0])) ? $url : array();

        // Get the Controller path to instanciate
        $pathController = APP_PATH . "controllers" . DS . $controller . ".php";


        // Is Controller File Exists Then ...
        if (file_exists($pathController))
        {
            // Include the Controller File
            require_once $pathController;

            // Join the Namespace and the name of controller to get the instance
            $claseIntanciar = NAMESPACE_CONTROLLERS . $controller;

            // Create the object or Instance of the Controller
            $object = new $claseIntanciar;

            // If have arguments then ...
            if (!empty($args))
            {
                // Call the data passed with the arguments
                call_user_func_array(array($object, $method), $args);
            } else {
                // If isn't have arguments, then call the controller and method ...
                call_user_func(array($object, $method));
            }

        } else {
            // If isn't find the Class or the file, then catch Error
            throw new Exception($controller .' -- Controller not found');
        }


    }

}