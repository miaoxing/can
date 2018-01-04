<?php

namespace Miaoxing\Can\Service;

class UserRole extends \Miaoxing\Plugin\BaseModel
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
