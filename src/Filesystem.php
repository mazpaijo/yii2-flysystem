<?php
/**
 * @link https://github.com/mazpaijo/yii2-flysystem
 * @copyright Copyright (c) 2015 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace mazpaijo\flysystem;

use Mazpaijo\Flysystem\AdapterInterface;
use Mazpaijo\Flysystem\Cached\CachedAdapter;
use Mazpaijo\Flysystem\Filesystem as NativeFilesystem;
use Mazpaijo\Flysystem\Replicate\ReplicateAdapter;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;

/**
 * Filesystem
 *
 * @method \Mazpaijo\Flysystem\FilesystemInterface addPlugin(\Mazpaijo\Flysystem\PluginInterface $plugin)
 * @method void assertAbsent(string $path)
 * @method void assertPresent(string $path)
 * @method boolean copy(string $path, string $newpath)
 * @method boolean createDir(string $dirname, array $config = null)
 * @method boolean delete(string $path)
 * @method boolean deleteDir(string $dirname)
 * @method \Mazpaijo\Flysystem\Handler get(string $path, \Mazpaijo\Flysystem\Handler $handler = null)
 * @method \Mazpaijo\Flysystem\AdapterInterface getAdapter()
 * @method \Mazpaijo\Flysystem\Config getConfig()
 * @method array|false getMetadata(string $path)
 * @method string|false getMimetype(string $path)
 * @method integer|false getSize(string $path)
 * @method integer|false getTimestamp(string $path)
 * @method string|false getVisibility(string $path)
 * @method array getWithMetadata(string $path, array $metadata)
 * @method boolean has(string $path)
 * @method array listContents(string $directory = '', boolean $recursive = false)
 * @method array listFiles(string $path = '', boolean $recursive = false)
 * @method array listPaths(string $path = '', boolean $recursive = false)
 * @method array listWith(array $keys = [], $directory = '', $recursive = false)
 * @method boolean put(string $path, string $contents, array $config = [])
 * @method boolean putStream(string $path, resource $resource, array $config = [])
 * @method string|false read(string $path)
 * @method string|false readAndDelete(string $path)
 * @method resource|false readStream(string $path)
 * @method boolean rename(string $path, string $newpath)
 * @method boolean setVisibility(string $path, string $visibility)
 * @method boolean update(string $path, string $contents, array $config = [])
 * @method boolean updateStream(string $path, resource $resource, array $config = [])
 * @method boolean write(string $path, string $contents, array $config = [])
 * @method boolean writeStream(string $path, resource $resource, array $config = [])
 *
 * @author Alexander Kochetov <mazpaijo@gmail.com>
 */
abstract class Filesystem extends Component
{
    /**
     * @var \Mazpaijo\Flysystem\Config|array|string|null
     */
    public $config;
    /**
     * @var string|null
     */
    public $cache;
    /**
     * @var string
     */
    public $cacheKey = 'flysystem';
    /**
     * @var integer
     */
    public $cacheDuration = 3600;
    /**
     * @var string|null
     */
    public $replica;
    /**
     * @var \Mazpaijo\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $adapter = $this->prepareAdapter();

        if ($this->cache !== null) {
            /* @var Cache $cache */
            $cache = Yii::$app->get($this->cache);

            if (!$cache instanceof Cache) {
                throw new InvalidConfigException('The "cache" property must be an instance of \yii\caching\Cache subclasses.');
            }

            $adapter = new CachedAdapter($adapter, new YiiCache($cache, $this->cacheKey, $this->cacheDuration));
        }

        if ($this->replica !== null) {
            /* @var Filesystem $filesystem */
            $filesystem = Yii::$app->get($this->replica);

            if (!$filesystem instanceof Filesystem) {
                throw new InvalidConfigException('The "replica" property must be an instance of \mazpaijo\flysystem\Filesystem subclasses.');
            }

            $adapter = new ReplicateAdapter($adapter, $filesystem->getAdapter());
        }

        $this->filesystem = new NativeFilesystem($adapter, $this->config);
    }

    /**
     * @return AdapterInterface
     */
    abstract protected function prepareAdapter();

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->filesystem, $method], $parameters);
    }

    /**
     * @return \Mazpaijo\Flysystem\FilesystemInterface
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }
    
}
