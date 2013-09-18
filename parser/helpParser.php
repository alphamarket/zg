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
                if(!($value instanceof \stdClass)) continue;
                $tmps = array();
                foreach($value as $sub_value)
                {
                    array_push($tmps, $sub_value);
                }
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
            }
            else
            {
                 $tmps = array();
                foreach($value as $key => $sub_value)
                {
                    if(!($value instanceof \stdClass)) continue;
                    if($key!="instance" && $key!="help")
                        array_push($tmps, $sub_value);
                }
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
                
                $this->printHelp($value);
            }
        }
    }
    
    protected function printHelp($content)
    {
        if(!(isset($content->title) || isset($content->help))) return;
        $this ->cout()
                ->cout($content->title, 1, self::cyan)
                ->cout(self::hiYellow.preg_replace("#(\\\$\w+)#i", self::defColor.self::yellow."$1".self::hiYellow, $content->help->command), 2)
                ->cout($content->help->detail, 3);
    }
}