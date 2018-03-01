<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

use EasyWeChat\Factory;


/**
 * @return \EasyWeChat\OfficialAccount\Application
 */
function wechat()
{
    if (!app()->has('wechat')) {
        app()->add('wechat', Factory::officialAccount(config()->get('wechat.options')));
    }

    return app()->get('wechat');
}
