zg
==

[Zinux](https://github.com/dariushha/zinux) Generator Tool
--
This is a tool designed to create, manipulate <i>[zinux](https://github.com/dariushha/zinux)</i> projects. <i>Zinux generator tool</i> is an efficient appliction designed to make use of <i>zinux</i>
project even easier than it is, and also makes you develop more, in a short time.<br />

> From now on we will refer to <b><i>Zinux Generator Tool</i></b> by <b><i>zg</i></b>. 

Topics
--
* [Requirements](#requirements)
* [Installation](#installation)
  * [Windows Users](#windows-users) 
* [Commands Types](#command-types)
	* [<b><i>Version</i></b>](#version)
	* [<b><i>Help</i></b>](#help)
	* [<b><i>New</i></b>](#new)
		* [New Project](#new-project)
		* [New Module](#new-module)
		* [New Controller](#new-controller)
		* [New Action](#new-action)
		* [New View](#new-view)
		* [New Layout](#new-layout)
		* [New Model](#new-model)
		* [New Helper](#new-helper)
		* [New Application](#new-application)
			* [New Appliction Bootstrap](#new-application-bootstrap)
			* [New Appliction Routes](#new-application-routes)
	* [<b><i>Remove</i></b>](#remove)
	* [<b><i>Build</i></b>](#build)
	* [<b><i>Config</i></b>](#config)
	* [<b><i>Security</i></b>](#security)
	* [<b><i>Status</i></b>](#status)
	* [<b><i>Update</i></b>](#update)

Requirements
--
* PHP version 5.3.10 or greater
* [Zinux](https://github.com/dariushha/zinux) Project 3.0.0

Installation
--
There is a [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) shell script, it will 
automatically download and configure your system to use <i>zinux</i> project freely in your system.<br />
It also installs [zinux generator tool](https://github.com/dariushha/zg) which is an handy tool to create, manipulate
and provides solid security for your <i>zinux</i> project, for more information see
[Zinux Generator Tool](#zinux-generator-tool).

> You will need to have [Git](http://git-scm.com/) installed before using
[zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer). 

For installation just download the [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) 
and save it at anywhere, and run the following command `bash /path/to/your/zinux/installer` it will do the reset. 

Windows Users
--
For running shell scripts in Windows you need a third-party application installed in your system 
to enable using shell scripts in Windows, such as:
* [GnuWin32](http://gnuwin32.sourceforge.net/)
* [UnxUtils](http://unxutils.sourceforge.net/)

> <b>Note:</b> [zinux generator tool](https://github.com/dariushha/zg) also uses shell scripts to run faster, 
so before you use [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) or start using
[zinux generator tool](https://github.com/dariushha/zg) make sure your Windows supports shell scripts, i.e if 
`ls -l` command lists your directories, you are OK! 

Command Types
==
The <i>zg</i> uses very simple and flexible command lines. Except [Security](#security) command line which is an 
sensitive command all other commands has aliases which is the short form of original command. A list of available 
commands and their details is as follow:<br />

Version
--
<b>Title</b><br />
Shows Version.
<hr />
<b>Description</b><br />
Show both Zinux's and Zinux Generator's versions.
<hr />
<b>Command</b><br />
```PHP
zg --version
```
<hr />
<b>Help</b><br />
```PHP
zg -h --version
```
<hr />

Help
--
<b>Title</b><br />
Prints help content.
<hr />
<b>Description</b><br />
Prints help content.
* It can be general help, which will print all commands help content.
* It can be specific, which will print only the help content of target command.

<hr /> 
<b>Command</b><br />
```PHP
zg -h ($command) (--heads)
```
<hr />
<b>Alias</b><br />
```PHP
zg -h ($command_alias) (--heads)
```
<hr />
<b>Help</b><br />
```PHP
zg -h -h
```
<hr />
<b>Optionals</b><br />
* <b>$command</b> : print a specific command's help content.
* <b>--heads</b>  : if you pass this argument it will only print valid command lines under `$command` command line.


<hr />
<b>Default Values</b><br />
* <b>$command</b> : If you don't pass `$command` it will print all commands' help content. 

<hr />
<b>Notes</b><br />
* `$command` should be a valid command in `zg` command list.

<hr />
<b>Examples</b><br />
```PHP
# prints all commands' help content
zg
# OR using aliases :
zg -h
```
```PHP
# prints 'zg new'command's help content
zg -h new 
# OR using aliases :
zg -h n
```
<b>Or you can be more specific, like:</b>
```PHP
# prints 'zg new action' command's help content
zg -h new action
# OR using aliases :
zg -h n a
```
<hr />
New
--
New Project
--
<b>Title</b><br />
Create new project.
<b>Description</b><br />
This command will creates new project and its initial files and directories are:
* application
	* appBootstrap.php
	* appRoutes.php 
* modules/defaultModule
	* controller
		* indexController.php
	* views/layout
		* defaultLayout.phtml
	* views/view/index
		* indexView.phtml
	* defaultBootstrap.php	
* public_html
	* index.php
	* .htaccess
* [zinux](https://github.com/dariushha/zinux) framework

<hr />
<b>Command</b><br />
```PHP
zg new project $project_name (--empty)
```
<hr />
<b>Alias</b><br />
```PHP
zg new $project_name (--empty)
```
<hr />
<b>Help</b><br />
```PHP
zg -h n p
```
<hr />
<b>Optionals</b><br />
* <b>--empty</b> : By passing this option it will create an empty project without any modules or application directory.

<hr />
<b>Notes</b><br />
> In entire <i>zg</i> commands [spectial characters](http://en.wikipedia.org/wiki/Special_characters) in arguments will converted to '`_`' character.

<hr /> 
<b>Examples</b><br />
```PHP
# creates new project direcroty named 'test
zg new project test
# or using aliases: 
zg n test
```

<hr />
New Module
--
<b>Title</b><br />
Create new module for project.
<hr />
<b>Description</b><br />
This command will creates new module and its initial files and directories are:
* modules/MODULE_NAME
	* controller
		* indexController.php
	* views/layout
		* MODULE_NAMELayout.phtml
	* views/view/index
		* indexView.phtml
	* defaultBootstrap.php	

<hr />
<b>Command</b><br />
```PHP
zg new module $module_name
```

<hr />
<b>Alias</b><br />
```PHP
zg n module $module_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h n module
```
<hr />
<b>Examples</b><br />
```PHP
# creates new module name 'admin'
zg new module admin
# or using aliases: 
zg n module admin
```
```PHP
# creates new module name 'ssl'
zg new module ssl
# or using aliases: 
zg n module ssl
```
<hr />
New Controller
--
<b>Title</b><br />
Create new controller.
<hr />
<b>Description</b><br />
Creates new controller in a module and its initial files and directories are:
* modules/MODULE_NAME
	* controller
		* CONTROLLER_NAMEController.php
	* views/view/CONTROLLER_NAME
		* indexView.phtml

<hr />
<b>Command</b><br />
```PHP
zg new controller $controller_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n c $controller_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n c
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to create the contoller in it.

<hr />
<b>Default Values</b><br />
* <b>$module_name</b> : <i>defaultModule</i>
	* If no module name supplied by default <i>defaultModule</i> will be targeted.

<hr />

<b>Notes</b><br />
> The `Module` and `Controller` postfixes are not needed at end of `$module_name` and `$controller_name` names.

<hr />
<b>Examples</b><br />
```PHP
# creates new contoller name 'auth' in 'defaultModule'
zg new controller auth
# or more specific:
zg new controller authController defaultModule 
# or using aliases: 
zg n c auth
```
```PHP
# creates new contoller name 'comments' in 'userModule'
zg new controller comments user
# or more specific:
zg new controller commentsController userModule 
# or using aliases: 
zg n c comment user
```

<hr />
New Action
--
<b>Title</b><br />
Create new action function.
<hr />
<b>Description</b><br />
Creates new action function in any desired, controller and module.
<hr />
<b>Command</b><br />
```PHP
zg new action $action_name ($controller_name) ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n a $action_name ($controller_name) ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n a
```
<hr />
<b>Optionals</b><br />
* <b>$controller_name</b> : The name of target controller that we want to create the action in it.
* <b>$module_name</b>     : The name of target module that contains `$controller_name`.

<hr />
<b>Default Values</b><br />
* <b>$controller_name</b> : <i>indexController</i>
* <b>$module_name</b>     : <i>defaultModule</i>

<hr />
<b>Notes</b><br />
> The `Module`,`Controller`,`Action` postfixes are not needed at end of `$module_name`, `$controller_name` and `$action_name` names.

<hr />
<b>Examples</b><br />
```PHP
# creates new action name 'help' in 'indexController', 'defaultModule'
zg new action help
# or using aliases: 
zg n a help
```
```PHP
# creates new action name 'login' in 'authController', 'sslModule'
zg new action login auth ssl
# or using aliases: 
zg n a login auth ssl
```

<hr />
New View
--
<b>Title</b><br />
Create new view.
<hr />
<b>Description</b><br />
Creates new view related to a controller in any desired module.
<hr />
<b>Command</b><br />
```PHP
zg new view $view_name ($controller_name) ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n a $view_name ($controller_name) ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n v
```
<hr />
<b>Optionals</b><br />
* <b>$controller_name</b> : The name of target controller that we want to relate the view with it.
* <b>$module_name</b>     : The name of target module that contains `$controller_name`.

<hr />
<b>Default Values</b><br />
* <b>$controller_name</b> : <i>indexController</i>
* <b>$module_name</b>     : <i>defaultModule</i>

<hr />
<b>Notes</b><br />
> The `Module`,`Controller`,`View` postfixes are not needed at end of `$module_name`, `$controller_name` and `$view_name` names.

<hr />
<b>Examples</b><br />
```PHP
# creates new view name 'help2' in 'indexController', 'defaultModule'
zg new view help2
# or using aliases: 
zg n v help2
```
```PHP
# creates new view name 'login2' in 'authController', 'sslModule'
zg new view login2 auth ssl
# or using aliases: 
zg n v login2 auth ssl
```

<hr />
New Layout
--
<b>Title</b><br />
Create new layout.
<hr />
<b>Description</b><br />
Creates new layout in a module.

<hr />
<b>Command</b><br />
```PHP
zg new layout $layout_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n l $layout_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n l
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to create the contoller in it.

<hr />
<b>Default Values</b><br />
* <b>$module_name</b> : <i>defaultModule</i>
	* If no module name supplied by default <i>defaultModule</i> will be targeted.

<hr />

<b>Notes</b><br />
> The `Module` and `Layout` postfixes are not needed at end of `$module_name` and `$layout_name` names.

<hr />
<b>Examples</b><br />
```PHP
# creates new layout name 'print' in 'defaultModule'
zg new layout print 
# or using aliases: 
zg n l print
```
```PHP
# creates new layout name 'dark' in 'userModule'
zg new layout dark user 
# or using aliases: 
zg n l dark user
```

<hr />
Remove
--

Build
--
Config
--
Security
--
Status
--
Update
--
