<?php
namespace zinux\zg\vendor\creators;
/**
 * Description of createView
 *
 * @author dariush
 */
class createHelper extends \zinux\zg\baseZg
{
    public function __construct(\zinux\zg\vendor\item $module, \zinux\zg\vendor\item $helper, $project_path = ".")
    {
        $helper->name = preg_replace("#(\w+)helper$#i","$1", $helper->name)."Helper";
        $ns = $this->convert_to_relative_path($helper->path, $project_path);;
        $this ->cout("Creating new helper '", 1,  self::defColor, 0)
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
