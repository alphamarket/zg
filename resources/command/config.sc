{
        "title":"Configure zinux generator for current project", 
        "alias":"c",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\config",
            "method":"config"
        },
        "help":
        {
            "command":"zg config $options",
            "alias": "zg c $options",
            "detail":"Config the project with given options"
        }, 
        "options":
        {
                "-history":"Enable history recording",
                "+history":"Disable history recording"
        },
        "show":
        {
                "title":"Show configurations for current project", 
                "alias":"s",
                "instance":
                {
                    "class":"\\zinux\\zg\\resources\\operator\\config",
                    "method":"show"
                },
                "help":
                {
                    "command":"zg config show",
                    "alias": "zg c s",
                    "detail":"Output configurations for current project"
                }
        }
}