<?php
namespace zinux\zg\vendor;
/**
 * Description of createModuleBoostrap
 *
 * @author dariush
 */

class createAction extends \zinux\zg\resources\operator\baseOperator
{
    public function __construct(Item $controller, Item $action, $project_path = ".")
    {
        $action->path = preg_replace("#(\w+)action$#i","$1", $action->name)."Action";
        $mbc = "
    /**
    * The \\{$controller->parent->parent->name}\\{$controller->parent->name}\\{$controller->name}::{$action->path}()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function {$action->path}()
    {
        
    }
";
        $this->cout("+", 1, self::green,0);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$controller->path}"))
            throw new \zinux\kernel\exceptions\notFoundException("{$controller->name} not found ...");
        $this->cout("+", 0, self::green,0);
        ob_start();
            system( "php -c '".dirname(__FILE__)."/php.ini' -l {$controller->path}", $ret );
        $output = ob_get_clean();
        if( $ret !== 0 )
        {
            $matches = array();
            if(preg_match_all( '/Parse error:\s*syntax error,(.+?)\s+in\s+.+?\s*line\s+(\d+)/i', $output, $matches))    
            {
                $this->cout("Error parsing '{$controller->name}'",0,self::hiRed);
                throw new \zinux\kernel\exceptions\invalideOperationException($matches[0][0]);
            }
        }
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
        if(method_exists(new $class, $action->path))
           throw new \zinux\kernel\exceptions\invalideOperationException("'$class' already contains method '{$action->path}'...");
           
        $this->cout("+", 0, self::green,0);
        $preg_match = "#([\s|\n]*class[\s|\n]*{$controller->name}[\s|\n]*extends[\s|\n]*((.*)[\s|\n]*)*[\\]zinux[\\]kernel[\\]controller[\\]baseController[\s|\n]*[\\\{]([^\\\}]*)[\\\}])#is";
        $file_cont = file_get_contents($controller->path);
        $this->cout("+", 0, self::green,0);
        
        if(!preg_match(
                $preg_match, 
                preg_quote($file_cont, "#"),
                $matches
        ))
            throw new \zinux\kernel\exceptions\notFoundException("Didn't find any match with '{$controller->name}' controller class");
        $this->cout("+", 0, self::green,0);
        $matched = stripslashes($matches[0]);
        
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
        file_put_contents($controller->path, preg_replace($preg_match, $top_str.$mbc.$down_str, $file_cont));
        $this->cout("+", 0, self::green,0);
        $action->parent = $controller;
        $s->modules->modules[$controller->parent->name]->controller[$controller->name]->action[$action->name] = $action;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green,1);
    }
}

?>
