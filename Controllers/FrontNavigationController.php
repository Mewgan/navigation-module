<?php

namespace Jet\Modules\Navigation\Controllers;

use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Content;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;

class FrontNavigationController extends MainController
{

    /**
     * @param Website $website
     * @param Content $content
     * @return null
     */
    public function show(Website $website, $content)
    {
        $data = $content->getData();
        if (!empty($data) && isset($data['navigation']) && is_numeric($data['navigation'])) {
            if (empty($this->websites)) {
                $this->websites[] = $website;
                $this->getThemeWebsites($website);
            }
            $navigation = Navigation::repo()->renderFront($data['navigation'], ['websites' => $this->websites, 'options' => $this->getWebsiteData($website)]);
            return (is_null($navigation))
                ? null
                : $this->_renderContent($content->getTemplate(), 'src/Modules/Navigation/Views/', compact('navigation'));
        }
        return null;
    }

    /**
     * @param Website $website
     * @param $value
     * @return null
     */
    public function renderField(Website $website, $value)
    {
        $navigation_types = $this->app->data['app']['settings']['navigation'];
        $values = explode('@', $value);
        $url = null;
        if (isset($values[1]) && isset($navigation_types[$values[0]])) {
            $callback = explode('@', $navigation_types[$values[0]]['get_url']);
            if ($values[0] == 'page') {
                $route = $this->callMethod($callback[0], $callback[1], ['id' => $values[1]]);
                if (!is_array($route)) $url = $route->getUrl();
            } else {
                /** @var Route $route */
                $route = Route::findOneByName($navigation_types[$values[0]]['route']);
                if(!is_null($route)) {
                    $_url = $this->callMethod($callback[0], $callback[1], ['url' => $route->getUrl(), 'id' => $values[1]]);
                    if (!is_array($_url)) $url = $_url;
                }
            }
            return $this->callMethod('Jet\FrontBlock\Controllers\FrontPhpController', 'fullUrl', ['url' => $url, 'website' => $website]);
        }
        return $url;
    }

}