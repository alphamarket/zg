<?php
namespace zinux\zg\vendors\creators;
/**
 * helper creator
 */
class createHelper extends \zinux\zg\baseZg
{
    /**
     * ctor a new helper
     * @param \zinux\zg\vendors\item $module target module item
     * @param \zinux\zg\vendors\item $helper target helper item
     * @param string $project_path project directory
     */
    public function __construct(\zinux\zg\vendors\item $module, \zinux\zg\vendors\item $helper, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($helper->path, $project_path);;
        $this ->cout("Creating new helper '", 0.5,  self::defColor, 0)
                ->cout($helper->name, 0, self::yellow, 0)
                ->cout("' for '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("' module.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($helper->path)))
            mkdir(dirname($helper->path), 0775);
        
        $mbc = "<?php
namespace $ns;
    
/**
* The $ns\\{$helper->name}
* @by Zinux Generator <b.g.dariush@gmail.com>
*/";
        file_put_contents($helper->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $helper->parent = $module;
        $s->modules->collection[strtolower($module->name)]->helper[strtolower($helper->name)] = $helper;
        $this->SaveStatus($s);
    }
}
