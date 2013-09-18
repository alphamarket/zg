<?php
namespace zinux\zg\resources\operator;

class _new extends baseOperator
{
    public function project($args)
    {
        if(!count($args))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Empty argument passed ...");
        
        $pName = implode("-", $args);
        
        $this ->cout("Creating new project '", 0, self::defColor, 0)
                ->cout("$pName", 0, self::yellow, 0)
                ->cout("' ...");
        $vpname = str_replace(" ", "-", $pName);
        $opt = array(
                "mkdir $pName",
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
        $this->Run($opt);
        $this->CreateStatusFile($pName);
    }
}
