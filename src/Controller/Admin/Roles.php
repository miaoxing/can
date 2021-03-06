<?php

namespace Miaoxing\Can\Controller\Admin;

class Roles extends \Miaoxing\Plugin\BaseController
{
    protected $controllerName = '角色管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
    ];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $roles = wei()->role()->curApp();

                // 分页
                $roles->limit($req['rows'])->page($req['page']);

                // 排序
                $roles->desc('id');

                $data = [];
                $roles->findAll();
                foreach ($roles as $role) {
                    $data[] = $role->toArray();
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $roles->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function createAction($req)
    {
        return $this->updateAction($req);
    }

    public function editAction($req)
    {
        $role = wei()->role()->curApp()->findId($req['id']);
        $permissionIds = $role->getRolePermissions()->getAll('permissionId');

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $role = wei()->role()->curApp()->findId($req['id']);
        $role->save($req);

        // 更新拥有权限
        if ($req['permissionIds']) {
            $req['permissionIds'] = $this->filterPermissionIds($req['permissionIds']);
            $permissions = [];
            $rolePermissions = $role->getRolePermissions();
            foreach ((array) $req['permissionIds'] as $permissionId) {
                foreach (explode(',', $permissionId) as $partId) {
                    $permissions[] = ['roleId' => $role['id'], 'permissionId' => $partId];
                }
            }
            $rolePermissions->saveColl($permissions);
        }

        return $this->suc();
    }

    protected function filterPermissionIds($permissionIds)
    {
        $permissionIds = array_flip($permissionIds);
        foreach ($permissionIds as $permissionId => $key) {
            // admin/users/new,admin/users/create
            $actions = explode(',', $permissionId);
            foreach ($actions as $action) {
                // admin/users/index
                $parts = explode('/', $action);

                // 忽略根节点
                if (count($parts) === 1) {
                    continue;
                }

                // 逐级检查是否已经存在
                $path = '';
                foreach ($parts as $item) {
                    $path .= $path ? ('/' . $item) : $item;
                    if ($path !== $permissionId && isset($permissionIds[$path])) {
                        unset($permissionIds[$permissionId]);
                        continue;
                    }
                }
            }
        }

        $permissionIds = array_flip($permissionIds);

        return $permissionIds;
    }

    public function destroyAction($req)
    {
        $role = wei()->role()->curApp()->findOneById($req['id']);

        $userRole = wei()->userRole()->find(['roleId' => $role['id']]);
        if ($userRole) {
            return $this->err('该角色已被分配,不能删除');
        }

        $role->destroy();

        return $this->suc();
    }

    public function assignAction($req)
    {
        $user = wei()->user()->findOneById($req['userId']);

        if ($this->request->isGet()) {
            $roles = wei()->role()->curApp()->findAll();
            $selectedRoleIds = wei()->can->getUserRoles($user)->getAll('roleId');

            return get_defined_vars();
        } else {
            wei()->role->assign($user, (array) $req['roles']);

            return $this->suc();
        }
    }
}
