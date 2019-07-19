<?php

namespace X2Core\Foundation\Services;


use Closure;
use ArrayAccess;
use Doctrine\Common\Cache\PhpFileCache;
use Eyrene\Cache\Exceptions\CacheDeleteException;
use Eyrene\Cache\Exceptions\CacheOptionException;
use X2Core\Exceptions\CacheConnectorException;
use X2Core\Util\Arr;

class Cache implements ArrayAccess
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $connector;

    /**
     * @var array
     */
    private $record;

    /**
     * Cache constructor.
     * @param $type
     * @param array $options
     */
    public function __construct($type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * Destruct Cache
     * @return void
     */
    public function __destruct()
    {
        $this->unload();
    }

    /**
     * @param string $id
     * @param mixed $data
     * @param int $lifetime
     * @return Cache
     */
    public function make($id, $data, $lifetime = 0){
        array_push($this->record,$id);
        $this->connector->save($id,$data,$lifetime);
        return $this;
    }

    /**
     * @param string $id
     * @param mixed $data
     * @param Closure|null $hydrateAction
     * @return Cache
     */
    public function hydrate($id, $data, Closure $hydrateAction = null){
        $payload = $this->connector->fetch($id);
        if(is_null($hydrateAction)){
//            self::hydrateCache($payload,$data);
        }else{
            $payload = $hydrateAction($payload,$data);
        }
        $this->connector->save($id,$payload,0);
        return $this;
    }


    /**
     * @param string$id
     * @return mixed
     */
    public function consume($id){
        return $this->connector->fetch($id);
    }

    /**
     * @return array|null
     */
    public function verbose(){
        return $this->connector->getStats();
    }

    /**
     * @param string $id
     * @return bool
     */
    public function deposited($id){
        return $this->connector->contains($id);
    }

    /**
     * @param string $id
     * @return $this
     * @throws CacheDeleteException
     */
    public function forget($id){
        try{
            if(!$this->connector->delete($id))
                throw new CacheDeleteException($id);
        }catch (CacheDeleteException $e){
            throw $e;
        }
         return $this;
    }

    /**
     * @throws CacheConnectorException
     * @throws CacheOptionException
     */
    public function load()
    {
        switch ($this->type){
            case 'file':
                Arr::require($this->options,['path'], CacheOptionException::class);
                $this->connector = new PhpFileCache($this->options['path']);
                break;
            default:
                throw new CacheConnectorException($this->type);
        }
        $this->record = $this->connector->contains('__record_ids') ?  $this->connector->fetch('__record_ids') : [];
    }

    /**
     * @return void
     */
    public function unload()
    {
        $this->connector->save('__record_ids',$this->connector);
        unset($this);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return  $this->deposited($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->consume($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->make($offset,$value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws CacheDeleteException
     */
    public function offsetUnset($offset)
    {
            $this->forget($offset);
    }
}