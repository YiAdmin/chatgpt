<?php

namespace app\chatgpt\subscribe;

class Event 
{
    public function onAppInit()
    {
        add_namespace([
            'ChatGPT' => '/chatgpt/library/'
        ]);
    }
}