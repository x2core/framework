<?php

namespace X2Core\Foundation\Http;

use Closure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class RequestHandler
 * @package X2Core\Foundation\Http
 */
abstract class RequestHandler
{
    /**
     * @var Closure[]
     */
    protected $gates = [];

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var resource
     */
    protected $content = "";

    /**
     * @param Request $result
     * @param $bundle
     * @return mixed
     */
    abstract function onRequest(Request $result, $bundle);

    /**
     * @param $result
     * @param $bundle
     * @return mixed
     */
    abstract function onReject($result, $bundle);

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param Closure $fn
     * @internal param string $content
     */
    public function appendContent(Closure $fn)
    {
        ob_start();
        $fn->call($this);
        $this->content += ob_get_clean();
    }

    /**
     * @return void
     */
    public function cleanContent()
    {
        $this->content = "";
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request)
    {
        $length = count($this->gates);
        for($i = 0; $i < $length; $i++){
            if(!$this->gates[0]($request))
                return false;
        }
        return true;
    }

    /**
     * @param Closure $gate
     * @return void
     */
    public function pushGates($gate)
    {
         $this->gates[] = $gate;
    }

    /**
     * @return Response
     */
    protected function createResponse(){
       return  $this->response = new Response();
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function json($data){
        return $this->response = new JsonResponse($data);
    }

    /**
     * @param callable $fn
     * @return StreamedResponse
     */
    protected function stream(callable $fn){
        return $this->response = new StreamedResponse($fn);
    }

    /**
     * @param $url
     * @return RedirectResponse
     */
    protected function redirect($url){
        return $this->response = new RedirectResponse($url);
    }

    /**
     * @return Response
     */
    protected function notContent(){
        $this->response = new Response();
        $this->response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $this->response;
    }

    /**
     * @return void
     * @throws ResponseNotSetException
     */
    protected function emitResponse(){
        if(is_null($this->response) || ! $this->response instanceof Request)
            throw new ResponseNotSetException();
        else
            $this->response->send();
    }
}