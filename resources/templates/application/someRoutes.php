<?php
namespace application;
/**
 * This is a class to add custom-routes to route maps
 */
class someRoutes extends \zinux\kernel\routing\routerBootstrap
{
    public function Fetch()
    {
        /**
         * Route Example For This:
         *      /note/1234/edit/what/so/ever?nonsences=passed => /note/edit/1234/what/so/ever?nonsences=passed 
         */
        #$this->addRoute("/note/$1/edit$2", "/note/edit/$1$2");
        #$this->addRoute("/note/$1/delete$2", "/note/delete/$1$2");
    }
}