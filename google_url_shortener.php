<?php

    class GoogleUrlShortenerComponent extends Object
    {
        var $name = 'GoogleUrlShortener';

        public $apiKey;

        public $shortenerUrl;

        function initialize(&$controller, $settings = array())
        {
            $this->controller =& $controller;
            $this->shortenerUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=';

            if (!empty($settings))
            {
                if (!empty($settings['apiKey']))
                {
                    $this->apiKey = $settings['apiKey'];
                }
            }
        }

        function startup(&$controller)
        {
        }

        function beforeRender(&$controller)
        {
        }

        function generateShortUrl($longUrl = null)
        {
            if (!empty($longUrl))
            {
                App::import('Core', 'HttpSocket');
                $socket = new HttpSocket();

                $result = $socket->post(
                    $this->shortenerUrl . $this->apiKey,
                    json_encode(array('longUrl' => $longUrl)),
                    array('header' => array('Content-Type' => 'application/json'))
                );

                return json_decode($result, true);
            }
            return false;
        }

        function getOriginalUrl($shortUrl = null)
        {
            if (!empty($shortUrl))
            {
                App::import('Core', 'HttpSocket');
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->apiKey,
                    array('shortUrl' => $shortUrl)
                );

                return json_decode($result, true);
            }
            return false;
        }

        function getAnalytics($shortUrl = null)
        {
            if (!empty($shortUrl))
            {
                App::import('Core', 'HttpSocket');
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->apiKey,
                    array('shortUrl' => $shortUrl, 'projection' => 'FULL')
                );

                return json_decode($result, true);
            }
            return false;
        }

        function shutdown(&$controller)
        {
        }

        function beforeRedirect(&$controller, $url, $status = null, $exit = true)
        {
        }
    }

?>