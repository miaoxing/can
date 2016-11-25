<?php

namespace Miaoxing\Can\Controller\Admin;

class Permissions extends \miaoxing\plugin\BaseController
{
    /**
     * @todo
     */
    protected $guestPages = [
        'admin/permissions/all'
    ];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json' :

                $permissions = wei()->permission();

                // 分页
                $permissions->limit($req['rows'])->page($req['page']);

                // 排序
                $permissions->desc('createTime');

                $data = [];
                $permissions->findAll();
                foreach ($permissions as $permission) {
                    $data[] = $permission->toArray();
                }

                return $this->suc([
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $permissions->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function allAction($req)
    {
        $customs = wei()->permission()->findAll();
        $actions = wei()->permission->getPagePermissionsFromCache($req['refresh']);

        return $this->suc([
            'pages' => $actions,
            'customs' => $customs->toArray(['id', 'name'])
        ]);
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
        $permission = wei()->permission()->findOrInitById($req['id']);
        return get_defined_vars();
    }

    public function updateAction($req)
    {
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'id' => [],
                'name' => []
            ],
            'names' => [
                'id' => '编号',
                'name' => '名称'
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        $permission = wei()->permission()->findId($req['id']);
        $permission->save($req);

        return $this->suc();
    }

    public function destroyAction($req)
    {
        $permission = wei()->permission()->findId($req['id']);
        $permission->destroy();
        return $this->suc();
    }
}
