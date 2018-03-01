<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\WeChatProvider\Console;


use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\OfficialAccount\Menu\Client as Menu;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WeChatMenu
 * @package Console
 */
class WeChatMenu extends Command
{
    protected function configure()
    {
        $this
            ->setName('wechat:menu')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'Operate wechat menu information. <comment>{query|create|delete}</comment>',
                'query'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        return $this->{$action}(wechat()->menu);
    }

    /**
     * @param Menu $menu
     */
    protected function query(Menu $menu)
    {
        try {
            $reorganization = [];

            $menus = $menu->list()['menu']['button'];

            $max = 0;
            foreach ($menus as $item => $value) {
                $reorganization[$item] = $value['sub_button'];
                $reorganization[$item][] = [
                    'type' => isset($value['type']) ? $value['type'] : '',
                    'name' => $value['name'],
                    'key' => isset($value['key']) ? $value['key'] : '',
                    'url' => isset($value['url']) ? $value['url'] : '',
                ];
                $max = ($len = count($reorganization[$item])) > $max ? $len : $max;
            }

            foreach ($reorganization as $key => $value) {
                if (count($value) < $max) {
                    $fill = $max - count($value);
                    for ($i = 0; $i < $fill; $i++) {
                        array_unshift($reorganization[$key], []);
                    }
                }
            }
            $tableContent = [];
            $count = count($reorganization);
            for ($i = 0; $i < $max; $i++) {
                $menu = [];
                for ($j = 0; $j < $count; $j++) {
                    $info = $reorganization[$j][$i];
                    $menu[$j] = (isset($info['name']) ? $info['name'] : '').(isset($info['type']) && !empty($info['type']) ? "[{$info['type']}]" : '');
                }
                $tableContent[] = $menu;
            }

            $table = new Table(output());
            $table->setRows($tableContent);
            $table->render();
        } catch (Exception $exception) {
            output()->writeln('<comment>✘︎ 没有菜单</comment>');
        }
    }

    /**
     * @param Menu $menu
     */
    protected function create(Menu $menu)
    {
        if ('ok' === $menu->create(config()->get('wechat.menu'))->get('errmsg')) {
            output()->writeln('<info>✔︎ 菜单添加成功</info>');

            $this->query($menu);
        }
    }

    /**
     * @param Menu $menu
     */
    protected function delete(Menu $menu)
    {
        if ('ok' === $menu->delete()->get('errmsg')) {
            output()->writeln('<info>✔︎ 菜单删除成功</info>');

            $this->query($menu);
        }
    }
}