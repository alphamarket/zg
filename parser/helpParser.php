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
        $stack = array(array("", $this->command_generator->Generate()));
        $head_lines = $this->remove_arg($this->args, "--heads");
        if(count($this->args))
        {
            $args = $this->args;
            $n = new parser($args, $this->command_generator);
            $stack = array(array("", $n->getOperator()));
        }
        if($head_lines)
        {
            $kw = unserialize(PARSER_KEYWORDS);
            $this->cout("Valid operation list for '".self::yellow."zg ".implode(" ", $this->args).self::defColor."': ");
            $found = 0;
            foreach($stack[0][1] as $key=> $value)
            {
                if(array_key_exists($key, $kw))continue;
                $found = 1;
                $this->cout("> ", 1, self::yellow, 0)->cout($key, 0, self::cyan);
            }
            if(!$found)
            {
                $this->cout("'".self::yellow."zg ".implode(" ", $this->args).self::defColor."' is a solo operation!", 1)
                        ->cout("No sub-operation found!",1, self::red);
            }
            return;
        }
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
        $this->cout()->cout(">    Type 'zg -h \$command' to print more help about that command.", 0,self::hiBlue);
        $this->cout()->cout(">    Type 'zg -h (\$command) --head' to print headline operations.", 0,self::hiBlue);
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
        if($render_options && isset($content->options))
        {
            $this->cout()->cout("Options: ", 2, self::hiYellow);
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->options as $option => $exp)
            {
                $this ->cout()
                        ->cout($option, 3, self::yellow, 0)
                        ->cout(" : ", 0, self::defColor, 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $exp), 0, self::yellow);
            }
        }
        if(isset($content->defaults))
        {
            $this ->cout()->cout("Default Values:", 2, self::hiYellow)->cout();
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->defaults as $arg => $value)
            {
                $this ->cout($arg, 3, self::yellow, 0)
                        ->cout(" : ", 0, self::defColor, 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $value), 0, self::yellow);
            }
        }
        if($render_options && isset($content->notes))
        {
            $this ->cout()->cout("Notes:", 2, self::hiYellow);
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
    }
}