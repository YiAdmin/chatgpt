<?php

namespace ChatGPT;

use yi\Http;

class Client 
{
    const URL = [
        'chat' => 'https://api.openai.com/v1/chat/completions',
        'completions' => 'https://api.openai.com/v1/completions'
    ];

    protected $_setOption;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function chat($messages, $options = [], $headers = [])
    {
        $defaults = [
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 2048
        ];
        $options = array_merge($defaults, $options, ['messages' => $messages]);
        return $this->post(static::URL['chat'], $options, $headers);
    }

    public function completions($prompt, $options = [])
    {
        $defaults = [
            'model' => 'text-davinci-003',
            'max_tokens' => 2048
        ];
        $options = array_merge($defaults, $options, ['prompt' => $prompt]);
        return $this->post(static::URL['completions'], $options);
    }

    public function get($url, $headers = [], $type = 'json')
    {
        $options = $this->getOptions($headers);
        return Http::get($url, [], $options, $type);
    }

    public function post($url, $params = [], $headers = [], $type = 'json')
    {
        $options = $this->getOptions($headers);
        return Http::post($url, $params, $type, $options);
    }

    public function setOption($cb)
    {
        $this->_setOption = $cb;
        return $this;
    }

    public function getOptions($headers)
    {
        $headers = array_merge([
            'Authorization' => 'Bearer ' . $this->secretKey
        ], $headers);
        $options = [
            'headers' => $headers,
            'timeout' => 100
        ];
        if (is_callable($this->_setOption)) return call_user_func($this->_setOption, $options);
        return $options;
    }
}