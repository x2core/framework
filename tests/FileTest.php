<?php


final class FileTest extends TestsBasicFramework
{

    /**
     * @return mixed
     */
    public function run()
    {
        $file = new \X2Core\Foundation\File\File(__DIR__ . DIRECTORY_SEPARATOR .'elm.txt');
        $file->open();
        $el = $file->read();
        $file->close();
        $this->assert($el,'a');
    }
}