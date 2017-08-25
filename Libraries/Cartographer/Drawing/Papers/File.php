<?php

/**
 * Drawing "Paper": File Writer | Cartographer\Drawing\Papers\File.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Papers;

use Next\Components\Debug\Exception;              # Exception Class
use Next\HTTP\Stream\Adapter\AdapterException;    # HTTP Stream Adapter Exception

use Next\Components\Object;                       # Object Class
use Next\HTTP\Stream\Adapter\Socket;              # HTTP Socket Adapter Class
use Next\HTTP\Stream\Writer;                      # HTTP Stream Writer Class

/**
 * Draws the Sitemap Nodes' Composite as a XML Node structure
 *
 * @package    Cartographer
 *
 * @uses       Next\Components\Debug\Exception,
 *             \Next\HTTP\Stream\Adapter\AdapterException,
 *             \Next\Components\Object, \Next\HTTP\Stream\Adapter\Socket,
 *             \Next\HTTP\Stream\Writer
 *             \Cartographer\Drawing\Papers\Paper
 */
class File extends Object implements Paper {

    /**
     * File Paper Default Options
     *
     * @var array $defaultOptions
     */
    protected $defaultOptions = [
        'filename' => 'sitemap.xml'
    ];

    /**
     * Additional Initialization.
     * Checks File Paper Parameter Options Integrity
     */
    public function init() {
        $this -> checkIntegrity();
    }

    // Paper Interface Method Implementation

    /**
     * Publishes Sitemap drawn
     *
     * @param string $data
     *  Sitemap Drawn
     *
     * @return boolean|void
     *  TRUE if Sitemap was generated successfully or
     *  'nothing' if an Exception is caught and re-thrown
     */
    public function publish( $data ) {

        try {

            $writer = new Writer(
                new Socket(
                    sprintf( '%s/%s', $this -> options -> destination, $this -> options -> filename ), Socket::TRUNCATE_WRITE
                )
            );

            $writer -> write( $data );

            return TRUE;

        } catch( AdapterException $e ) {
            throw new Exception( $e -> getMessage(), Exception::UNFULFILLED_REQUIREMENTS );
        }
    }

    // Auxiliary Methods

    /**
     * Checks File Paper Parameter Options Integrity
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'destination' is missing
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'destination' is not a valid Directory
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'destination' is not writable
     */
    private function checkIntegrity() {

        if( ! isset( $this -> options -> destination ) ) {
            throw new Exception( 'Missing required Parameter <strong>destination</strong>', Exception::WRONG_USE );
        }

        if( ! is_dir( $this -> options -> destination ) ) {
            throw new Exception( 'Invalid output directory', Exception::WRONG_USE  );
        }

        if( ! is_writable( $this -> options -> destination ) ) {
            throw new Exception( 'Output directory is not writable', Exception::WRONG_USE );
        }
    }
}