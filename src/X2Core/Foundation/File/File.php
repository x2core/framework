<?php

namespace X2Core\Foundation\File;

/**
 * Class File
 * @package X2Core\Foundation\File
 *
 * Util class to manage files
 */
class File implements \Iterator, \Countable
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var resource
     */
    private $fileObj;

    /**
     *
     * File constructor.
     *
     * @param $filename
     */
    public function __construct($filename)
    {

        $this->filename = $filename;
    }

    /**
     * Fetch hash file content
     *
     * @param $filename
     * @static
     *
     * @return string
     */
    public static function hash($filename){
        return md5_file($filename);
    }

    /**
     * Check if exist the file
     *
     * @return bool
     */
    public function exists(){
        return file_exists($this->filename);
    }

    /**
     *
     * Check if the target is file
     *
     * @return bool
     */
    public function isFile(){
        return is_file($this->filename);
    }

    /**
     *
     * Fetch pathInfo basename about file
     *
     * @return string
     */
    public function basename(){
        return pathinfo($this->filename, PATHINFO_BASENAME);
    }

    /**
     *
     * Fetch pathInfo dirname about file
     *
     * @return string
     */
    public function dirname(){
        return pathinfo($this->filename, PATHINFO_DIRNAME);
    }

    /**
     *
     * Fetch pathInfo extension about file
     *
     * @return string
     */
    public function extension(){
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    /**
     *
     * Create resource of file open
     *
     * @param string $mode
     * @return $this
     */
    public function open($mode = 'r'){
        $this->fileObj = fopen($this->filename, $mode);
        return $this;
    }

    /**
     *
     * Destroy resource and close file
     *
     * @return void
     */
    public function close(){
         fclose($this->fileObj);
    }

    /**
     *
     * Check if the file is open
     *
     * @return bool
     */
    public function isOpen(){
        return is_resource( $this->fileObj );
    }

    /**
     *
     * Read file and to move a offset
     *
     * @return bool|string
     */
    public function read(){
        return fgets($this->fileObj /*,($length !== NULL OR is_int($length)) ? $length : 1*/);
    }

    /**
     * @param $moves
     * @return int
     */
    public function toIndex($moves){
        return fseek($this->fileObj, $moves);
    }

    /**
     * @return bool|int
     */
    public function index(){
        return ftell($this->fileObj);
    }

    /**
     * @return array
     */
    public function info(){
       return fstat($this->fileObj);
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->filename;
    }

    /**
     * @param $src
     */
    public function write($src){
        fwrite($this->fileObj, $src);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
       return fread($this->fileObj, 1);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        fseek($this->fileObj, 1);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return ftell($this->fileObj);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return !feof($this->fileObj);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        rewind($this->fileObj);
    }

    /**
     * @param $new_path
     * @return $this
     */
    public function copy($new_path){
        $newFile = new File($new_path);
        $newFile->open('w');
        foreach ($this as $data){
            $newFile->write($data);
        }
        $newFile->close();
        return $this;
    }

    /**
     * @param $newName
     * @param $context
     * @return $this
     */
    public function rename($newName, $context){
        rename($this->filename, $newName, $context);
        return $this;
    }

    /**
     *
     * Delete the file that is target
     *
     * @return  $this
     */
    public function delete(){
        if($this->isOpen()){
            $this->close();
        }
        unlink($this->filename);
        return $this;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return filesize($this->filename);
    }

    /**
     *
     * Get the file php handler resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->fileObj;
    }
}