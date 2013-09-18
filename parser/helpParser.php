<?php
namespace zinux\zg\parser;
/**
 * Description of parser
 *
 * @author dariush
 */
class helpParser extends baseParser
{    
    public function Run()
    {
        $this->cout("Zinux Generator by Dariush Hasanpoor [b.g.dariush@gmail.com] 2013", 0, self::yellow);
        foreach ($this->command_generator->Generate() as $key=> $value)
        {
            if(!isset($value->title))
            {
                foreach($value as $key=> $sub_value)
                {
                    $this->printHelp($key, $sub_value);
                }
            }
            else
                $this->printHelp($key, $value);
        }
    }
    protected function printHelp($name, $content)
    {
        \zinux\kernel\utilities\debug::_var(func_get_args());
    }
}