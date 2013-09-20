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
        if(isset($s->project->bootstrap[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application bootstrap '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator();
        $appBootstrap =  $c->createAppBootstrap($args[0]);
    }
    public function routes($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,1,1);
        $s = $this->GetStatus();
        $args[0] = preg_replace("#(\w+)routes$#i","$1", $args[0])."Routes";
        if(isset($s->project->bootstrap[strtolower($args[0])]))
            throw new \zinux\kernel\exceptions\notFoundException("Application routes '{$args[0]}' already exists in zg manifest!<br />Try 'zg build' command!");
        $c = new \zinux\zg\vendor\creator();
        $appRoutes =  $c->createAppRoutes($args[0]);
    }
}
