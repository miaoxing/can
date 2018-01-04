<?php

namespace Miaoxing\Can\Service;

class UserPermission extends \Miaoxing\Plugin\BaseModel
{
    protected $table = 'userPermissions';

    protected $providers = [
        'db' => 'app.db',
    ];
}
