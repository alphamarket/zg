<?php
namespace zinux\zg\parser;
/**
 * Description of parser
 *
 * @author dariush
 */
class parser extends baseParser
{    
    public function Run()
    {
        if(!count($this->args))
            $this->args[] = "help";
        if($this->args[0]=="help")
        {
            array_shift($this->args);
            $help  = new helpParser($this->args, $this->command_generator);
            $help->Run();
            return;
        }
        echo "PRSING";
    }
}