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
        $stack = array($this->command_generator->Generate());
        while(count($stack))
        {
            $value = array_pop($stack);
            if(!isset($value->title))
            {
                $tmps = array();
                foreach($value as $sub_value)
                {
                    array_push($tmps, $sub_value);
                }
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
            }
            else
                $this->printHelp($value);
        }
    }
    
    protected function printHelp($content)
    {
        $this ->cout()
                ->cout($content->title, 1, self::cyan)
                ->cout(self::hiYellow.preg_replace("#(\\\$\w+)#i", self::defColor.self::yellow."$1".self::hiYellow, $content->help->command), 2)
                ->cout($content->help->detail, 3);
    }
}