<?php
namespace zinux\zg\vendors\reflections;
/**
 * Description of ReflectionClass
 *
 * @author dariush
 */
class ReflectionClass extends \ReflectionClass
{
    public function __construct($argument)
    {
        parent::__construct($argument);
    }
    public function RemoveMethod(ReflectionMethod $method)
    {
        file_put_contents($this->getFileName(), $method->Remove());
    }
    public function AddMethod($mehod)
    {
        if(!isset($mehod) || !is_string($mehod))
            throw new \zinux\kernel\exceptions\invalideArgumentException;
        $file_cont = file_get_contents($this->getFileName());
        $fl = explode(PHP_EOL, $file_cont);
        $head_cont  = "";
        for($i = $this->getStartLine()-1; $i<$this->getEndLine()-1; $i++)
        {
            $head_cont.=$fl[$i].PHP_EOL;
        }
        file_put_contents($this->getFileName(), str_replace($head_cont, $head_cont.$mehod, $file_cont));
    }
}

?>
