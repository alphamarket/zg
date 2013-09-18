<?php    
    ini_set('display_errors','On');
    
    error_reporting(E_ALL);
    
    session_start();

    defined('RUNNING_ENV') || define('RUNNING_ENV', 'DEVELOPMENT');

    defined('DEMO_ROOT_SITE') || define('DEMO_ROOT_SITE', '/');

    require_once '../zinux/baseZinux.php';
try
{
    # create an application with given module directory
    $app = new \zinux\kernel\application\application('../modules');
    # process the application instance
    $app 
            # setting cache directory
            ->SetCacheDirectory("../cache")
            # setting router's bootstrap which will route /note/:id:/edit => /note/edit/:id:
            ->SetRouterBootstrap(new \application\someRoutes)
            # set application's bootstrap 
            ->SetBootstrap(new application\appBootstrap)
            # init the application's optz.
            ->Startup()
            # run the application 
            ->Run()
            # shutdown the application
            ->Shutdown();
}
# catch any thing from application
catch(Exception $e)
{
    # serialize it!
    $_SESSION['exception'] = serialize($e);
    # relocate to error controller!
    header("location: ".DEMO_ROOT_SITE."error");
    # exit
    exit;
}
