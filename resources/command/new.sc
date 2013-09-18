{
        "project":
        {
            "title":"Create new project", 
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"project"
            },
            "help":
            {
                "command":"zf new project $project_name",
                "detail":"Creates a new zinux project"
            }
        },
        "module":
        {
            "title":"Add new module", 
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"module"
            },
            "help":
            {
                "command":"zf new module $module_name",
                "detail":"Creates a new module for project"
            }
        },
        "test":
        {
                "title":"test", 
                "instance":
                {
                    "class":"\\zinux\\zg\\resources\\operator\\_new",
                    "method":"test"
                },
                "help":
                {
                    "command":"zf new test $test_name",
                    "detail":"Creates a new test for project"
                }, 
                "hihi":
                {
                        "title":"Add new hihi", 
                        "instance":
                        {
                            "class":"\\zinux\\zg\\resources\\operator\\_new",
                            "method":"hihi"
                        },
                        "help":
                        {
                            "command":"zf new test hihi $module_name",
                            "detail":"Creates a new hihi for project"
                        }
                }
        }
        
}