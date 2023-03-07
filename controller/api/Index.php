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
     * @Menu(title=对话)
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
            $content = $form['message'];
            $id = $form['id'];
            $message = MessageLogic::instance()->init($id);
            $message->put(['role' => 'user', 'content' => $content]);
            $client = $this->client();
            $config = get_module_group_config('chatgpt', 'base');
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

    /**
     * @Menu(title=编辑文本)
     */
    public function textedit()
    {
        try {
            $form = request()->post();
            $rule = [
                'instruction' => 'require',
                'model' => 'require|in:text-davinci-edit-001,code-davinci-edit-001',
                'n' => 'require|integer|between:1,10'
            ];
            validate($rule)->check($form);
            $client = $this->client();
            $result = $client->textedit($form);
            return $this->success($result['choices']);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @Menu(title=生成图片)
     */
    public function image()
    {
        try {
            $form = request()->post();
            $rule = [
                'prompt' => 'require',
                'size' => 'require|in:256x256,512x512,1024x1024',
                'n' => 'require|integer|between:1,10'
            ];
            validate($rule)->check($form);
            $client = $this->client();
            $result = $client->image($form);
            return $this->success($result['data']);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    protected function client()
    {
        $config = get_module_group_config('chatgpt', 'base');
        return new Client($config['secret']);
    }
}