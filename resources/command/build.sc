{
    "title":"Build a new zg configuration", 
    "alias":"b",
    "instance":
    {
        "class":"\\zinux\\zg\\resources\\operator\\build",
        "method":"build"
    },
    "help":
    {
        "command":"zg build ( -m ) ( -a ) ( -p )",
        "alias": "zg b ( -m ) ( -a ) ( -p )",
        "detail":"Builds a new zinux project configuration, based on currently existed zinux project.<br /> if no module directory supplied 'modules' would be targeted"
    },
    "options":
    {
        "-p":"Project's root directory.<br />(zg will analyzing this folder's sub-structures.)",
        "-a":"Project's application directory, a relative path from '-p' argument.",
        "-m":"Modules' root directory, a relative path from '-p' argument.<br />(zg will consider this folder's sub-structures as project's modules.)"
    },
    "defaults":
    {
        "-m":"modules",
        "-a":"application",
        "-p":". [ CURRENT_DIR ]"
    },
    "notes":
    [
        "This is a fail-safe command to restore zinux projects<br />that has lost their configuration file.",
        "You can use this command to rebuild the current project<br />config file if you ever messed-up with something.",
        "'zg build' only supports folder/file structures that is compatible<br />with standard zinux framework's defined directory structure."
    ],
    "log":
    {
        "title":"Print build logs", 
        "alias":"l",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\build",
            "method":"log"
        },
        "help":
        {
            "command":"zg build log (--all | --events | --proc | --clear)",
            "alias": "zg b l (--all | --events | --proc | --clear)",
            "detail":"Print build logs produced by 'zg build' command.<br />By default outputs events log!"
        },
        "options":
        {
            "--all":"Prints both 'event' and 'processed items' logs.",
            "--events":"Prints only 'event' logs.",
            "--proc":"Prints only 'processed items' logs.",
            "--clear":"Clear logs."
        }
    }
}