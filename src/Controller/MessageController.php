<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\WeChatProvider\Controller;


use FastD\Http\Response;
use FastD\Http\ServerRequest;

/**
 * Class MessageController
 * @package Controller
 */
class MessageController
{
    /**
     * @param ServerRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function pushMessage(ServerRequest $request)
    {
        $server = wechat()->server;
        $server->push(function ($message) {
            $forward = '\\WeChat\\'.ucfirst($message->MsgType);
            return call_user_func_array([new $forward(), 'handle'], [$message]);
        });
        return $server->serve();
    }
}