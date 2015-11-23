<?php

class ThemeHouse_RedirectRules_Listener_TemplatePostRender extends ThemeHouse_Listener_TemplatePostRender
{

    public static $checkRedirectRules = false;

    public function run()
    {
        if (self::$checkRedirectRules == $this->_templateName) {
            $redirectRules = XenForo_Application::getSimpleCacheData('th_redirectRules');

            $user = XenForo_Visitor::getInstance()->toArray();
            $template = $this->_template;
            $containerData = $this->_containerData;

            if ($redirectRules) {
                foreach ($redirectRules as $redirectRule) {
                    if (XenForo_Helper_Criteria::userMatchesCriteria($redirectRule['user_criteria'], true, $user) && XenForo_Helper_Criteria::pageMatchesCriteria(
                        $redirectRule['page_criteria'], true, $template->getParams(), $containerData)) {
                        $url = $this->_getRedirectUrl($redirectRule['reroute_domain']);
                        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) !== FALSE) {
                            header("HTTP/1.1 301 Moved Permanently");
                            header('Location : ' . $url);
                            exit();
                        }
                    }
                }
            }
        }

        return parent::run();
    } /* END run */

    protected function _getRedirectUrl($domain)
    {
        $redirectUrl = 'http';
        if ($_SERVER["SERVER_PORT"] === 443) {
            $redirectUrl .= "s";
        }
        $redirectUrl .= "://";
        if (!in_array($_SERVER["SERVER_PORT"], array(
            80,
            443
        ))) {
            $redirectUrl .= $domain . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $redirectUrl .= $domain . $_SERVER["REQUEST_URI"];
        }
        return $redirectUrl;
    } /* END _getRedirectUrl */

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new ThemeHouse_RedirectRules_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */
}