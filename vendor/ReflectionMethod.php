<?php
namespace zinux\zg\vendor;

/**
 * Description of ReflectionMethod
 *
 * @author dariush
 */
class ReflectionMethod extends \ReflectionMethod
{
    /**
     * holds method start line#
     * @var integer
     */
    protected $start_line = null;
    /**
     * holds method class
     * @var \ReflectionClass
     */
    public $target_class = null;
    
    const LAST_FINAL = 0;
    const LAST_ABSTRACT = 1;
    const LAST_PUBLIC = 2;
    const LAST_FUNC = 3;
    const LAST_CMNT = 4;
    
    public function __construct($class, $name, $class_file_content)
    {
        parent::__construct($class, $name);
        $this->target_class = new \ReflectionClass($class);
        $this->file_content = explode(PHP_EOL, $class_file_content);
        $this->_getEndLine($this->_getStartLine($this->file_content), $this->file_content);
    }
    
    protected function _getEndLine($start_index, $class_file_content)
    {
        if(is_string($class_file_content))
            $class_file_content = explode(PHP_EOL, $class_file_content);
        
        $fl = $class_file_content;
        $method_txt = "";
        $braces = -1;
        for($i = $start_index; $i<$this->target_class->getEndLine();$i++)
        {
            $method_txt.=$fl[$i].PHP_EOL;
            if(preg_match_all("#(\{)#i", $fl[$i], $matches))
            {
                while(count($matches[0]))
                {
                    array_push($braces, array_shift($matches[0]));
                }
            }
        }
        echo $method_txt;
        
    }
    
    protected function _getStartLine($class_file_content)
    {
        if(is_string($class_file_content))
            $class_file_content = explode(PHP_EOL, $class_file_content);
        
        $fl = $class_file_content;
        $max = count($fl)+1;
        $modifiers = array_fill(0, 5, -$max);
        for($i = $this->target_class->getStartLine()-1; $i<$this->target_class->getEndLine()-1; $i++)
        {
            $txt = trim($fl[$i]);
            if(preg_match("#^(//)#i", $txt)) continue;
            if(preg_match("#(.*\*/)#i", $txt))
                $modifiers[self::LAST_CMNT] = 0;
            if(preg_match("#(/\*.*)#i", $txt))
                $modifiers[self::LAST_CMNT] = 1;
            if($modifiers[self::LAST_CMNT]) continue;
            $txt = preg_replace("#(/\*.*\*/|.*\*/|\(.*\))#i", "", $txt);
            foreach (explode(" ", trim($txt)) as $token)
            {
                $token = trim($token);
                if(!strlen($token)) continue;
                foreach(array("final", "abstract", "public", "function") as $index => $value)
                {
                    if(strtolower($value) == strtolower($token))
                    {
                        $modifiers[$index] = $i;
                    }
                    elseif($modifiers[self::LAST_FUNC]>0)
                    {
                        if(strtolower($token)==strtolower($this->name))
                            goto __END;
                        $modifiers = array_fill(0, 5, -$max);
                    }
                }
            }
        }
__END:
        unset($modifiers[self::LAST_CMNT]);
        for($i = 0;$i<count($modifiers);$i++)
        {
            if($modifiers[$i]<=0)
                unset($modifiers[$i]);
        }
        return $this->start_line = min($modifiers);
    }
}

?>
