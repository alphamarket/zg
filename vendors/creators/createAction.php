<?php
namespace zinux\zg\vendors\creators;
/**
 * action creator
 */
class createAction extends \zinux\zg\operators\baseOperator
{
    /**
     * ctor a new action
     * @param \zinux\zg\vendors\item $controller target controller item
     * @param \zinux\zg\vendors\item $action target action item
     * @param string $project_path project directory
     * @throws \zinux\kernel\exceptions\notFoundException if controller not found or class does not exist
     * @throws \zinux\kernel\exceptions\invalideOperationException if controller is not sub class of baseController
     */
    public function __construct(\zinux\zg\vendors\item $controller, \zinux\zg\vendors\item $action, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($controller->path, $project_path);
        $this ->cout("Creating new action '",0.5,  self::getColor(self::defColor), 0)
                ->cout($action->path, 0, self::getColor(self::yellow), 0)
                ->cout("' in '",0,self::getColor(self::defColor), 0)
                ->cout("$ns\\{$controller->name}", 0, self::getColor(self::yellow), 0)
                ->cout("'.");
        $mbc = "
    /**
    * The \\{$controller->parent->parent->name}\\{$controller->parent->name}\\controllers\\{$controller->name}::{$action->path}()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function {$action->path}()
    {
        
    }
";
        
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$controller->path}"))
            throw new \zinux\kernel\exceptions\notFoundException("{$controller->name} not found ...");
        
        $this->check_php_syntax($controller->path);
        
        $this->require_file($controller->path);
        
        
        $s = $this->GetStatus($project_path);
        
        $class = "$ns\\{$controller->name}";
        
        if(!class_exists($class))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
    
        
        $rf = new \ReflectionClass($class);
        
        if(!$rf->isSubclassOf('\zinux\kernel\controller\baseController'))
            throw new \zinux\kernel\exceptions\invalideOperationException("'$class' should be a sub class of '\zinux\kernel\controller\baseController'");
        if(!method_exists(new $class, $action->path))
        {
            $n = new \zinux\zg\vendors\reflections\ReflectionClass($class);
            $n->AddMethod($mbc);
        }
        
        $action->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)] = $action;
        $this->SaveStatus($s);
        
    }
}
?>
