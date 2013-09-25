<?php
namespace zinux\zg\vendors\creators;
/**
 * model creator
 */
class createModel extends \zinux\zg\baseZg
{
    /**
     * ctor a new model
     * @param \zinux\zg\vendors\item $module target module item
     * @param \zinux\zg\vendors\item $model target model item
     * @param type $project_path
     */
    public function __construct(\zinux\zg\vendors\item $module, \zinux\zg\vendors\item $model, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($model->path, $project_path);;
        $this ->cout("Creating new model '", 0.5,  self::defColor, 0)
                ->cout($model->name, 0, self::yellow, 0)
                ->cout("' for '",0,self::defColor, 0)
                ->cout($ns, 0, self::yellow, 0)
                ->cout("' module.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($model->path)))
            mkdir(dirname($model->path), 0775);
        
        $mbc = "<?php
namespace $ns;
    
/**
* The $ns\\{$model->name}
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class {$model->name}
{
}";
        file_put_contents($model->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $model->parent = $module;
        $s->modules->collection[strtolower($module->name)]->model[strtolower($model->name)] = $model;
        $this->SaveStatus($s);
    }
}