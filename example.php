<?php

define( 'DEVELOPMENT_MODE', 1 );

set_include_path(
    '.' . PATH_SEPARATOR . __DIR__ . '/Libraries'   .
    '.' . PATH_SEPARATOR . __DIR__ . '/Libraries/vendor'
);

date_default_timezone_set( 'America/Sao_Paulo' );

require_once 'Next/Loader/AutoLoader.php';
require_once 'Next/Loader/AutoLoader/Stream.php';

$autoloader = new Next\Loader\AutoLoader;
$autoloader -> registerAutoloader( new Next\Loader\AutoLoader\Stream );

Next\Components\Debug\Handlers::register();

use Next\Components\Debug\Exception;

$metaProvider = new Cartographer\Providers\Video\Meta(
    [
      'uploadDateIsPublishingDate' => TRUE,
      'urls' =>
      [
        'http://chaoticonline.tk/videos/season-1/welcome-to-chaotic-part-1/'
      ],
    ]
);

try {

    $cartographer = new Cartographer\Cartographer(
        [
          'provider' => $metaProvider,
          'pen'      => new Cartographer\Drawing\Pens\XML,
          'paper'    => new Cartographer\Drawing\Papers\Response
        ]
    );

    if( $cartographer -> publish() ) {
        echo 'Sitemap generated successfully';
    }

} catch( Exception $e ) {

    echo 'Oh snap! ', $e -> getMessage();
}