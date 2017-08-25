<?php

/**
 * The Cartographer | Cartographer\Cartographer.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer;

use Next\Components\Debug\Exception;      # Exception Class

use Next\Components\Object;               # Object Class

use Cartographer\Providers\Provider;      # Data Provider Interface
use Cartographer\Drawing\Pens\Pen;        # Drawing Pen Interface
use Cartographer\Drawing\Papers\Paper;    # Drawing Paper Interface

/**
 * The Cartographer gets its tools to publish a Sitemap using the
 * data coming from one of the Providers
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Debug\Exception, \Next\Components\Object,
 *             \Cartographer\Provider\Provider, \Cartographer\Drawing\Pens\Pen
 */
class Cartographer extends Object {

    /**
     * Additional Initialization.
     * Checks Integrity of Cartographer's Parameter Options
     */
    public function init() {
        $this -> checkIntegrity();
    }

    /**
     * Asks Provider to build the Sitemap Nodes' Composite, drawing it
     * with provided Drawing Pen and then Publishes it
     *
     * @param array|optional $options
     *  Additional arguments to be passed to
     *  \Cartographer\Drawing\Pens\Pen::write()
     *
     * @return mixed|void
     *  Return what the provided Drawing Pen returns, if anything
     */
    public function publish( array $options = [] ) {

        $this -> options -> provider
                         -> draw( $this -> options -> pen );

        return $this -> options -> paper -> publish(
            $this -> options -> pen -> write( $options )
        );
    }

    // Auxiliary Methods

    /**
     * Checks the integrity of Parameter Options
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if the required Parameter Option 'provider' is missing
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'pen' is missing
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'paper' is missing
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'provider' is not an
     *  instance of \Cartographer\Providers\Provider
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'pen' is not an
     *  instance of \Cartographer\Drawing\Pens\Pen
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if required Parameter Option 'paper' is not an
     *  instance of \Cartographer\Drawing\Papers\Paper
     */
    private function checkIntegrity() {

        if( ! isset( $this -> options -> provider ) ) {

            throw new Exception(
                'Missing required Parameter <strong>provider</strong>', Exception::WRONG_USE
            );
        }

        if( ! isset( $this -> options -> pen ) ) {

            throw new Exception(
                'Missing required Parameter <strong>pen</strong>', Exception::WRONG_USE
            );
        }

        if( ! isset( $this -> options -> paper ) ) {

            throw new Exception(
                'Missing required Parameter <strong>paper</strong>', Exception::WRONG_USE
            );
        }

        if( ! $this -> options -> provider instanceof Provider ) {

            throw new Exception(

                'Parameter <strong>provider</strong> must be an instance of <em>Cartographer\Providers\Provider<em>',

                Exception::WRONG_USE
            );
        }

        if( ! $this -> options -> pen instanceof Pen ) {

            throw new Exception(

                'Parameter <strong>pen</strong> must be an instance of <em>Cartographer\Drawing\Pens\Pen<em>',

                Exception::WRONG_USE
            );
        }

        if( ! $this -> options -> paper instanceof Paper ) {

            throw new Exception(

                'Parameter <strong>paper</strong> must be an instance of <em>Cartographer\Drawing\Papers\Paper<em>',

                Exception::WRONG_USE
            );
        }
    }
}