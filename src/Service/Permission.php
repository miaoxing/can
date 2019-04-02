<?php

namespace Miaoxing\Can\Service;

use ReflectionClass;

class Permission extends \Miaoxing\Plugin\BaseModel
{
    protected $namespaces = [
        '' => '前台',
        'admin' => '后台',
    ];

    protected $table = 'permissions';

    protected $providers = [
        'db' => 'app.db',
    ];

    /**
     * 获取所有控制器中定义的页面操作权限
     *
     * @param bool $refresh
     * @return array
     */
    public function getPagePermissionsFromCache($refresh = false)
    {
        $cacheKey = $this->getPermissionCacheKey();
        if ($refresh) {
            $this->cache->remove($cacheKey);
        }

        return $this->cache->get($cacheKey, 86400, function () {
            return $this->getPagePermissions();
        });
    }

    /**
     * 获取指定目录下的控制器中定义的权限
     *
     * @return array
     */
    protected function getPagePermissions()
    {
        // 1. 通过配置获取所有控制器
        $actions = $this->initActions();
        $config = $this->plugin->getConfig();
        $controllerMap = $config['app']['controllerMap'];

        // 附加当前应用的控制器
        $plugin = $this->plugin->getById(wei()->app->getNamespace());
        if ($plugin) {
            $controllerMap += $plugin->getControllerMap();
        }

        // 2. 从控制器类中获取权限
        foreach ($controllerMap as $name => $class) {
            // 忽略缓存未更新导致类不存在
            if (!class_exists($class)) {
                $this->logger->info('Controller class not found', ['class' => $class]);
                continue;
            }

            // 跳过未安装的插件
            $pluginId = $this->plugin->getPluginIdByClass($class);
            if (!$this->plugin->isInstalled($pluginId)) {
                continue;
            }

            // 跳过未配置权限的控制器
            $class = new ReflectionClass($class);
            $defaultProperties = $class->getDefaultProperties();
            if (!($defaultProperties['actionPermissions'])) {
                continue;
            }

            // 跳过不展示出来的权限功能
            if (isset($defaultProperties['hidePermission']) && $defaultProperties['hidePermission']) {
                continue;
            }

            // 在action前面补齐控制器,使之成为完整格式
            $classInfo = $this->getClassInfo($name);
            $controller = $classInfo['controller'];
            $permissions = $this->fillController($defaultProperties['actionPermissions'], $controller);

            // 使用控制器做键名,合并相同的控制器
            $controllers = &$actions[$classInfo['namespace']]['controllers'];
            if (!isset($controllers[$controller])) {
                $controllers[$controller] = [
                    'value' => $controller,
                    'name' => $this->getControllerName($defaultProperties),
                    'actions' => $permissions,
                ];
            } else {
                $controllers[$controller]['actions'] += $permissions;
            }
        }

        // 3. 转换为数字键名,方便JS使用
        foreach ($actions as $namespace => $configs) {
            $configs['controllers'] = array_values($configs['controllers']);
            $actions[$namespace] = $configs;
        }

        wei()->event->trigger('afterGetPagePermissions', [&$actions]);

        return $actions;
    }

    protected function getControllerName($properties)
    {
        if (isset($properties['controllerPermissionName'])) {
            return $properties['controllerPermissionName'];
        }

        if (isset($properties['controllerName'])) {
            return $properties['controllerName'];
        }

        return '未命名控制器';
    }

    /**
     * 初始化权限数组的整体结构
     *
     * @return array
     */
    protected function initActions()
    {
        $actions = [];
        foreach ($this->namespaces as $namespace => $name) {
            $actions[$namespace] = [
                'name' => $name,
                'value' => $namespace,
                'controllers' => [],
            ];
        }

        return $actions;
    }

    /**
     * 获取权限的缓存键名
     *
     * @return string
     */
    protected function getPermissionCacheKey()
    {
        $appRecord = wei()->app->getRecord();

        // 使用最后更新时间作为缓存键名,这样当安装/卸载插件后,能够获得最新的权限列表
        return 'pagePermissions:' . $appRecord['id'] . ':' . $appRecord['updateTime'];
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getClassInfo($name)
    {
        // 取出第一部分作为命名空间
        $pos = strpos($name, '\\');
        if ($pos !== false) {
            $namespace = substr($name, 0, $pos);
        } else {
            $namespace = '';
        }

        return [
            'namespace' => $namespace,
            'controller' => strtr($name, '\\', '/'),
        ];
    }

    /**
     * 在action前面补齐控制器
     *
     * 如new,create变为controller/new,controller/create
     *
     * @param array $actionPermissions
     * @param string $controller
     * @return array
     */
    protected function fillController(array $actionPermissions, $controller)
    {
        $permissions = [];
        foreach ($actionPermissions as $action => $name) {
            $actions = [];
            foreach (explode(',', $action) as $action) {
                $actions[] = $controller . '/' . $action;
            }
            $permissions[implode(',', $actions)] = $name;
        }

        return $permissions;
    }
}
