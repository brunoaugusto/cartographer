<?php

/**
 * Drawing "Paper": HTTP Response | Cartographer\Drawing\Papers\Response.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Papers;

use Next\HTTP\Response\ResponseException;           # Response Exception Class
use Next\Components\Object;                         # Object Class
use Next\HTTP\Headers\Fields\Entity\ContentType;    # Content-Type Entity Header

/**
 * Draws the Sitemap Nodes' Composite as a XML Node structure
 *
 * @package    Cartographer
 *
 * @uses       \Next\HTTP\Response\ResponseException, \Next\Components\Object,
 *             \Next\HTTP\Response, \Next\HTTP\Headers\Fields\Entity\ContentType,
 *             \Cartographer\Drawing\Papers\Paper
 */
class Response extends Object implements Paper {

    // Paper Interface Method Implementation

    /**
     * Publishes Sitemap drawn
     *
     * @param string $data
     *  Sitemap Drawn
     *
     * @return boolean
     *  Always TRUE
     */
    public function publish( $data ) {

        $response = new \Next\HTTP\Response;

        try {

            $response -> addHeader(
                new ContentType( [ 'value' => 'text/xml' ] )
            );

        } catch( ResponseException $e ) {}

        $response -> appendBody( $data ) -> send();

        return TRUE;
    }
}