<?php
namespace zg\vendors\reflections;
/**
 * An inhanced ReflectionClass
 */
class ReflectionClass extends \ReflectionClass
{
    public function __construct($argument)
    {
        parent::__construct($argument);
    }
    /**
     * removes a method from class file's content
     * @param \zg\vendors\reflections\ReflectionMethod $method target method to remove
     */
    public function RemoveMethod(ReflectionMethod $method)
    {
        file_put_contents($this->getFileName(), $method->Remove());
    }
    /**
     * adds a method to class files's content
     * @param string $mehod method's string
     * @throws \zinux\kernel\exceptions\invalideArgumentException if method is not an string or not setted
     */
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
