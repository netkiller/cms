<?php

return new \Phalcon\Config(array(
	'database' => array(
		'adapter'  => 'Mysql',
		'host'     => 'localhost',
		'port'     => 3306,
		'username' => 'root',
		'password' => 'Chen_1980',
		'dbname'     => 'inf',

	),
        'mongodb' => array(
        'host'     => 'mongodb://neo:chen@192.168.6.1/test',
		'port'     => '',
		'username' => '',
		'password' => '',
		'dbname'   => 'test',
        		
        'fileinfo' =>array(
        				'maxSize'=>1000000,
        				'type'=>'image',//array('gif', 'jpg', 'jpeg', 'png', 'bmp')
        				'savePath'=> php_uname('s')=='Windows NT' ? 
        					(dirname($_SERVER["DOCUMENT_ROOT"]).'/images/') //测试环境的
        					:'/www/netkiller.cn/inf.netkiller.cn/images/',//线上
        		),
        		
        ),
	'application' => array(
		'controllersDir' => __DIR__ . '/../../app/controllers/',
		'modelsDir'      => __DIR__ . '/../../app/models/',
		'viewsDir'       => __DIR__ . '/../../app/views/',
		'pluginsDir'     => __DIR__ . '/../../app/plugins/',
		'libraryDir'     => __DIR__ . '/../../app/library/',
        'formsDir'       => __DIR__ . '/../../app/forms/',
		'imagesDir'		 => '/www/netkiller.cn/inf.netkiller.cn/images/',
		'imagesUri'		 => 'http://inf.netkiller.cn/',
		'baseUri'        => '',
		'templateDir'=> include('template.php'),

	),
	'redis' => array(
		'host' => '192.168.4.1',

//		'host' => '127.0.0.1',

		'port' => '6379'
	),
	'models' => array(
		'metadata' => array(
			'adapter' => 'Memory'
		)
	),
	
));
