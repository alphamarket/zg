<?php
namespace zinux\zg\resources\operator;

class new_app extends baseOperator
{
    public function bootstrap($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,1,1);
        $s = $this->GetStatus();
        $args[0] = preg_replace("#(\w+)bootstrap$#i","$1", $args[0])."Bootstrap";
        if(isset($s->project->bootstraps[$args[0]]))
            throw new \zinux\kernel\exceptions\notFoundException("Application bootstrap '{$args[0]}' already exists in zg manifest!<br />    Try 'zg reload' command!");
        $c = new \zinux\zg\vendor\creator();
        $appBootstrap =  $c->createAppBootstrap($args[0]);
    }
}
