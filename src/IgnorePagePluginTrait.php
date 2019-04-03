<?php

namespace Miaoxing\Can;

/**
 * @property array ignorePages
 */
trait IgnorePagePluginTrait
{
    public function onAdminNavGetNavs80(&$navs, &$categories, &$subCategories)
    {
        foreach ($navs as $i => $nav) {
            if (in_array($nav['url'], $this->ignorePages)
            ) {
                unset($navs[$i]);
            }
        }
    }

    public function onAfterGetPagePermissions80(&$actions)
    {
        $ignoreControllers = [];
        foreach ($this->ignorePages as $page) {
            $ignoreControllers[] = wei()->str->camel($page);
        }

        foreach ($actions['admin']['controllers'] as $i => $controllers) {
            if (in_array($controllers['value'], $ignoreControllers)) {
                unset($actions['admin']['controllers'][$i]);
            }
        }
    }
}
