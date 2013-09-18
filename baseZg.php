<?php
namespace zinux\zg;
require_once dirname(__FILE__)."/../baseZinux.php";
defined("ZG_ROOT") ||  define("ZG_ROOT", dirname(__FILE__));
defined("RUNNING_ENV") ||  define("RUNNING_ENV", "DEVELOPMENT");

/**
 * Description of baseZg
 *
 * @author dariush
 */
abstract class baseZg extends \zinux\baseZinux
{
    public function Initiate(){}
}