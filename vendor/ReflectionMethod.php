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
    
    const LAST_FINAL = 0;
    const LAST_ABSTRACT = 1;
    const LAST_PUBLIC = 2;
    const LAST_FUNC = 3;
    const LAST_CMNT = 4;
    
    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
    }
    
    public function getStartLine()
    {
        parent::getStartLine();
    }
}

?>
