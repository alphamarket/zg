<?php
namespace zg\operators;

/**
 * A base class for operators
 */
abstract class baseOperator extends \zg\baseZg
{    
    /**
     * Construct a new operator
     * @param boolean $suppress_header_text check if you want to have general header output or not!
     */
    public function __construct($suppress_header_text = 0)
    {
        if(!$suppress_header_text)
            $this->PrintTItleString();
    }
    /**
     * Runs exec command
     * @param array $opt the system commands to run
     */
    public function Run($opt)
    {
        # for each command 
        foreach($opt as $value)
        {
            # run it
            exec($value);
        }
    }
    /**
     * General header printer
     */
    public function PrintTItleString()
    {
        $this->cout("Zinux Generator(v".ZG_VERSION.") by Dariush Hasanpoor [b.g.dariush@gmail.com] 2013", 0, self::getColor(self::yellow));
    }
    /**
     * Normalizes the given name and removes special characters and spaces and replaces them by '_' character. 
     * @param string $name target name to normalize
     * @param string $fix the fix part of name that should exists at end of $name 
     */
    public function NormalizeName(&$name, $fix = "")
    {
        $name = preg_replace (array('/[^\p{L}\p{N}]+/ui', "#^[0-9]+#i", '/^_/u', '/_$/u'), array("_", "", "", ""), $name);
        $name = preg_replace (array("#^[0-9]+#i", '/^_/u', '/_$/u'), array("", "", ""), $name);
        if(!strlen($fix)) return;
        $name = preg_replace("#(\w+)$fix$#i", "$1", $name).ucfirst($fix);
    }
}
