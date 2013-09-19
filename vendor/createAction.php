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
        $mbc = "
    public function ".preg_replace("#action$#i","", $action->name)."Action()
    {
    }
    ";
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$controller->path}"))
            throw new \zinux\kernel\exceptions\notFoundException("{$controller->name} not found ...");
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
        require_once $controller->path;
        
        $s = $this->GetStatus($project_path);
        
        $module = $controller->parent;
        
        $ns = "{$module->parent->name}\\{$module->name}\\controllers";
        
        $class = "$ns\\{$controller->name}";
        
        if(!class_exists($class))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
    
        $rf = new \ReflectionClass($class);
        
        if(!$rf->isSubclassOf('\zinux\kernel\controller\baseController'))
            throw new \zinux\kernel\exceptions\invalideOperationException("'$class' should be a sub class of '\zinux\kernel\controller\baseController'");
            
        if(!preg_match_all(
                "#((.*)class[\s|\n]*{$controller->name}[\s|\n]*extends[\s|\n]*((.*)[\s|\n]*)*[\\]zinux[\\]kernel[\\]controller[\\]baseController[\s|\n]*(.*))#is", 
                preg_quote(file_get_contents($controller->path), "#"),
                $matches
        ))
            throw new \zinux\kernel\exceptions\notFoundException("Didn't find any match with '{$controller->name}' controller class");
        \zinux\kernel\utilities\debug::_var($matches,1);
        $matches = $matches[0];
        $brace = 0;
        for($index = 0; $index<strlen($matches); $index++)
        {
            if($matches[$index]=="{" && ++$brace)
                echo "{".PHP_EOL;
            elseif($matches[$index] == "}")
            {
                $brace--;
                if(!$brace)
                    echo "HERE IS THE PLACE ...";
                echo "}".PHP_EOL;
            }
        }
        \zinux\kernel\utilities\debug::_var($matches,1);
            
        preg_match("", $subject);
        
        $this->cout("+", 0, self::green);
        $action->parent = $controller;
        $s->modules->modules[$controller->parent->name]->controller[$controller->name]->action[$action->name] = $action;
        $this->SaveStatus($s);
    }
}

?>
