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
        $arg = null;
        if(count($this->args))
        {
            $n = new parser($this->args, $this->command_generator);
            $this->printHelp($n->getOperator(), 1);
            return;
        }
        $stack = array(array("", $this->command_generator->Generate()));
        while(count($stack))
        {
            $value = array_pop($stack);
            $key = $value[0];
            $value = $value[1];
            if(!isset($value->title))
            {
                if(!$this->is_iterable($value)) continue;
                $tmps = array();
                foreach($value as $_key => $sub_value)
                {
                    array_push($tmps, array($_key, $sub_value));
                }
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
            }
            else
            {
                 $tmps = array();
                foreach($value as $_key => $sub_value)
                {
                    if(!$this->is_iterable($value)) continue;
                    if($key!="instance" && $key!="help")
                        array_push($tmps, array($_key, $sub_value));
                }
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
                $this->printHelp($value);
            }
        }
    }
    
    protected function printHelp($content, $render_options = 0)
    {
        if(!(isset($content->title) && isset($content->help))) return;
        $command = preg_replace("#(\\\$\w+)#i", self::defColor.self::yellow."$1".self::hiYellow, $content->help->command);
        $rep_pat = "$1".str_repeat(" ", 3*5);
        $this ->cout()
                ->cout($content->title, 1, self::cyan)
                ->cout(self::hiYellow.preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat),  $command), 2, self::defColor);
        if(isset($content->help->alias))
            $this->cout("Alias: [ ".self::hiYellow.preg_replace("#(\\\$\w+)#i", self::defColor.self::yellow."$1".self::hiYellow, $content->help->alias).self::defColor." ]", 3, self::defColor);
        $this->cout();
        $rep_pat = "$1".str_repeat(" ", 3*5);
        $this ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat),  $content->help->detail), 3);
        if(isset($content->notes))
        {
            $this ->cout("Notes:", 2, self::hiYellow);
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->notes as $index => $note)
            {
                $index++;
                $this ->cout()
                        ->cout("$index ) ", 3, self::yellow, 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $note))
                        ->cout();
            }
        }
        if($render_options && isset($content->options))
        {
            $this->cout("Options: ", 2, self::hiYellow);
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->options as $option => $exp)
            {
                $this ->cout()
                        ->cout($option, 3, self::yellow, 0)
                        ->cout(" : ", 0, self::defColor, 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $exp), 0, self::yellow);
            }
        }
    }
}