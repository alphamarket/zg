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
        $class_cont = "";
        \zinux\kernel\utilities\debug::_var(array($crf->getStartLine()-1,$crf->getEndLine()-1));
        for($i = $rf->getStartLine()-1; $i<$rf->getEndLine()-1; $i++)
        {
            if($i>=$crf->getStartLine()-1 && $i<$crf->getEndLine())
                $i = $crf->getEndLine();
            $class_cont .= $fl[$i].PHP_EOL;
        }
//        echo $file_cont;
//        echo $new_file_cont;
//        echo $class_cont;
        $modifiers = array(self::LAST_ABSTRACT=>0,self::LAST_CMNT=>0, self::LAST_FINAL=>0, self::LAST_FUNC=>0, self::LAST_PUBLIC=>0);
        $g = self::green;
        for($i = $rf->getStartLine()-1; $i<$rf->getEndLine()-1; $i++)
        {
            $txt = $fl[$i];
            
            if(preg_match("#^(//)#i", $txt, $matches)) continue;
            if(preg_match("#(.*\*/)#i", $txt, $matches))
            {
                $modifiers[self::LAST_CMNT] = 0;
            }
            if(preg_match("#(/\*.*)#i", $txt, $matches))
            {
                #\zinux\kernel\utilities\debug::_var($matches);
                $modifiers[self::LAST_CMNT] = 1;
            }
            if($modifiers[self::LAST_CMNT]) continue;
            /**
             * 
             * 
             * USE EXPLODE
             * 
             * 
             */
            $this->cout($txt = preg_replace("#(/\*.*\*/|.*\*/)#i", "", trim($txt)),0,self::yellow);
            foreach (explode(" ", trim($txt)) as $key=> $value)
            {
                echo $value." ~ ";
                foreach(array("final", "abstract", "public", "function") as $key => $value)
                {
                    
                }
            };
            $this->cout();
            $this->cout($txt = preg_replace("#(/\*.*\*/|.*\*/)#i", "", trim($txt)),0,$g);
            
            if($i%2)
                $g=self::green;
            else
                $g=self::red;
        }
        return;
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
