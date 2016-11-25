<?php

namespace Miaoxing\Can\Service;

class UserRole extends \miaoxing\plugin\BaseModel
{
    protected $table = 'userRoles';

    protected $guarded = [
        'createTime',
        'createUser',
        'updateTime',
        'updateUser',
    ];

    protected $providers = [
        'db' => 'app.db',
    ];
}
