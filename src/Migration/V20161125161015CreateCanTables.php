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
        $this->schema->table('permissions')
            ->string('id', 32)
            ->int('appId')
            ->string('name', 32)
            ->timestampsV1()
            ->int('createUser')
            ->int('updateUser')
            ->primary('id')
            ->exec();

        $this->schema->table('roles')
            ->id()
            ->int('appId')
            ->string('name', 64)
            ->timestampsV1()
            ->int('createUser')
            ->int('updateUser')
            ->exec();

        $this->schema->table('rolePermissions')
            ->id()
            ->int('roleId')
            ->string('permissionId', 64)
            ->bool('allow')->defaults(1)
            ->exec();

        $this->schema->table('userPermissions')
            ->id()
            ->int('userId')
            ->string('permissionId', 64)
            ->timestampsV1()
            ->int('createUser')
            ->int('updateUser')
            ->exec();

        $this->schema->table('userRoles')
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
        $this->schema->dropIfExists('permissions');
        $this->schema->dropIfExists('roles');
        $this->schema->dropIfExists('rolePermissions');
        $this->schema->dropIfExists('userPermissions');
        $this->schema->dropIfExists('userRoles');
    }
}
