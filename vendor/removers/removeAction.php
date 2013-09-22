<?php
namespace zinux\zg\vendor\removers;
/**
 * Description of removemoduleBootstrap
 *
 * @author dariush
 */

class removeAction extends \zinux\zg\resources\operator\baseOperator
{
    const LAST_FINAL = 0;
    const LAST_ABSTRACT = 1;
    const LAST_PUBLIC = 2;
    const LAST_FUNC = 3;
    const LAST_CMNT = 4;
    public function __construct(\zinux\zg\vendor\item $action, $project_path = ".")
    {
        $controller = $action->parent;
        $ns = $this->convert_to_relative_path($controller->path, $project_path);
        $action->path = preg_replace("#(\w+)action$#i","$1", $action->name)."Action";
        $this ->cout("Removing new action '",1,  self::defColor, 0)
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
        
        require_once $controller->path;
        
        
        $s = $this->GetStatus($project_path);
        
        $class = "$ns\\{$controller->name}";
        
        if(!class_exists($class))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
    
        
        $rf = new \ReflectionClass($class);
        
        if(!$rf->isSubclassOf('\zinux\kernel\controller\baseController'))
            throw new \zinux\kernel\exceptions\invalideOperationException("'$class' should be a sub class of '\zinux\kernel\controller\baseController'");
        if(!method_exists(new $class, $action->path))
           throw new \zinux\kernel\exceptions\invalideOperationException("'$class' does not contain method '{$action->path}'...");
        
        $cr = new \zinux\zg\vendor\reflections\ReflectionClass($class);
        $cr->RemoveMethod(new \zinux\zg\vendor\reflections\ReflectionMethod($class, $action->name));
        
        
        $action->parent = $controller;
        unset($s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)]);
        $this->SaveStatus($s);
        
    }
}
?>
