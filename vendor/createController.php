<?php
namespace zinux\zg\vendor;
/**
 * Description of createModuleBoostrap
 *
 * @author dariush
 */
class createController extends \zinux\zg\baseZg
{
    public function __construct(Item $module, Item $moduleBootstrap)
    {
        $mbc = "<?php
namespace modules\\{$module->name};

class {$moduleBootstrap->name}
{
    /**
     * A pre-dispatch function
     * @param \\zinux\\kernel\\routing\\request\\\$request
     */
    public function pre_CHECK(\\zinux\\kernel\\routing\\request\\\$request)
    {
    }
    /**
     * A post-dispatch function
     * @param \\zinux\\kernel\\routing\\request\\\$request
     */
    public function post_CHECK(\\zinux\\kernel\\routing\\request\\\$request)
    {
    }
}";
        file_put_contents($moduleBootstrap->path, $mbc);
        $this->cout("+", 1, self::green);
        $s = $this->GetStatus();
        $s->boostraps->modules[] = $moduleBootstrap;
        $this->SaveStatus($s);
    }
}

?>
