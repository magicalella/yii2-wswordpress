# yii2-wswordpress
 Modulo per integrare la  Yii con Wordpress
 
 Documentazione API WP
 https://developer.wordpress.org/rest-api/
 
 Installation
 ------------
 
 The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
 
 Run
 
 ```
 php composer.phar require magicalella/yii2-wswordpress 
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
          'class' => 'magicalella\wswordpress\Wswordpress',
          'endpoint' => 'https://miosito.it/wp-json/',
          'method' => 'GET',
          'consumer_key' => 'ck_xxx',//for Woocommerce
          'consumer_secret' => 'cs_xxx'//for Woocommerce
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
 
