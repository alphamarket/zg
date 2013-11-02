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
		* [Encryption](#encryption)
		* [Decryption](#decryption)
		* [Cryption Cache](#cryption-cache)
	* [<b><i>Status</i></b>](#status)
	* [<b><i>Update</i></b>](#update)

Requirements
--
* PHP version 5.3.10 or greater
* [Zinux](https://github.com/dariushha/zinux) Project 3.0.0 or greater

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
For technical reasons zinux generator does not support Windows!!<br />
We are sorry about this....
 
> Some advise for PHP developers, you cannot become a professional PHP developer and develop a full
scale PHP application within windows, you just cannot!! so may be it is time to move your PHP developments 
on linux.

Command Types
==
The <i>zg</i> uses very simple and flexible command lines. Except [Security](#security) command line which is an 
sensitive command all other commands has aliases which is the short form of original command. A list of available 
commands and their details are as follow:<br />

Version
--
<b>Title</b><br />
Shows Version.
<hr />
<b>Description</b><br />
Show both Zinux's and Zinux Generator's versions.
<hr />

```PHP
# Command
zg --version
```


```PHP
# Help
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

```PHP
# Command
zg -h ($command) (--heads)
```


```PHP
# Alias
zg -h ($command_alias) (--heads)
```


```PHP
# Help
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

```PHP
# Command
zg new project $project_name (--empty)
```

```PHP
# Alias
zg n $project_name (--empty)
```

```PHP
# Help
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

```PHP
# Command
zg new module $module_name
```

```PHP
# Alias
zg n module $module_name
```

```PHP
# Help
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

```PHP
# Command
zg new controller $controller_name ($module_name)
```



```PHP
# Alias
zg n c $controller_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new action $action_name ($controller_name) ($module_name)
```



```PHP
# Alias
zg n a $action_name ($controller_name) ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new view $view_name ($controller_name) ($module_name)
```



```PHP
# Alias
zg n a $view_name ($controller_name) ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new layout $layout_name ($module_name)
```



```PHP
# Alias
zg n l $layout_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new model $model_name ($module_name)
```



```PHP
# Alias
zg n m $model_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new helper $helper_name ($module_name)
```



```PHP
# Alias
zg n h $helper_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg new application boostrap $bootstrap_name
```



```PHP
# Alias
zg n app b $bootstrap_name
```



```PHP
# Help
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

```PHP
# Command
zg new application routes $routes_name
```



```PHP
# Alias
zg n app r $routes_name
```



```PHP
# Help
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

```PHP
# Command
zg remove module $module_name
```



```PHP
# Alias
zg r module $module_name
```



```PHP
# Help
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

```PHP
# Command
zg remove controller $controller_name ($module_name)
```



```PHP
# Alias
zg r c $controller_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove action $action_name ($controller_name) ($module_name)
```



```PHP
# Alias
zg r a $action_name ($controller_name) ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove view $view_name ($controller_name) ($module_name)
```



```PHP
# Alias
zg r a $view_name ($controller_name) ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove layout $layout_name ($module_name)
```



```PHP
# Alias
zg r l $layout_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove model $model_name ($module_name)
```



```PHP
# Alias
zg r m $model_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove helper $helper_name ($module_name)
```



```PHP
# Alias
zg r h $helper_name ($module_name)
```



```PHP
# Help
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

```PHP
# Command
zg remove application boostrap $bootstrap_name
```



```PHP
# Alias
zg r app b $bootstrap_name
```



```PHP
# Help
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

```PHP
# Command
zg remove application routes $routes_name
```



```PHP
# Alias
zg r app r $routes_name
```



```PHP
# Help
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

```PHP
# Command
zg build ( -m ) ( -a ) ( -p )
```



```PHP
# Alias
zg b ( -m ) ( -a ) ( -p )
```



```PHP
# Help
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

```PHP
# Command
zg config $options
```



```PHP
# Alias
zg c $options
```



```PHP
# Help
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
Sometimes you need to send your PHP project or carray it with yourself but don't want to expose your project codes accidentally to public access, The <i>zg</i> come up with an solution, 
with <i>zg</i> you can encrypt or decrypt your project's files with any key you want.

> Since the encryption/decryption are sensitive operations the <i>zg</i> didn't come up with aliases for this operations, to prevent un-wanted mistakes. 
 
Encryption
--
<b>Title</b><br />
Encrypt your project. 
<hr />
<b>Description</b><br />
This command will encrypt all of your project's files except the zinux project under your project, with is ofcourse a public project. 
<hr />
<b>Command</b><br />
```PHP
# Command
 zg security encrypt $encryption_key (-i #)
```
```PHP
# Help
zg -h security encrypt
```
<hr />
<b>Optionals</b><br />
* <b>-i</b> : Number of encryption iterations. This should be greater than 0.

<hr />
<b>Default Values</b><br />
* <b>-i</b> : 1

<hr />
<b>Notes</b><br />
> Be careful with command if you make mistake it is possible to blow up your project.
<hr />
>  When you encrypt your project, you cannot lost your '.zg' folder and its content. The project without its '.zg' will be too risky to encrypt!
<hr />
> This command will encrypt your entire project except the 'zinux' folder. Which obviously is a public project!
<hr />
> If you ever forger your $encryption_key, your project will be lost for good. So make sure you pass a easy to remember but hard to guess crypt key.
<hr />
> If your $encryption_key is a multiline key put it between quotation marks!

<hr />
<b>Examples</b><br />
```PHP
# This will encrypt your project with a key the for 1 time
zg security encrypt "Some multi-line KEY 
to 3nCRypT"
```
```PHP
# This will encrypt your project with a key the for 20 times
zg security encrypt "Some single-line KEY BUt with iteration value" -i 20
```
<hr />
Decryption
--
<b>Title</b><br />
Decrypt your project. 
<hr />
<b>Description</b><br />
This command will decrypt your project's encrypted files, the files that registered as encrypted in `zg security encrypt` command. 
<hr />
<b>Command</b><br />
```PHP
# Command
 zg security decrypt $decryption_key (-i #)
```
```PHP
# Help
zg -h security encrypt
```
<hr />
<b>Optionals</b><br />
* <b>-i</b> : Number of decryption iterations. This should be greater than 0.

<hr />
<b>Default Values</b><br />
* <b>-i</b> : 1

<hr />
<b>Notes</b><br />
> Be careful with command if you make mistake it is possible to blow up your project.
<hr />
>  For decryption you have to pass exact key value and exact iteration number to get right decrypted codes.
<hr />
> This command will decrypt your entire project which encrypted by 'zg security encrypt' command!!
<hr />
> If your $decryption_key is a multiline key put it between quotation marks!

<hr />
<b>Examples</b><br />
```PHP
# This will decrypt your project with key same as encryption key the for 1 time
zg security decrypt "Some multi-line KEY 
to 3nCRypT"
```
```PHP
# This will encrypt your project with key same as encryption key the for 20 times
zg security encrypt "Some single-line KEY BUt with iteration value" -i 20
```
<hr />
Cryption Cache
--
<b>Title</b><br />
Cryption cache operator.
<hr />
<b>Description</b><br />
Provides operations on cryption cache.
<hr />
```PHP
# Command
zg security cache (--clear|--reset)
```
```PHP
# Help
zg -h security cache
```
<hr />
<b>Optionals</b><br />
* <b>--clear</b> : Clears all cryption cached data and files.
* <b>--reset</b> : Reset files to before previous DEcryption operation. With this option zg will undo LAST `zg security decryption` command's effects!

<hr />
<b>Details</b><br />
> You cannot <b>clear cryption cache</b> while project flaged as encrypted.

<hr />
<b>Notes</b><br />
> This command provides fail-safe for `zg security decryption` lets assume your have passed a wrong cryption key and you forced `zg security decryption` to decrypt the
project while the key is wrong!!!<br />
<b>What happens then?</b> you project's codes will blow-up! you will lost your code files!!<br />
In above cases you case do `zg security cache --reset` command to undo <b>LAST</b> `zg security decryption` command's effects!!
<hr />
> `zg security cache --reset` will undo the <b>last</b> `zg security decryption` command's effects!

<hr />
<b>Examples</b><br />
```PHP
# undo the LAST `zg security decryption` command's effects
zg security --reset
```
```PHP
# After decrypting the project, clears the cache data and files
zg security --cache
```
<hr />
Status
--
<b>Title</b><br />
Show project status.
<hr />
<b>Description</b><br />
This command will output <i>zg</i>'s report from project status. 
<hr />
<b></b><br />
```PHP
# Command
zg status (+p) (+d : #) ($section_name)
```
```PHP
# Alias
zg s (+p) (+d : #) ($section_name)s
```
```PHP
# Help
zg -h s 
```
<hr />
<b>Optionals</b><br />
* <b>+p</b> : Show items parent detail in structure tree.
* <b>+d</b> : The depth # that recursion should proceed to.
* <b>$section_name</b> : Narrow down your explore items by passing its path from root to target section name. e.g 'zg status modules collection defaultmodule':.Will show the details about defaultmodule!

<hr />
<b>Default Values</b><br />
* <b>$section_name</b> : By default this command will show the entire status object.
* <b>+d</b> : 5

<hr />
<b>Examples</b><br />
```PHP
# print project's entire status report
zg status
# or using aliases: 
zg s
```
```PHP
# print any reports zg has on default module in the project
zg status modules collection defaultmodule
# or using aliases: 
zg s modules collection defaultmodule
```
<hr />
Update
--
<b>Title</b><br />
Update zinux framework
<hr />
<b>Description</b><br />
Update zinux framework with its online repository.
<hr />
```PHP
# Command
zg update  ( $branch_name ) (--all) (--cache | --simulate | --verbose)
```
```PHP
# Alias
zg u  ( $branch_name ) (--all) (--cache | --simulate | --verbose) 
```
```PHP
# Help
zg -h u 
```
<hr />
<b>Optionals</b><br />
* <b>$branch_name</b> 

<hr />
<b>Default Values</b><br />
* <b>$branch_name</b> : master

<hr />
<b>Notes</b><br />
> <b>WARNING:</b> This command will `stash` any local changes before updating branches. Be aware of this!

<hr />
<b>Examples</b><br />
```PHP
# updated project's module's master branches
zg update
# or using aliases: 
zg u
```
```PHP
# simulate updating project's module's master branches
# no change will be apply
zg update --simulate
# or using aliases: 
zg u --simulate
```
```PHP
# updates project's module's all branches
zg update --all
# or using aliases: 
zg u --all
```
```PHP
# updates installed zinux framework's cache's module
# if you update your cached zinux, from then on every
# project you create will be updated 
zg update --cache
# or using aliases: 
zg u --cache
```
<hr />
