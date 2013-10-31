<?php
namespace zinux\zg\vendors\removers;
/**
 * action remover
 */
class removeAction extends \zinux\zg\operators\baseOperator
{
    /**
     * ctor a new action remover
     * @param \zinux\zg\vendors\item $action target action to remove
     * @param string $project_path project directory
     * @throws \zinux\kernel\exceptions\notFoundException if action's parent controller/class not found
     * @throws \zinux\kernel\exceptions\invalideOperationException if target controller is not subclass of baseController or target method not found
     */
    public function __construct(\zinux\zg\vendors\item $action, $project_path = ".")
    {
        $controller = $action->parent;
        $ns = $this->convert_to_relative_path($controller->path, $project_path);
        $action->path = preg_replace("#(\w+)action$#i","$1", $action->name)."Action";
        $this ->cout("Removing new action '",0.5,  self::defColor, 0)
                ->cout($action->path, 0, self::yellow, 0)
                ->cout("' in '",0,self::defColor, 0)
                ->cout("$ns\\{$controller->name}", 0, self::yellow, 0)
                ->cout("'.");
        $mbc = "
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
           throw new \zinux\kernel\exceptions\invalideOperationException("'$class' does not contain method '{$action->path}'...");
        
        $cr = new \zinux\zg\vendors\reflections\ReflectionClass($class);
        $cr->RemoveMethod(new \zinux\zg\vendors\reflections\ReflectionMethod($class, $action->name));
        
        
        $action->parent = $controller;
        unset($s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)]);
        $this->SaveStatus($s);
        
    }
}
?>
