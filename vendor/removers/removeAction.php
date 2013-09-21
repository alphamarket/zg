<?php
namespace zinux\zg\vendor\removers;
/**
 * Description of removemoduleBootstrap
 *
 * @author dariush
 */

class removeAction extends \zinux\zg\resources\operator\baseOperator
{
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
        $this->cout("+", 1, self::green,0);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$controller->path}"))
            throw new \zinux\kernel\exceptions\notFoundException("{$controller->name} not found ...");
        $this->cout("+", 0, self::green,0);
        $this->check_php_syntax($controller->path);
        $this->cout("+", 0, self::green,0);
        require_once $controller->path;
        $this->cout("+", 0, self::green,0);
        
        $s = $this->GetStatus($project_path);
        
        $class = "$ns\\{$controller->name}";
        
        if(!class_exists($class))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
    
        $this->cout("+", 0, self::green,0);
        $rf = new \ReflectionClass($class);
        
        if(!$rf->isSubclassOf('\zinux\kernel\controller\baseController'))
            throw new \zinux\kernel\exceptions\invalideOperationException("'$class' should be a sub class of '\zinux\kernel\controller\baseController'");
        if(!method_exists(new $class, $action->path))
           throw new \zinux\kernel\exceptions\invalideOperationException("'$class' does not contain method '{$action->path}'...");
        if(!is_callable(array(new $class, $action->path)))
            throw new \zinux\kernel\exceptions\invalideOperationException("{$action->name} is not callable ...");
           
        $file_cont = file_get_contents($controller->path);
        $crf = new \ReflectionMethod($class, $action->name);
        $fl = explode(PHP_EOL, $file_cont);
        $new_file_cont = "";
        \zinux\kernel\utilities\debug::_var(array($crf->getStartLine(),$crf->getEndLine()));
        for($i = $rf->getStartLine()-1; $i<$rf->getEndLine()-1; $i++)
        {
            if($i>=$crf->getStartLine()-1 && $i<$crf->getEndLine())
            {
                $j = $i;
                while($j)
                {
                    if(trim($fl[$j])=="") {$j--; continue;}
                    if(\zinux\kernel\utilities\string::startsWith(trim($fl), "//")){$j--; continue;}
                    if(trim($fl)){}
                }
                $i = $crf->getEndLine();
            }
            $new_file_cont.=$fl[$i].PHP_EOL;
        }
        die($new_file_cont);
        $this->cout("+", 0, self::green,0);
        $new_file_cont =  str_replace($new_file_cont, $new_file_cont.$mbc, $file_cont);
        file_put_contents($controller->path, $new_file_cont);
        $this->cout("+", 0, self::green,0);
        $action->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)] = $action;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green,1);
    }
}
?>
