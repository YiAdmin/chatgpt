<?php

namespace app\chatgpt\controller\api;

use ChatGPT\Client;
use app\chatgpt\logic\api\MessageLogic;

/**
 * @Menu(title=ChatGPT)
 */
class Index extends Base
{
    public $needLogin = ['*'];

    public $noNeedCheck = [];

    /**
     * @Menu(title=Chat)
     */
    public function chat()
    {
        $form = request()->post();
        try {
            $rule = [
                'id' => 'require',
                'message' => 'require'
            ];
            validate($rule)->check($form);
            $config = get_module_group_config('chatgpt', 'base');
            $client = new Client($config['secret']);
            $content = $form['message'];
            $id = $form['id'];
            $message = MessageLogic::instance()->init($id);
            $message->put(['role' => 'user', 'content' => $content]);
            if ($config['proxy']) $client->setOption(function($options) use ($config) {
                $options['proxy'] = $config['proxy'];
                return $options;
            });
            $result = $client->chat($message->get());
            if (isset($result['choices'])) {
                foreach ($result['choices'] as $item) {
                    $message->put([
                        'role' => $item['message']['role'], 'content' => $item['message']['content']
                    ]);
                }
                $message->save();
                return $this->success($result['choices']);
            }
            return $this->error();
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}