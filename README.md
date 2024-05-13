# yii2-wswordpress
 Modulo per integrare la  Yii con Wordpress
 
 
 Installation
 ------------
 
 The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
 
 Run
 
 ```
 composer require "magicalella/yii2-wswordpress" "*"
 ```
 
 or add
 
 ```
 "magicalella/yii2-wswordpress": "*"
 ```
 
 to the require section of your `composer.json` file.
 
 Usage
 -----
 
 1. Add component to your config file
 ```php
 'components' => [
     // ...
     'wswordpress' => [
         'class' => 'magicalella\wswordpress\WsWordpress',
         'endpoint' => 'URL API Wordpress',
         'method' => 'GET'
     ],
 ]
 ```
 
 2. Get dato to WP
 ```php
 $wswordpress = Yii::$app->wswordpress;
 $result = $wswordpress->call('wp/v2/...',[
     'param'=>value_param
     ]
 );
 ```
 
