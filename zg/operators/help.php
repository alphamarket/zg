<?php
namespace zg\operators;

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
        $help  = new \zg\parser\helpParser($args, new \zg\command\commandGenerator());
        # run the help parser
        $help->Run();
    }
}
