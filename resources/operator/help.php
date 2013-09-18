<?php
namespace zinux\zg\resources\operator;

class help extends baseOperator
{
    public function show($args)
    {
        $help  = new \zinux\zg\parser\helpParser($args, new \zinux\zg\command\commandGenerator());
        $help->Run();
    }
}
