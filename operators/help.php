<?php
namespace zinux\zg\operators;

/**
 * zg -h handler
 */
class help extends baseOperator
{
    /**
     * zg -h handler
     */
    public function show($args)
    {
        # invoke a help parse with passed argument
        $help  = new \zinux\zg\parser\helpParser($args, new \zinux\zg\command\commandGenerator());
        # run the help parser
        $help->Run();
    }
}
