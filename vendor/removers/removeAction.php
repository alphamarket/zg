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
        $ns = $this->convert_to_relative_path($action->parent->path, $project_path);
        $controller = $action->parent;
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
        
        $module = $controller->parent;
        
        $ns = "{$module->parent->name}\\{$module->name}\\controllers";
        
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
        $this->cout("+", 0, self::green,0);
        $preg_match = "#([\s|\n]*class[\s|\n]*{$controller->name}[\s|\n]*extends[\s|\n]*((.*)[\s|\n]*)*[\\]zinux[\\]kernel[\\]controller[\\]baseController[\s|\n]*[\\\{]([^\\\}]*)[\\\}])#is";
        $file_cont = preg_quote(file_get_contents($controller->path),"#");
        $this->cout("+", 0, self::green,0);
        
        if(!preg_match(
                $preg_match, 
                $file_cont,
                $matches
        ))
            throw new \zinux\kernel\exceptions\notFoundException("Didn't find any match with '{$controller->name}' controller class");
            throw new \zinux\kernel\exceptions\notImplementedException;
        $this->cout("+", 0, self::green,0);
        $matched = stripslashes($matches[0]);
        echo preg_quote($matched, "#").PHP_EOL;
        echo ($pat = "#(public?[\s|\n]+function[\s|\n]+{$action->name}[\s|\n]*)#i").PHP_EOL;
        preg_match($pat, preg_quote($matched, "#"), $matched);
        \zinux\kernel\utilities\debug::_var($matched);return;
        
        $brace = 0;
        
        $top_str = "";
        $down_str = "";
        
        for($index = 0; $index<strlen($matched); $index++)
        {
            if($matched[$index]=="{" && ++$brace);
            elseif($matched[$index] == "}" && !--$brace)
            {
                $this->cout("+", 0, self::green,0);
                $top_str = substr($matched, 0, $index-1);
                $down_str = substr($matched, $index);
                break;
            }
        }
        $this->cout("+", 0, self::green,0);
        $file_cont = $this->inverse_preg_quote(preg_replace($preg_match, $top_str.$mbc.$down_str, ($file_cont)),"#");
        file_put_contents($controller->path, $file_cont);
        $this->cout("+", 0, self::green,0);
        $action->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)] = $action;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green,1);
    }
}

?>
