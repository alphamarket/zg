<?php
namespace zinux\zg\vendors\creators;
/**
 * Description of appRoutes
 *
 * @author dariush
 */
class appRoutes extends \zinux\zg\baseZg
{
    public function __construct(\zinux\zg\vendors\item $application, \zinux\zg\vendors\item $appRoutes, $project_path = ".")
    {
        $appRoutes->name = preg_replace("#(\w+)routes$#i","$1", $appRoutes->name)."Routes";
        $ns = $this->convert_to_relative_path($appRoutes->path, $project_path);
        $this ->cout("Creating new application routes '", 0.5,  self::defColor, 0)
                ->cout($appRoutes->name, 0, self::yellow, 0)
                ->cout("' at '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("'.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($appRoutes->path)))
            mkdir(dirname($appRoutes->path), 0775);
        
        $mbc ="<?php
namespace $ns;
/**
* The {$application->name}'s router
*/
class {$appRoutes->name} extends \\zinux\\kernel\\routing\\routerBootstrap
{
    public function Fetch()
    {
        /**
         * Route Example For This:
         *      /foo/1234/edit/what?so=ever => /foo/edit/1234/what?so=ever
         */
        #\$this->addRoute(\"/foo/$1/edit$2\",\"/foo/edit/$1$2\");
        /**
         * Route Example For This:
         *      /foo/1234/delete/what?so=ever => /foo/delete/1234/what?so=ever
         */
        #\$this->addRoute(\"/foo/$1/delete$2\",\"/foo/delete/$1$2\");
    }
}";
        file_put_contents($appRoutes->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $appRoutes->parent = $application;
        $s->project->routes[strtolower($appRoutes->name)]  = $appRoutes;
        $this->SaveStatus($s);
    }
}

?>
