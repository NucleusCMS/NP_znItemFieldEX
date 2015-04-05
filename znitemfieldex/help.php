<?php
	$language = str_replace( array('\\','/'), '', getLanguageName());
    $plugin_path = str_replace('\\','/',dirname(__FILE__));
    $dir_name = trim(substr($plugin_path,strrpos($plugin_path,'/')),'/');
    $plugin_dir = "plugins/{$dir_name}/";
    $plugin_path = $plugin_path . '/';
    $p = (isset($_GET['p'])) ? htmlspecialchars($_GET['p'],ENT_QUOTES,_CHARSET) : '';
	$help_path = "{$plugin_path}{$language}.help{$p}.php";
	$k = array();
	foreach($_GET as $k=>$v)
    {
    	if($k==='ticket') continue;
    	if($k==='p') continue;
    	$pear[] = "{$k}={$v}";
	}
	$url = $manager->addTicketToUrl('?'.join('&',$pear));
	if(is_file($help_path)){
		$content=include_once($help_path);
	}
	else{
		$content=file_get_contents("{$plugin_path}help.html");
	}
