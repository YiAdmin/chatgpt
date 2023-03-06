<?php

namespace app\chatgpt\logic\api;

use app\chatgpt\model\api\MessageModel;

class MessageLogic extends Logic
{
    protected $id;

    protected $messages;

    protected $newMessages = [];

    public function init($id)
    {
        $this->id = $id;
        $this->messages = MessageModel::where('chatid', $this->id)->select(['role', 'content'])->orderBy('id', 'asc')->get()->toArray();
        return $this;
    }

    public function put($message)
    {
        array_unshift($this->messages, $message);
        $this->newMessages[] = array_merge(['chatid' => $this->id, 'created_at' => time()], $message);
        return $this;
    }

    public function get()
    {
        return $this->messages;
    }

    public function save()
    {
        MessageModel::insert($this->newMessages);
        return $this;
    }

}