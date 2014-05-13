<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'myYii Backend',

    // preloading 'log' component
    'preload' => array(
        'log',
        'input', //Filter
        'bootstrap', // preload the bootstrap component
    ),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.restfullyii.components.*',
        'application.extensions.bootstrap.components.*',
        //DEBUGGING STUFF
        'application.vendors.FirePHPCore.FirePHP',
        'application.vendors.FirePHPCore.FB',
    ),
    'aliases' => array(
        //yiibooster
        'bootstrap' => 'webroot.protected.extensions.bootstrap',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '1',
            'generatorPaths' => array(//    'bootstrap.gii', // since 0.9.1
            ),
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),

    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        //email
        'mailer' => array(
            'class' => 'application.extensions.mailer.EMailer',
        ),
        'phpThumb'=>array(
                  'class'=>'ext.EPhpThumb.EPhpThumb',
                  //'options'=>array(optional phpThumb specific options are added here)
              ),

        //filter,security
        'input' => array(
            'class' => 'CmsInput',
            'cleanPost' => true,
            'cleanGet' => true,
            'cleanMethod' => 'stripClean'
        ),
        //yiibooster
        'bootstrap' => array(
            //KSBootstrap extends Bootstrap just to load assets directly  from a folder in  webroot (yiibooster_assets),and
            //avoid publishing which I hate.
            'class' => 'ext.bootstrap.components.KSBootstrap',
            'coreCss' => true,
            'responsiveCss' => true
        ),

        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => require(dirname(__FILE__).'/../extensions/restfullyii/config/routes.php'),
        ),

        /*'db'=>array(
            'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
        ),*/
        // uncomment the following to use a MySQL database

        'db'=>(!APP_DEPLOYED)?
                                                         array(    //LOCALHOST
                                                        'class' => 'CDbConnection',
                                                        'connectionString' => 'mysql:host=localhost;dbname=balgarin',
                                                        'username' => 'root',
                                              			 'password' => '',
                                                         'charset' => 'UTF8',
                                                         'tablePrefix'=>'', // even empty table prefix required!!!
                  		                               'emulatePrepare' => true,
                  			                           'enableProfiling' => true,
                                                         'schemaCacheID' => 'cache',
                                                         'queryCacheID'=> 'cache',
                                                         'schemaCachingDuration' => 120
                  	                                    ):   //SERVER
                                                        array(
                                                         'class' => 'CDbConnection',
                                                            'connectionString' => 'mysql:host=localhost;dbname=balgarin',
                                                                                                       'username' => 'root',
                                                                                                        'password' => '',
                                                        'charset' => 'UTF8',
                                                        'tablePrefix' => '',
                                                        'emulatePrepare' => true,
                                                     //   'enableProfiling' => true,
                                                       'schemaCacheID' => 'cache',
                                                       'schemaCachingDuration' => 3600
                                              ),


        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
            ),
        ),
        'clientScript' => array(
            'class' => 'CClientScript',
            'scriptMap' => array(
                'jquery.js' => false,
                'jquery.min.js' => false
            ),
            'coreScriptPosition' => CClientScript::POS_END,
        ),

    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'fromEmail' => 'admin-balgarin@gmail.com',
        'replyEmail' => 'reply-to@gmail.com',
        'myEmail'=>'balgarin@gmail.com',
        'gmail_password'=>'balgarin@gmail.com',
        'RESTusername' => 'admin',
        'RESTpassword' => '1',
        // 'recaptcha_private_key'=>'[FILL IN YOUR KEY]',// captcha will not work without these keys!
        //  'recaptcha_public_key'=>'[FILL IN YOUR KEY]',http://www.google.com/recaptcha
        'contactRequireCaptcha' => false,
        //Choose Bootswatch theme,default is default bootstrap theme.See http://bootswatch.com/
        //Options:default,slate,amelia,cerulean,cyborg, journal,readable,simplex,spacelab,superhero,united
        'bootswatch_theme' => 'slate'
        //'bootswatch_theme'=>'default'

    ),
);