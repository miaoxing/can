<?php

namespace Miaoxing\Can\Service;

class UserPermission extends \miaoxing\plugin\BaseModel
{
    protected $table = 'userPermissions';

    protected $providers = [
        'db' => 'app.db'
    ];
}
