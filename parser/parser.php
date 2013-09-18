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
        \zinux\kernel\utilities\debug::_var($this->command_generator->Generate());
        \zinux\kernel\utilities\debug::_var($this);
    }
}