<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\WeChatProvider;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\WeChatProvider\Controller\GatewayController;
use FastD\WeChatProvider\Controller\MessageController;

class WeChatProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed
     */
    public function register(Container $container)
    {
        $wechat = [
            'wechat' => [
                'options' => load(app()->getPath().'/config/wechat.php'),
                'menu' => load(app()->getPath().'/config/menu.php'),
                'subscribe' => '欢迎关注',
            ],
        ];

        config()->merge($wechat);

        route()->get('/', [new GatewayController(), 'valid']);
        route()->post('/', [new MessageController(), 'pushMessage']);
    }
}