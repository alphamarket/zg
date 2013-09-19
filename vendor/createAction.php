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
            if(preg_match_all( '/Parse error:\s*syntax error,(.+?)\s+in\s+.+?\s*line\s+(\d+)/i', $output, $match ))    
            {
                $this->cout("Error parsing '{$controller->name}'",0,self::hiRed);
                throw new \zinux\kernel\exceptions\invalideOperationException($match[0][0]);
            }
        }return;
        require_once $controller->path;
        if(!class_exists($controller->name))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
        $brace = 0;
        preg_match("", $subject);
        
        $this->cout("+", 0, self::green);
        $s = $this->GetStatus($project_path);
        $action->parent = $controller;
        $s->modules->modules[$controller->parent->name]->controller[$controller->name]->action[$action->name] = $action;
        $this->SaveStatus($s);
    }
}

?>
