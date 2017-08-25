<?php

/**
 * Data ProviderInterface | Cartographer\Providers\Provider.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Providers;

use Next\Components\Interfaces\Filterable;    # Filterable Interface

/**
 * A Provider Object can be used to retrieve structured and validated
 * data from different sources
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Interfaces\Filterable
 */
interface Provider extends Filterable {}