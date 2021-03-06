<?php

namespace Miaoxing\Can\Service;

use Miaoxing\Plugin\Service\User;

class Role extends \Miaoxing\Plugin\BaseModel
{
    /**
     * @var \Miaoxing\Plugin\BaseModel|\Miaoxing\Plugin\BaseModel[]
     */
    protected $rolePermissions;

    /**
     * @var \Miaoxing\Can\Service\Permission|\Miaoxing\Can\Service\Permission[]
     */
    protected $permissions;

    protected $table = 'roles';

    protected $providers = [
        'db' => 'app.db',
    ];

    /**
     * 获取角色与权限关联表数据
     *
     * @return \Miaoxing\Plugin\BaseModel|\Miaoxing\Plugin\BaseModel[]
     */
    public function getRolePermissions()
    {
        $this->rolePermissions ||
            $this->rolePermissions = $this->appDb('rolePermissions')->findAll(['roleId' => $this['id']]);

        return $this->rolePermissions;
    }

    /**
     * 检查角色是否拥有某个权限
     *
     * @param int $permissionId
     * @return bool
     */
    public function hasPermission($permissionId)
    {
        return $this->db('rolePermission')->fetch(['roleId' => $this['id'], 'permissionId' => $permissionId]);
    }

    /**
     * @param \Miaoxing\Plugin\Service\User $user
     * @param array $roleIds
     * @return array
     */
    public function assign(User $user, $roleIds)
    {
        $data = [];
        foreach ($roleIds as $role) {
            $data[] = ['userId' => $user['id'], 'roleId' => $role, 'appId' => wei()->app->getId()];
        }
        $userRoles = wei()->userRole()->curApp()->findAll(['userId' => $user['id']]);
        $userRoles->saveColl($data);

        wei()->can->refreshPermissionIds($user);

        return ['code' => 1, 'message' => '操作成功'];
    }

    public function afterSave()
    {
        parent::afterSave();
        $this->tagCache('can')->clear();
    }

    public function afterDestroy()
    {
        parent::afterDestroy();
        $this->tagCache('can')->clear();
    }
}
