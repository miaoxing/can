<?php

namespace MiaoxingTest\Can\Service;

class CanTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    public function testWildCardPermissions()
    {
        $this->createRoleUser('*');

        $result = wei()->can->can('admin/products');
        $this->assertTrue($result);

        $result = wei()->can->can('test');
        $this->assertTrue($result);

        $result = wei()->can->can('admin/a/b/c');
        $this->assertTrue($result);
    }

    public function testControllerPermissions()
    {
        $this->createRoleUser('admin/products');

        $result = wei()->can->can('admin/products');
        $this->assertTrue($result);

        $result = wei()->can->can('admin/products/new');
        $this->assertTrue($result);

        $result = wei()->can->can('admin/products/1/edit');
        $this->assertTrue($result);

        $result = wei()->can->can('products');
        $this->assertFalse($result);
    }

    public function testActionPermissions()
    {
        $this->createRoleUser('admin/products/edit');

        $result = wei()->can->can('admin/products');
        $this->assertFalse($result);

        $result = wei()->can->can('admin/products/edit');
        $this->assertTrue($result);

        $result = wei()->can->can('admin/products/1/edit');
        $this->assertFalse($result);
    }

    protected function createRoleUser($permissionId)
    {
        $user = wei()->user()->save();
        $ret = wei()->curUser->loginById($user['id']);
        $this->assertRetSuc($ret);

        $role = wei()->role()->setAppId()->save(['name' => '管理员']);

        wei()->userRole()->save([
            'userId' => $user['id'],
            'roleId' => $role['id'],
        ]);

        wei()->appDb('rolePermissions')->save([
            'roleId' => $role['id'],
            'permissionId' => $permissionId,
        ]);

        return $user;
    }
}
