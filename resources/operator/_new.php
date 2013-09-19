<?php
namespace zinux\zg\resources\operator;

class _new extends baseOperator
{
    public function project($args)
    {
        $this->restrictArgCount($args);
        
        $pName = implode("-", $args);
        $this->CreateStatusFile($pName);
        $s = $this->GetStatus($pName);
        $s->modules->meta = new \zinux\zg\vendor\Item("module", $s->project->path."/modules");
        $this->SaveStatus($s);
        
        $this ->cout("Creating new project '", 0, self::defColor, 0)
                ->cout("$pName", 0, self::yellow, 0)
                ->cout("' ...");
        $vpname = str_replace(" ", "-", $pName);
        $opt = array(
                "cp ".ZG_TEMPLE_ROOT."/* $pName/ -R",
                "cp -rf ".Z_CACHE_ROOT." $pName",
                "mv ./$pName/".basename(Z_CACHE_ROOT)." ./$pName/zinux",
                "echo '# add this to apache vhost.conf files
<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	ServerName $vpname.local
	DocumentRoot \"/var/www/$pName/public_html\"
</VirtualHost>

# add this to /etc/hosts
# 127.0.0.1 $vpname.local
' > ./$pName/public_html/$vpname.local",
                "chmod -R 775 $pName", 
                "chmod 777 $pName"
        );
        $this->createModule("default", $pName);
        /**
         * instead of copying templates directly we can do following processes
         * + Create appliaction/boostrap
         * + Creat application/routes
         * + Crate public_html
         * + Create defaultModule
         *      + COPYING defaultBootstrap.php correspondingly
         * + COPYING ModuleController.php directly
         * + Creating IndexController
         * + Creating IndexAction in IndexController 
         *      + Creating IndexView.phtml correspondingly
         * + COPYING defaultLayout.phtml directly
         */
        $this->Run($opt, 0);
    }
    
    public function module($args)
    {
        if(!$this->CheckZG())
            return;
        
        $this->restrictArgCount($args, 1);
        
        $this->createModule($args[0]);
    }
    protected function createModule($name ,$projectDir = ".")
    {
        $s = $this->GetStatus($projectDir);
        
        if(!file_exists($s->modules->meta->path))
            mkdir($s->modules->meta->path, 0775);
        
        if(\zinux\kernel\utilities\fileSystem::resolve_path("{$s->modules->meta->path}/{$name}Module"))
            throw new \zinux\kernel\exceptions\invalideOperationException("Module '{$name}' already exists ...");
            
        $module = new \zinux\zg\vendor\item("{$name}Module", "{$s->modules->meta->path}/{$name}Module");
        $s->modules->modules[] = $module;
        $this->SaveStatus($s);
        
        $this ->cout("Creating new module '", 0, self::defColor, 0)
                ->cout("{$module->name}", 0, self::yellow, 0)
                ->cout("' ...");
        $this->Run(array(
                "mkdir {$module->path}",
                "cd {$module->path} && mkdir controllers",
                "cd {$module->path} && mkdir models",
                "cd {$module->path} && mkdir views",
                "cd {$module->path}/views && mkdir view",
                "cd {$module->path}/views && mkdir helper",
                "cd {$module->path}/views && mkdir layout",
                "chmod 775 -R {$module->path}"    
        ));
        new \zinux\zg\vendor\moduleBoostrap($module, new \zinux\zg\vendor\Item("{$name}Bootstrap", $module->path."/{$name}Bootstrap.php"), $projectDir);
    }
}
