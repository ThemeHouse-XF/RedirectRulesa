<?php

class ThemeHouse_RedirectRules_Listener_FrontControllerPreView extends ThemeHouse_Listener_FrontControllerPreView
{

    public function run()
    {
        $this->_getRedirectRulesTemplateName();

        return parent::run();
    } /* END run */

    public static function frontControllerPreView(XenForo_FrontController $fc,
        XenForo_ControllerResponse_Abstract &$controllerResponse, XenForo_ViewRenderer_Abstract &$viewRenderer,
        array &$containerParams)
    {
        $frontControllerPreView = new ThemeHouse_RedirectRules_Listener_FrontControllerPreView($fc, $controllerResponse,
            $viewRenderer, $containerParams);
        list($controllerResponse, $viewRenderer, $containerParams) = $frontControllerPreView->run();
    } /* END frontControllerPreView */

    protected function _getRedirectRulesTemplateName()
    {
        $xenOptions = XenForo_Application::get('options');

        if ($this->_viewRenderer instanceof XenForo_ViewRenderer_HtmlPublic || $this->_viewRenderer instanceof XenForo_ViewRenderer_HtmlAdmin) {
            if ($this->_viewRenderer instanceof XenForo_ViewRenderer_HtmlAdmin) {
                if (!$xenOptions->th_redirectRules_allowAdmin) {
                    return false;
                }
            }

            if ($this->_controllerResponse instanceof XenForo_ControllerResponse_View) {
                ThemeHouse_RedirectRules_Listener_TemplatePostRender::$checkRedirectRules = $this->_controllerResponse->templateName;

                return true;
            }
        }
    } /* END _getRedirectRulesTemplateName */
}