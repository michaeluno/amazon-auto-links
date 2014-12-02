Dim objShell
Set objShell = WScript.CreateObject ( "WScript.shell" )
objShell.run "cmd /K php inclusion_class_list\php-class-inclusion-list-creator.php"
Set objShell = Nothing