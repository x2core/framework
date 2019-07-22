<?php

namespace X2Core\Util;


use DOMDocument;
use DOMElement;

/**
 * Class DOM
 * @package X2Core\Util
 *
 * Several Utilities to work xml and html document
 */
class DOM
{
    /**
     * @var DOMDocument
     */
    private $document;

    /**
     * @var DOMElement
     */
    private $root;

    /**
     * DOM constructor.
     * @param string $version
     * @param string $root
     */
    public function __construct($version = "1.0", $root = "root")
    {
        if($version === false){
            return;
        }
        $this->document = new DOMDocument($version);
        $this->root = $this->document->createElement($root);
        $this->document->appendChild($this->root);
    }

    /**
     * @param array $arr
     * @param DOMElement|NULL $node
     * @return $this
     */
    public function appendArray(array $arr, DOMElement $node = NULL){
        if(is_null($node)){
            $node = $this->root;
        }
        if(isset($arr['*value'])){
            $node->nodeValue = $arr['*value'];
            unset($arr['*value']);
        }
        foreach ($arr as $key => $value){
            if(is_array($value)){
                $node->appendChild($child = new DOMElement($key));
                $this->appendArray($value, $child);
            }else{
              $node->setAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * @param array $arr
     * @param DOMElement $container
     * @return $this
     * @throws \DOMException
     * @internal param DOMElement|NULL $node
     */
    public function appendModel(array $arr, DOMElement $container = NULL){
        if(is_null($container)){
            $container = $this->root;
        }

        if(isset($arr['name'])){
            $container->appendChild($child = new \DOMElement($arr['name']));
        }else{
            throw new \DOMException('Array Model should has a name');
        }

        if(isset($arr['value'])){
            $valueNode = $arr['value'];
            if(is_array($valueNode)){
                foreach ($valueNode as $childModel){
                    $this->appendModel($childModel, $child);
                }
            }else{
                $child->nodeValue = $valueNode;
            }
        }

        if(isset($arr['attributes'])){
            foreach ($arr['attributes'] as $attr => $attrVal){
                $child->setAttribute($attr, $attrVal);
            }
        }

        return $this;
    }

    /**
     * @param $filename
     */
    public function saveData($filename){
        $this->document->save($filename);
    }

    /**
     * @return mixed
     */
    public function toXML(){
        return $this->document->saveXML($this->root);
    }

    /**
     * @return mixed
     */
    public function toHTML(){
        return $this->document->saveHTML($this->root);
    }

//    /**
//     * @param array|null $arr
//     * @param DOMElement|null $context
//     * @return array
//     */
//    public function saveInArray(array &$arr, DOMElement $context = NULL){
//        if($context === NULL){
//            $context = $this->document->firstChild;
//        }
//
//        $list = $context->childNodes;
//        $length = $list->length;
//        for($i = 0; $i < $length; $i++){
//           $current = $list->item($i);
//           $this->saveInArray( $arr[$current->nodeName] = [], $current);
//        }
//    }

    /**
     * @param $filename
     * @return $this
     */
    static public function fromFile($filename){
        $document = new DOMDocument();
        return (new DOM(false))->setDocument($document->load($filename));
    }

    /**
     * @param $source
     * @return $this
     */
    static public function fromHTML($source){
        $document = new DOMDocument();
        return (new DOM(false))->setDocument($document->loadHTML($source));
    }

    /**
     * @param $source
     * @return $this
     */
    static public function fromXML($source){
        $document = new DOMDocument();
        return (new DOM(false))->setDocument($document->loadXML($source));
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->root->nodeName;
    }

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param mixed $document
     * @return $this
     */
    public function setDocument(DOMDocument $document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * @return DOMElement
     */
    public function getRoot()
    {
        return $this->root;
    }
}