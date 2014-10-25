<?php

    class GoogleUrlShortenerComponent extends Component
    {
        private $apiKey;

        private $shortenerUrl;

        public function __construct(ComponentCollection $collection, $settings = array())
        {
            $this->_controller = $collection->getController();
            parent::__construct($collection, $settings);
            $this->shortenerUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=';

            if (!empty($settings))
            {
                if (!empty($settings['apiKey']))
                {
                    $this->apiKey = $settings['apiKey'];
                }
            }
        }

        public function initialize(Controller $controller)
        {
            $this->controller = $controller;
        }

        public function startup(Controller $controller)
        {
        }

        public function beforeRender(Controller $controller)
        {
        }

        public function generateShortUrl($longUrl = null)
        {
            if (!empty($longUrl))
            {
                App::uses('HttpSocket', 'Network/Http');
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

        public function getOriginalUrl($shortUrl = null)
        {
            if (!empty($shortUrl))
            {
                App::uses('HttpSocket', 'Network/Http');
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->apiKey,
                    array('shortUrl' => $shortUrl)
                );

                return json_decode($result, true);
            }
            return false;
        }

        public function getAnalytics($shortUrl = null)
        {
            if (!empty($shortUrl))
            {
                App::uses('HttpSocket', 'Network/Http');
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->apiKey,
                    array('shortUrl' => $shortUrl, 'projection' => 'FULL')
                );

                return json_decode($result, true);
            }
            return false;
        }

        public function shutdown(Controller $controller)
        {
        }

        public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true)
        {
        }
    }

?>