<?php

namespace Miaoxing\Can;

use Miaoxing\Plugin\Service\User;
use Wei\RetTrait;

class Plugin extends \miaoxing\plugin\BasePlugin
{
    use RetTrait;

    protected $name = '权限校验服务,支持RBAC权限';

    protected $version = '1.0.0';

    protected $adminNavId = 'user';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $navs[] = [
            'parentId' => 'user-admin',
            'url' => 'admin/roles',
            'name' => '角色管理',
            'sort' => 1000,
        ];
    }

    /**
     * 待导航数据生成后,过滤没有权限访问的地址
     *
     * @param array $navs
     * @param array $categories
     * @param array $subCategories
     */
    public function onAdminNavGetNavs50(&$navs, &$categories, &$subCategories)
    {
        $can = wei()->can;
        foreach ($navs as $i => $nav) {
            if (!$can->accessUrl($nav['url'])) {
                unset($navs[$i]);
            }
        }
    }

    /**
     * 检查用户是否有权限访问后台页面
     *
     * @param string $page
     * @param \Miaoxing\Plugin\Service\User $user
     * @return \Wei\Response
     */
    public function onAdminAuth($page, User $user)
    {
        if (!wei()->can->can($page, $user)) {
            // 上报一段时间供数据观察
            $this->logger->warning('用户无权限访问页面');

            return $this->err('很抱歉,您没有权限访问该页面', -403);
        }
    }
}
