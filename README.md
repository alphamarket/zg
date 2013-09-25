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
			* [New Application Bootstrap](#new-application-bootstrap)
			* [New Application Routes](#new-application-routes)
	* [<b><i>Remove</i></b>](#remove)
		* [Remove Module](#remove-module)
		* [Remove Controller](#remove-controller)
		* [Remove Action](#remove-action)
		* [Remove View](#remove-view)
		* [Remove Layout](#remove-layout)
		* [Remove Model](#remove-model)
		* [Remove Helper](#remove-helper)
		* [Remove Application](#remove-application)
			* [Remove Application Bootstrap](#remove-application-bootstrap)
			* [Remove Application Routes](#remove-application-routes)
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
Create new module.
<hr />
<b>Description</b><br />
This command will creates new module for project and its initial files and directories are:
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

<b>Notes</b><br />
> The `Module` postfix is not needed at end of `$module_name`.

<hr />
<b>Examples</b><br />
```PHP
# creates new module named 'admin'
zg new module admin
# or using aliases: 
zg n module admin
```
```PHP
# creates new module named 'ssl'
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
# creates new contoller named 'auth' in 'defaultModule'
zg new controller auth
# or more specific:
zg new controller authController defaultModule 
# or using aliases: 
zg n c auth
```
```PHP
# creates new contoller named 'comments' in 'userModule'
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
# creates new action named 'help' in 'indexController', 'defaultModule'
zg new action help
# or using aliases: 
zg n a help
```
```PHP
# creates new action named 'login' in 'authController', 'sslModule'
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
# creates new view named 'help2' in 'indexController', 'defaultModule'
zg new view help2
# or using aliases: 
zg n v help2
```
```PHP
# creates new view named 'login2' in 'authController', 'sslModule'
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
# creates new layout named 'print' in 'defaultModule'
zg new layout print 
# or using aliases: 
zg n l print
```
```PHP
# creates new layout named 'dark' in 'userModule'
zg new layout dark user 
# or using aliases: 
zg n l dark user
```

<hr />
New Model
--
<b>Title</b><br />
Create new model.
<hr />
<b>Description</b><br />
Creates new model in a module.

<hr />
<b>Command</b><br />
```PHP
zg new model $model_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n m $model_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n m
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
> For free uses of models there is no naming convention in models, i.e in layouts when you
execute the `zg new layout dark` it will create a layout named <b>darkLayout</b> with a <b>Layout</b>
post-appended name.<br />
In models there is no such post-appending in names, so for example if you execute the `zg new model user` it will create a model in `defaultModule` named exactly as you type i.e <b>user</b> and if you execute `zg new model userModel` it will exactly creates a model named <b>userModel</b>, etc.
<hr />
> The `Module` postfix is not needed at end of `$module_name`.

<hr />
<b>Examples</b><br />
```PHP
# creates new model named 'user' in 'defaultModule'
zg new model user
# or using aliases: 
zg n m user
```
```PHP
# creates new model named 'adminModel' in 'userModule'
zg new model adminModel user
# or using aliases: 
zg n m adminModel user
```

<hr />
New Helper
--
<b>Title</b><br />
Create new heler.
<hr />
<b>Description</b><br />
Creates new helper in a module.

<hr />
<b>Command</b><br />
```PHP
zg new helper $helper_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg n h $helper_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h n h
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
> The `Module` and `Helper` postfixes are not needed at end of `$module_name` and `$helper_name`.

<hr />
<b>Examples</b><br />
```PHP
# creates new helper named 'generics' in 'defaultModule'
zg new helper generics
# or using aliases: 
zg n h generics
```
```PHP
# creates new helper named 'validators' in 'userModule'
zg new helper validators user
# or using aliases: 
zg n h validators user
```
New Application
--
New Application Bootstrap
--
<b>Title</b><br />
Create new application bootstrap for project.
<hr />
<b>Description</b><br />
This command will creates new application bootstrap under `PROJECT-ROOT/application` directory. 
<hr />
<b>Command</b><br />
```PHP
zg new application boostrap $bootstrap_name
```

<hr />
<b>Alias</b><br />
```PHP
zg n app b $bootstrap_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h n app b
```
<hr />

<b>Notes</b><br />
> The `Bootstrap` postfix is not needed at end of `$bootstrap_name`.

<hr />
<b>Examples</b><br />
```PHP
# creates new application bootstrap named 'db'
zg new application bootstrap db
# or using aliases: 
zg n app b db
```
```PHP
# creates new application bootstrap named 'ssl'
zg new application bootstrap ssl
# or using aliases: 
zg n app b ssl
```
<hr />
New Application Routes
--
<b>Title</b><br />
Create new application routes for project.
<hr />
<b>Description</b><br />
This command will creates new application routes under `PROJECT-ROOT/application` directory. 
<hr />
<b>Command</b><br />
```PHP
zg new application routes $routes_name
```

<hr />
<b>Alias</b><br />
```PHP
zg n app r $routes_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h n app r
```
<hr />

<b>Notes</b><br />
> The `Routes` postfix is not needed at end of `$routes_name`.

<b>Examples</b><br />
```PHP
# creates new application routes named 'comment'
zg new application routes comment
# or using aliases: 
zg n app r comments
```
```PHP
# creates new application routes named 'ssl'
zg new application routes ssl
# or using aliases: 
zg n app r ssl
```
<hr />
Remove
--
Remove Module
--
<b>Title</b><br />
Removes an existed module.
<hr />
<b>Description</b><br />
This command will Remove an existed module from project.

<hr />
<b>Command</b><br />
```PHP
zg remove module $module_name
```

<hr />
<b>Alias</b><br />
```PHP
zg r module $module_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h r module
```
<hr />

<b>Notes</b><br />
> The `Module` postfix is not needed at end of `$module_name`.

<hr />
<b>Examples</b><br />
```PHP
# removes an existed module named 'admin'
zg remove module admin
# or using aliases: 
zg r module admin
```
```PHP
# removes an existed module named 'ssl'
zg remove module ssl
# or using aliases: 
zg r module ssl
```
<hr />
Remove Controller
--
<b>Title</b><br />
Removes an existed controller.
<hr />
<b>Description</b><br />
Removes an existed controller from module.

<hr />
<b>Command</b><br />
```PHP
zg remove controller $controller_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r c $controller_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r c
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to remove the contoller from it.

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
# removes an existed contoller named 'auth' from 'defaultModule'
zg remove controller auth
# or more specific:
zg remove controller authController defaultModule 
# or using aliases: 
zg r c auth
```
```PHP
# removes an existed contoller named 'comments' from 'userModule'
zg remove controller comments user
# or more specific:
zg remove controller commentsController userModule 
# or using aliases: 
zg r c comment user
```

<hr />
Remove Action
--
<b>Title</b><br />
Removes an existed action function.
<hr />
<b>Description</b><br />
Removes an existed action function from any desired, controller and module.
<hr />
<b>Command</b><br />
```PHP
zg remove action $action_name ($controller_name) ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r a $action_name ($controller_name) ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r a
```
<hr />
<b>Optionals</b><br />
* <b>$controller_name</b> : The name of target controller that we want to remove the action from it.
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
# removes an existed action named 'help' from 'indexController', 'defaultModule'
zg remove action help
# or using aliases: 
zg r a help
```
```PHP
# removes an existed action named 'login' from 'authController', 'sslModule'
zg remove action login auth ssl
# or using aliases: 
zg r a login auth ssl
```

<hr />
Remove View
--
<b>Title</b><br />
Removes an existed view.
<hr />
<b>Description</b><br />
Removes an existed view related to a controller from any desired module.
<hr />
<b>Command</b><br />
```PHP
zg remove view $view_name ($controller_name) ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r a $view_name ($controller_name) ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r v
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
# removes an existed view named 'help2' from 'indexController', 'defaultModule'
zg remove view help2
# or using aliases: 
zg r v help2
```
```PHP
# removes an existed view named 'login2' from 'authController', 'sslModule'
zg remove view login2 auth ssl
# or using aliases: 
zg r v login2 auth ssl
```

<hr />
Remove Layout
--
<b>Title</b><br />
Removes an existed layout.
<hr />
<b>Description</b><br />
Removes an existed layout from module.

<hr />
<b>Command</b><br />
```PHP
zg remove layout $layout_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r l $layout_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r l
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to remove the contoller from it.

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
# removes an existed layout named 'print' from 'defaultModule'
zg remove layout print 
# or using aliases: 
zg r l print
```
```PHP
# removes an existed layout named 'dark' from 'userModule'
zg remove layout dark user 
# or using aliases: 
zg r l dark user
```

<hr />
Remove Model
--
<b>Title</b><br />
Removes an existed model.
<hr />
<b>Description</b><br />
Removes an existed model from a module.

<hr />
<b>Command</b><br />
```PHP
zg remove model $model_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r m $model_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r m
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to remove the contoller from it.

<hr />
<b>Default Values</b><br />
* <b>$module_name</b> : <i>defaultModule</i>
	* If no module name supplied by default <i>defaultModule</i> will be targeted.

<hr />

<b>Notes</b><br />
> For free uses of models there is no naming convention in models, i.e in layouts when you
execute the `zg remove layout dark` it will remove a layout named <b>darkLayout</b> with a <b>Layout</b>
post-appended name.<br />
In models there is no such post-appending in names, so for example if you execute the `zg remove model user` 
it will remove a model in `defaultModule` named exactly as you type i.e <b>user</b> and if you execute 
`zg remove model userModel` it will exactly remove a model named <b>userModel</b>, etc.
<hr />
> The `Module` postfix is not needed at end of `$module_name`.

<hr />
<b>Examples</b><br />
```PHP
# removes an existed model named 'user' from 'defaultModule'
zg remove model user
# or using aliases: 
zg r m user
```
```PHP
# removes an existed model named 'adminModel' from 'userModule'
zg remove model adminModel user
# or using aliases: 
zg r m adminModel user
```

<hr />
Remove Helper
--
<b>Title</b><br />
Removes an existed heler.
<hr />
<b>Description</b><br />
Removes an existed helper from a module.

<hr />
<b>Command</b><br />
```PHP
zg remove helper $helper_name ($module_name)
```

<hr />
<b>Alias</b><br />
```PHP
zg r h $helper_name ($module_name)
```

<hr />
<b>Help</b><br />
```PHP
zg -h r h
```
<hr />
<b>Optionals</b><br />
* <b>$module_name</b> : The name of target module that we want to remove the contoller from it.

<hr />
<b>Default Values</b><br />
* <b>$module_name</b> : <i>defaultModule</i>
	* If no module name supplied by default <i>defaultModule</i> will be targeted.

<hr />

<b>Notes</b><br />
> The `Module` and `Helper` postfixes are not needed at end of `$module_name` and `$helper_name`.

<hr />
<b>Examples</b><br />
```PHP
# removes an existed helper named 'generics' from 'defaultModule'
zg remove helper generics
# or using aliases: 
zg r h generics
```
```PHP
# removes an existed helper named 'validators' from 'userModule'
zg remove helper validators user
# or using aliases: 
zg r h validators user
```
Remove Application
--
Remove Application Bootstrap
--
<b>Title</b><br />
Removes an existed application bootstrap for project.
<hr />
<b>Description</b><br />
This command will Removes an existed application bootstrap from `PROJECT-ROOT/application` directory. 
<hr />
<b>Command</b><br />
```PHP
zg remove application boostrap $bootstrap_name
```

<hr />
<b>Alias</b><br />
```PHP
zg r app b $bootstrap_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h r app b
```
<hr />

<b>Notes</b><br />
> The `Bootstrap` postfix is not needed at end of `$bootstrap_name`.

<hr />
<b>Examples</b><br />
```PHP
# removes an existed application bootstrap named 'db'
zg remove application bootstrap db
# or using aliases: 
zg r app b db
```
```PHP
# removes an existed application bootstrap named 'ssl'
zg remove application bootstrap ssl
# or using aliases: 
zg r app b ssl
```
<hr />
Remove Application Routes
--
<b>Title</b><br />
Removes an existed application routes for project.
<hr />
<b>Description</b><br />
This command will Removes an existed application routes from `PROJECT-ROOT/application` directory. 
<hr />
<b>Command</b><br />
```PHP
zg remove application routes $routes_name
```

<hr />
<b>Alias</b><br />
```PHP
zg r app r $routes_name
```

<hr />
<b>Help</b><br />
```PHP
zg -h r app r
```
<hr />

<b>Notes</b><br />
> The `Routes` postfix is not needed at end of `$routes_name`.

<b>Examples</b><br />
```PHP
# removes an existed application routes named 'comment'
zg remove application routes comment
# or using aliases: 
zg r app r comments
```
```PHP
# removes an existed application routes named 'ssl'
zg remove application routes ssl
# or using aliases: 
zg r app r ssl
```
<hr />

Build
--
<b>Title</b><br />
Re-build <i>zg</i> config file. 
<hr />
<b>Description</b><br />
Sometimes happen you manipulate your project entities manually instead of using <i>zg</i>, i.e you
may add a controller named `fooController` manually in `defaultController` and next time you may want 
to a new action named `barAction` to `fooController`, but since `fooController` created manually it 
doesn't exist in <i>zg</i> manifest list, so it won't recognize `fooController` and an error will raise like:
```
# output result of : 
# zg n a bar foo

Zinux Generator(vX.X.X) by Dariush Hasanpoor [b.g.dariush@gmail.com] 2013
[ Error occured ]
    Controller 'defaultModule/fooController' does not exist in zg manifest!
    Try 'zg build' command!
```
In such cases <i>zg</i> provided a simple solution and that is `zg build` command.<br />
It will build up <i>zg</i> manifest list from scratch to top, any [zinux entity](https://github.com/dariushha/zinux#mvc-entities)
will be re-registered again, including in our example case `fooController` which made manually.<br />
In our example case after `zg build` you are good to go with `zg n a bar foo`.

> There is an other usecase of `zg build` options an is that when you have lost or corrupted your <i>zg</i>
config files, this command will be useful. 

<hr />
<b>Command</b><br />
```PHP
zg build ( -m ) ( -a ) ( -p )
```

<hr />
<b>Alias</b><br />
```PHP
zg b ( -m ) ( -a ) ( -p )
```

<hr />
<b>Help</b><br />
```PHP
zg -h b
```
<hr />
<b>Optionals</b><br />
* <b>-m</b> : `-m /app/modules/path` 
* <b>-a</b> : `-a /app/application/folder/path`
* <b>-p</b> : `-p /app/project/path`


<hr />
<b>Default Values</b><br />
* <b>-m</b> : <i>modules</i> 
* <b>-a</b> : <i>application</i>
* <b>-p</b> : <i>.</i>(Current Directory)

<hr />
<b>Notes</b><br />
> In many normal cases you don't have to enter any of `zg build`'s options at all, as long as you lauch 
`zg build` in any project's root folder it will built up the <i>zg</i> manifest file.  

<hr />
<b>Examples</b><br />
```PHP
# builds a zg manifest under current folder
# note : we are in /path/to/PROJECT-ROOT when we are doing this
zg build
# or using aliases: 
zg b
```
```PHP
# builds a zg manifest for an application
zg build -p "/path/to/target/app" -m "/relative/path/from/app/root/to/modules/" -a "/relative/path/to/application"  
# or using aliases: 
zg b -p "/path/to/target/app" -m "/relative/path/from/app/root/to/modules/" -a "/relative/path/to/application"
```
<hr />

Config
--
<b>Title</b><br />
Configure zinux generator.
<hr />
<b>Description</b><br />
Configure zinux generator for current project with given options.
<hr />
<b>Command</b><br />
```PHP
zg config $options
```

<hr />
<b>Alias</b><br />
```PHP
zg c $options
```

<hr />
<b>Help</b><br />
```PHP
zg -h c
```
<hr />
<b>Notes</b><br />
As i am wrting this document, there are only 2 options available for configuration:
* <b>-show-parents</b> : Skip parent property in 'zg status' command.
* <b>+show-parents</b> : Do not skip parent property in 'zg status' command.

<hr />
<b>Examples</b><br />
```PHP
zg config +show-parents
# or using aliases: 
zg c -show-parents
```
<hr />
Security
--
Status
--
Update
--
