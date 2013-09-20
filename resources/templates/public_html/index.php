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
            ->SetRouterBootstrap(new \application\appRoutes)
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
    /**
     * You can redirect this exception to a controller e.g /error
     */
    echo "<legend>Oops!</legend>";
    echo "<p>Error happened ...</p>";
    echo "<p><b>Message: </b></p><p>{$e->getMessage()}</p>";
    echo "<p><b>Stack Trace: </b></p><pre>".preg_replace(PHP_EOL, "<br />", $e->getTraceAsString())."</pre>";
}
