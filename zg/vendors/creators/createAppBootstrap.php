<?php
namespace zg\vendors\creators;
/**
 * app bootstrap creator
 */
class createAppBootstrap extends \zg\baseZg
{
    /**
     * ctor a new app bootstrap
     * @param \zg\vendors\item $application target application item
     * @param \zg\vendors\item $appBootstrap target bootstrap item
     * @param string $project_path project directory
     */
    public function __construct(\zg\vendors\item $application, \zg\vendors\item $appBootstrap, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($appBootstrap->path, $project_path);
        $this ->cout("Creating new application bootstrap '", 0.5,  self::getColor(self::defColor), 0)
                ->cout($appBootstrap->name, 0, self::getColor(self::yellow), 0)
                ->cout("' at '",0,self::getColor(self::defColor), 0)
                ->cout($ns, 0, self::getColor(self::yellow), 0)
                ->cout("'.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($appBootstrap->path)))
            mkdir(dirname($appBootstrap->path), 0775,1);
        
        $mbc = "<?php
namespace $ns;
/**
* The {$application->name}'s bootstrapper
*/
class {$appBootstrap->name} extends \\zinux\\kernel\\application\\applicationBootstrap
{
    public function PRE_CHECK(\\zinux\\kernel\\routing\\request  \$request)
    {
        /**
         * this is a pre-strap function use this on pre-bootstrap opt.
         * @param \\zinux\\kernel\\routing\\request \$request 
         */
    }
    
    public function POST_CHECK(\\zinux\\kernel\\routing\\request \$request)
    {
        /**
         * this is a post-strap function use this on post-bootstrap opt.
         * @param \\zinux\\kernel\\routing\\request \$request 
         */
    }
}";
        file_put_contents($appBootstrap->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $appBootstrap->parent = $application;
        $s->project->meta->app_path = $application->path."/".\trim(\str_replace($application->path, "", dirname($appBootstrap->path)),"/")."/";
        $s->project->bootstrap[strtolower($appBootstrap->name)]  = $appBootstrap;
        $this->SaveStatus($s);
    }
}

?>
