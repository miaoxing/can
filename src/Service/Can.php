<?php

namespace Miaoxing\Can\Service;

use Miaoxing\Plugin\Service\Str;
use Miaoxing\Plugin\Service\User;

/**
 * @property Str str
 */
class Can extends \Miaoxing\Plugin\BaseService
{
    /**
     * @var Role[]|Role[][]
     */
    protected $roles = [];

    /**
     * @var UserRole|UserRole
     */
    protected $userRoles;

    /**
     * 用户权限的缓存信息
     *
     * @var string
     */
    protected $permissionCacheKey = 'permissionIdsV10:';

    /**
     * 判断用户是否能执行某项权限(Permission)
     *
     * @param string $permission 权限的名称
     * @return bool
     */
    public function __invoke($permission)
    {
        return $this->can($permission);
    }

    /**
     * 检查用户是否有指定的权限
     *
     * @param string $permissionId
     * @param \Miaoxing\Plugin\Service\User $user
     * @return bool
     */
    public function can($permissionId, User $user = null)
    {
        if (!$this->plugin->isInstalled('can')) {
            $this->logger->info('Plugin "can" has not installed');

            return true;
        }

        $user || $user = wei()->curUser;
        $permissionIds = $this->getPermissionIds($user);

        // 判断是否为超级管理员
        if ($user->isSuperAdmin()) {
            return true;
        }

        // 判断是否拥有全部权限
        if (isset($permissionIds['*'])) {
            return true;
        }

        // 判断是否拥有指定权限,如 admin/articles
        if (isset($permissionIds[$permissionId])) {
            return true;
        }

        // 从大到小逐级检查
        $path = '';
        $parts = explode('/', $permissionId);
        array_pop($parts);
        foreach ($parts as $part) {
            $path .= $path ? ('/' . $part) : $part;
            if (isset($permissionIds[$path])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查用户能否访问指定的URL地址
     *
     * @param string $url
     * @param \Miaoxing\Plugin\Service\User $user
     * @return bool
     */
    public function accessUrl($url, User $user = null)
    {
        $page = $this->convertUrlToPage($url);
        if (!$page) {
            return true;
        }

        return $this->can($page, $user);
    }

    /**
     * 获取当前用户拥有的权限列表
     *
     * @param \Miaoxing\Plugin\Service\User $user
     * @return array
     */
    public function getPermissionIds(User $user)
    {
        $key = $this->app->getNamespace() . ':' . $this->permissionCacheKey . $user['id'];
        return wei()->tagCache('can')->get($key, 864000, function () use ($user) {
            $permissionIds = [];

            // 获取用户关联角色的权限
            foreach ($this->getRoles($user) as $role) {
                $rolePermissionIds = $role->getRolePermissions()->getAll('permissionId');
                $permissionIds += array_flip($rolePermissionIds);
            }

            return $permissionIds;
        });
    }

    /**
     * @param User $user
     * @return bool
     */
    public function refreshPermissionIds(User $user)
    {
        wei()->tagCache('can')->remove($this->permissionCacheKey . $user['id']);
        $this->getPermissionIds($user);

        return $this;
    }

    /**
     * 将URL地址转换为页面权限
     *
     * @param string $url
     * @return false|string
     */
    public function convertUrlToPage($url)
    {
        $comps = parse_url($url);

        // 对外部链接不做处理
        if (isset($comps['host'])) {
            return false;
        }

        // 忽略查询参数
        $path = $comps['path'];

        // 补齐默认action
        $slashCount = substr_count($path, '/');
        if ($slashCount == 0 || ($slashCount == 1 && substr($path, 0, 6) == 'admin/')) {
            $path .= '/index';
        }

        // 转换为驼峰形式
        return $this->str->camel($path);
    }

    /**
     * 检查用户是否存在某角色
     *
     * @param string $roleId
     * @param \Miaoxing\Plugin\Service\User $user
     * @return bool
     */
    public function hasRole($roleId, \Miaoxing\Plugin\Service\User $user)
    {
        return (bool) wei()->userRole()->curApp()->find(['userId' => $user['id'], 'roleId' => $roleId]);
    }

    /**
     * 获取用户与角色关联表数据
     *
     * @param \Miaoxing\Plugin\Service\User $user
     * @return UserRole|UserRole[]
     */
    public function getUserRoles(User $user)
    {
        $this->userRoles || $this->userRoles = wei()->userRole()->curApp()->findAll(['userId' => $user['id']]);

        return $this->userRoles;
    }

    /**
     * @param User $user
     * @return Role|Role[]
     */
    public function getRoles(User $user)
    {
        if (!isset($this->roles[$user['id']])) {
            $this->roles[$user['id']] = wei()->role()
                ->select('roles.*')
                ->join('userRoles', 'userRoles.roleId = roles.id')
                ->andWhere([
                    'roles.appId' => $this->app->getId(),
                    'userId' => $user['id'],
                ])
                ->findAll();
        }

        return $this->roles[$user['id']];
    }
}
