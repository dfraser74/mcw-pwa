<?
    require(dirname(__FILE__).'/../../../../wp-load.php');
    require(dirname(__FILE__).'/../functions.php');
    etag_start();
    header('Service-Worker-Allowed: /');
    header('Content-Type: application/javascript');
    
    $pwa_assets=get_option('pwa_assets');

    echo "importScripts('".plugin_dir_url(__FILE__)."node_modules/workbox-sw/build/importScripts/workbox-sw.dev.v2.1.2.js');";
    echo "\r\n";
    echo "let jsassets=[\r\n ";
    foreach( $pwa_assets['scripts'] as $src ) :
        echo $src!==FALSE? "'".$src."',\r\n ":"";
    endforeach;
    echo '];';
    echo "\r\n";
    echo "let cssassets=[\r\n";
    foreach( $pwa_assets['styles'] as $src ) :
        echo $src!==FALSE? "'".$src."',\r\n":"";
    endforeach;
    
    echo '];';
    readfile('sw.js');
    etag_end();
  ?>