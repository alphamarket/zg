<?php
namespace application;

class appBootstrap extends \zinux\kernel\application\applicationBootstrap
{
    public function PRE_CHECK(\zinux\kernel\routing\request &$request)
    {
        /**
         * this is a pre-strap function use this on pre-bootstrap opt.
         * @param \zinux\kernel\routing\request $request 
         */
    }
    
    public function post_FOO(\zinux\kernel\routing\request $request)
    {
        /**
         * this is a post-strap function use this on post-bootstrap opt.
         * @param \zinux\kernel\routing\request $request 
         */
    }
}