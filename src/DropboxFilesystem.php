<?php
/**
 * @link https://github.com/mazpaijo/yii2-flysystem
 * @copyright Copyright (c) 2015 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace mazpaijo\flysystem;

use Dropbox\Client;
use Mazpaijo\Flysystem\Dropbox\DropboxAdapter;
use yii\base\InvalidConfigException;

/**
 * DropboxFilesystem
 *
 * @author Alexander Kochetov <mazpaijo@gmail.com>
 */
class DropboxFilesystem extends Filesystem
{
    /**
     * @var string
     */
    public $token;
    /**
     * @var string
     */
    public $app;
    /**
     * @var string|null
     */
    public $prefix;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->token === null) {
            throw new InvalidConfigException('The "token" property must be set.');
        }

        if ($this->app === null) {
            throw new InvalidConfigException('The "app" property must be set.');
        }

        parent::init();
    }

    /**
     * @return DropboxAdapter
     */
    protected function prepareAdapter()
    {
        return new DropboxAdapter(
            new Client($this->token, $this->app),
            $this->prefix
        );
    }
}
