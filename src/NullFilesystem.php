<?php
/**
 * @link https://github.com/mazpaijo/yii2-flysystem
 * @copyright Copyright (c) 2015 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace mazpaijo\flysystem;

use Mazpaijo\Flysystem\Adapter\NullAdapter;

/**
 * NullFilesystem
 *
 * @author Alexander Kochetov <mazpaijo@gmail.com>
 */
class NullFilesystem extends Filesystem
{
    /**
     * @return NullAdapter
     */
    protected function prepareAdapter()
    {
        return new NullAdapter();
    }
}
