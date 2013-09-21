<?php
namespace zinux\zg\vendor\removers;
/**
 * Description of removemoduleBootstrap
 *
 * @author dariush
 */
class appBootstrap extends \zinux\zg\baseZg
{
    public function __construct(\zinux\zg\vendor\item $application, \zinux\zg\vendor\item $appBootstrap, $project_path = ".")
    {
        $appBootstrap->name = preg_replace("#(\w+)bootstrap$#i","$1", $appBootstrap->name)."Bootstrap";
        $ns = $this->convert_to_relative_path($appBootstrap->path, $project_path);
        $this ->cout("Removing new application bootstrap '", 1,  self::defColor, 0)
                ->cout($appBootstrap->name, 0, self::yellow, 0)
                ->cout("' at '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("'.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($appBootstrap->path)))
            mkdir(dirname($appBootstrap->path), 0775);
        $this->cout("+", 1, self::green,0);
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
        $this->cout("+", 0, self::green,0);
        $s = $this->GetStatus($project_path);
        $appBootstrap->parent = $application;
        $s->project->bootstrap[strtolower($appBootstrap->name)]  = $appBootstrap;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}

?>
