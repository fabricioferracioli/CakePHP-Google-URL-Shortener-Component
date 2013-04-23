<?php

    App::uses('ModelBehavior', 'Model');
    App::uses('Inflector', 'Cake/Utility');
    App::uses('HttpSocket', 'Network/Http');

    class GoogleUrlShortenerBehavior extends ModelBehavior
    {
        public function setup(Model $Model, $settings = array())
        {
            $this->shortenerUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=';
            if (!isset($this->settings[$Model->alias]))
            {
                $this->settings[$Model->alias] = array(
                    'controller' => Inflector::pluralize($Model->alias),
                    'action' => 'view',
                    'slug' => false,
                    'onUpdate' => false,
                    'field' => 'short_url',
                    'apiKey' => null
                );
            }
            $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array)$settings);
        }

        public function afterSave(Model $Model, $created)
        {
            if ($created || $this->settings[$Model->alias]['onUpdate'])
            {
                $Model->recursive = -1;
                $row = $Model->read(null, $Model->id);

                if ($this->settings[$Model->alias]['slug'])
                {
                    $url = Router::url(array('controller' => $this->settings[$Model->alias]['controller'], 'action' => $this->settings[$Model->alias]['action'], $row[$Model->alias][$this->settings[$Model->alias]['alias']]), true);
                }
                else
                {
                    $url = Router::url(array('controller' => $this->settings[$Model->alias]['controller'], 'action' => $this->settings[$Model->alias]['action'], $Model->id), true);
                }

                $short = $this->generateShortUrl($Model, $url);
                $Model->set($this->settings[$Model->alias]['alias'], $short['id']);
            }
        }

        public function generateShortUrl(Model $Model, $longUrl = null)
        {
            if (!empty($longUrl))
            {
                $socket = new HttpSocket();

                $result = $socket->post(
                    $this->shortenerUrl . $this->settings[$Model->alias]['apiKey'],
                    json_encode(array('longUrl' => $longUrl)),
                    array('header' => array('Content-Type' => 'application/json'))
                );

                return json_decode($result, true);
            }
            return false;
        }

        public function getOriginalUrl(Model $Model, $shortUrl = null)
        {
            if (!empty($shortUrl))
            {
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->settings[$Model->alias]['apiKey'],
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
                $socket = new HttpSocket();

                $result = $socket->get(
                    $this->shortenerUrl . $this->settings[$Model->alias]['apiKey'],
                    array('shortUrl' => $shortUrl, 'projection' => 'FULL')
                );

                return json_decode($result, true);
            }
            return false;
        }
    }

?>