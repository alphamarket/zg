<?php
namespace zinux\zg\vendors\reflections;
/**
 * An inhanced ReflectionMethod
 */
class ReflectionMethod extends \ReflectionMethod
{
    /**
     * holds method's start line#
     * @var integer
     */
    protected $start_line = null;
    /**
     * holds method's end line#
     * @var integer
     */
    protected $end_line = null;
    /**
     * holds method's text
     * @var string
     */
    protected $method_txt = null;
    /**
     * holds target class's text
     * @var string
     */
    protected $file_content = null;
    /**
     * holds method class
     * @var \ReflectionClass
     */
    protected $target_class = null;
    
    const LAST_FINAL = 0x0;
    const LAST_ABSTRACT = 0x1;
    const LAST_PUBLIC = 0x2;
    const LAST_FUNC = 0x3;
    const LAST_PRIVATE = 0x4;
    const LAST_PRTCTD = 0x5;
    const LAST_CMNT = 0x6;
    
    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        $this->target_class = new \ReflectionClass($class);
        $this->file_content = explode(PHP_EOL, file_get_contents($this->target_class->getFileName()));
        $this->_getEndLine($this->_getStartLine($this->file_content), $this->file_content);
    }
    /**
     * gets accurate end line of method from an start point in a class content
     * @param int $start_index method's start point
     * @param string|array $class_file_content either the class file's content in string or tokenized in lines
     * @return int the end line of method
     */
    protected function _getEndLine($start_index, $class_file_content)
    {
        if(is_string($class_file_content))
            $class_file_content = explode(PHP_EOL, $class_file_content);
        
        $fl = $class_file_content;
        $this->method_txt = "";
        $braces = 0;
        $last_count = 0;
        $matches = array();
        for($this->end_line = $start_index-1; $this->end_line<$this->target_class->getEndLine();$this->end_line++)
        {
            if(preg_match_all("#(\{)#i", $fl[$this->end_line], $matches))
                $braces+=count($matches[0]);
            if(preg_match_all("#(\})#i", $fl[$this->end_line], $matches))
            {
                $last_count = count($matches[0]);;
                $braces -= $last_count;
                if(!$braces)
                    break;
            }
            $this->method_txt.=$fl[$this->end_line].PHP_EOL;
        }
        $i = 0;
        while($last_count)
        {
            if($fl[$this->end_line][$i]=="}") $last_count--;
            $this->method_txt.=$fl[$this->end_line][$i++];
        }
        $this->method_txt.=(PHP_EOL);
        # make it one-based #
        return ++$this->end_line;
    }
    /**
     * gets accurate start line of method from an start point in a class content
     * @param string|array $class_file_content either the class file's content in string or tokenized in lines
     * @return int the start line of method
     */
    protected function _getStartLine($class_file_content)
    {
        if(is_string($class_file_content))
            $class_file_content = explode(PHP_EOL, $class_file_content);
        
        $fl = $class_file_content;
        $max = count($fl)+1;
        $keywords = array(
                    self::LAST_ABSTRACT=>"abstract",
                    self::LAST_PUBLIC=>"public", 
                    self::LAST_FINAL=>"final", 
                    self::LAST_PRIVATE=>"private",
                    self::LAST_PRTCTD=>"protected",
                    self::LAST_FUNC=>"function",
                    self::LAST_CMNT => "/*COMMENT*/"
        );
        $modifiers = array_fill(0, count($keywords), -$max);
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
                foreach($keywords as $index => $value)
                {
                    if($index == self::LAST_CMNT) continue;
                    if(strtolower($value) == strtolower($token))
                    {
                        $modifiers[$index] = $i;
                    }
                    elseif($modifiers[self::LAST_FUNC]>0)
                    {
                        if(strtolower($token)==strtolower($this->name))
                            goto __END;
                        $modifiers = array_fill(0, count($keywords), -$max);
                    }
                }
            }
        }
__END:
        unset($modifiers[self::LAST_CMNT]);
        $this->start_line = $max;
        for($i = 0;$i<count($modifiers);$i++)
        {
            if($modifiers[$i]>=0 && $this->start_line>$modifiers[$i])
                $this->start_line = $modifiers[$i];
        }
        # make it one-based #
        return ++$this->start_line;
    }
    /**
     * gets accurate start line of method
     * @return int the start line of method
     */
    public function getStartLine()
    {
        return $this->start_line;
    }
    /**
     * gets accurate end line of method
     * @return int the start line of method
     */
    public function getEndLine()
    {
        return $this->end_line;
    }
    /**
     * gets accuratemethod texts
     * @return string methods text
     */
    public function getMethodText()
    {
        return $this->method_txt;
    }
    /**
     * remove the method from its parent class content
     * @return string The class content with function removed!
     */
    public function Remove()
    {
        $class_file_content = $this->file_content;
        if(is_string($class_file_content))
            $class_file_content = explode(PHP_EOL, $class_file_content);
        $removed = "";
        $i = 0;
        while($i<$this->target_class->getStartLine()-1)
        {
            $removed .= $class_file_content[$i++].PHP_EOL;
        }
        for(; $i<$this->target_class->getEndLine(); $i++)
        {
            if($i>=$this->start_line-1 && $i<$this->end_line-1) 
            {
                $matches = array();
                preg_match_all("#(\})#i", $class_file_content[$this->end_line-1], $matches);
                $last_count = count($matches[0]);
                $j = 0;
                while($last_count)
                    if($class_file_content[$this->end_line-1][$j++]=="}")
                        $last_count--;
                $removed.=str_repeat(" ", 4);
                for(;$j<strlen($class_file_content[$this->end_line-1]);$j++)
                    $removed.=$class_file_content[$this->end_line-1][$j];
                $removed.=(PHP_EOL);
                $i = $this->end_line-1;
                continue;
            }
            $removed.=($class_file_content[$i].PHP_EOL);
        }
        while($i<count($class_file_content))
        {
            $removed .= $class_file_content[$i++].PHP_EOL;
        }
        return $removed;
    }
}
?>
