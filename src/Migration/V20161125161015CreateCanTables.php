<?php

namespace Miaoxing\Can\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161125161015CreateCanTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->scheme->table('permissions')
            ->string('id', 32)
            ->int('appId')
            ->string('name', 32)
            ->timestamps()
            ->int('createUser')
            ->int('updateUser')
            ->primary('id')
            ->exec();

        $this->scheme->table('roles')
            ->id()
            ->int('appId')
            ->string('name', 64)
            ->timestamps()
            ->int('createUser')
            ->int('updateUser')
            ->exec();

        $this->scheme->table('rolePermissions')
            ->id()
            ->int('roleId')
            ->string('permissionId', 64)
            ->bool('allow')->defaults(1)
            ->exec();

        $this->scheme->table('userPermissions')
            ->id()
            ->int('userId')
            ->string('permissionId', 64)
            ->timestamps()
            ->int('createUser')
            ->int('updateUser')
            ->exec();

        $this->scheme->table('userRoles')
            ->id()
            ->int('appId')
            ->int('userId')
            ->int('roleId')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->scheme->dropIfExists('permissions');
        $this->scheme->dropIfExists('roles');
        $this->scheme->dropIfExists('rolePermissions');
        $this->scheme->dropIfExists('userPermissions');
        $this->scheme->dropIfExists('userRoles');
    }
}
