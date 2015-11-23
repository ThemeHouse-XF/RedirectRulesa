<?php

abstract class ThemeHouse_Listener_FrontControllerPreView
{

    /**
     * Standard approach to caching other model objects for the lifetime of the
     * model.
     *
     * @var array
     */
    protected $_modelCache = array();

    /**
     *
     * @var XenForo_FrontController
     */
    protected static $_fc = null;

    /**
     *
     * @var XenForo_ControllerResponse_Abstract
     */
    protected $_controllerResponse = null;

    /**
     *
     * @var XenForo_ViewRenderer_Abstract
     */
    protected $_viewRenderer = null;

    protected $_containerParams = array();

    /**
     *
     * @param XenForo_FrontController $fc
     * @param XenForo_ControllerResponse_Abstract $controllerResponse
     * @param XenForo_ViewRenderer_Abstract $viewRenderer
     * @param array $containerParams
     */
    public function __construct(XenForo_FrontController $fc, XenForo_ControllerResponse_Abstract &$controllerResponse, 
        XenForo_ViewRenderer_Abstract &$viewRenderer, array &$containerParams)
    {
        if (is_null(self::$_fc)) {
            self::$_fc = $fc;
        }
        
        $this->_controllerResponse = $controllerResponse;
        $this->_viewRenderer = $viewRenderer;
        $this->_containerParams = $containerParams;
    }

    /**
     * Called before attempting to prepare a view in the front controller.
     * This
     * could also be considered post-dispatch (after completing the dispatch
     * loop).
     *
     * @param XenForo_FrontController $fc The front controller instance. From
     * this, you can inspect the request, response, dependency loader, etc.
     * @param XenForo_ControllerResponse_Abstract $controllerResponse
     * @param XenForo_ViewRenderer_Abstract $viewRenderer
     * @param array $containerParams List of key-value parameters that will be
     * used to help prepare/render the necessary container template.
     */
    public static function frontControllerPreView(XenForo_FrontController $fc, 
        XenForo_ControllerResponse_Abstract &$controllerResponse, XenForo_ViewRenderer_Abstract &$viewRenderer, 
        array &$containerParams)
    {
        // This only works on PHP 5.3+, so method should be overridden for now
        $class = get_called_class();
        $frontControllerPreView = new $class($fc, $controllerResponse, $viewRenderer, $containerParams);
        list($controllerResponse, $viewRenderer, $containerParams) = $frontControllerPreView->run();
    }

    /**
     *
     * @return string
     */
    public function run()
    {
        $this->_run();
    
        $routeMatch = self::$_fc->route();
        
        $this->_viewRenderer = $this->_getViewRenderer($routeMatch->getResponseType());
        
        return array(
            $this->_controllerResponse,
            $this->_viewRenderer,
            $this->_containerParams
        );
    }

    
    /**
     * Method designed to be overridden by child classes to add run behaviours.
     */
    protected function _run()
    {
        // TODO: remove returned value as no longer required
        return array(
            $this->_controllerResponse,
            $this->_viewRenderer,
            $this->_containerParams
        );
    }

    /**
     * Gets the specified model object from the cache.
     * If it does not exist,
     * it will be instantiated.
     *
     * @param string $class Name of the class to load
     *
     * @return XenForo_Model
     */
    public function getModelFromCache($class)
    {
        if (!isset($this->_modelCache[$class])) {
            $this->_modelCache[$class] = XenForo_Model::create($class);
        }
        
        return $this->_modelCache[$class];
    }

    /**
     * Creates the view renderer for a specified response type.
     * If an invalid
     * type is specified, false is returned.
     *
     * @param $response Zend_Controller_Response_Http Response object
     * @param $response Type string Type of response
     * @param $request Zend_Controller_Request_Http Request object
     *
     * @return XenForo_ViewRenderer_Abstract false
     */
    public function getViewRenderer(Zend_Controller_Response_Http $response, $responseType, 
        Zend_Controller_Request_Http $request, XenForo_Dependencies_Abstract $dependencies)
    {
        return false;
    }

    /**
     * Gets the view renderer for the specified response type.
     *
     * @param string Response type (eg, html, xml, json)
     *
     * @return XenForo_ViewRenderer_Abstract
     */
    protected function _getViewRenderer($responseType)
    {
        $viewRenderer = $this->getViewRenderer(self::$_fc->getResponse(), $responseType, self::$_fc->getRequest(), 
            self::$_fc->getDependencies());
        
        if ($viewRenderer) {
            return $viewRenderer;
        }
        
        return $this->_viewRenderer;
    }

    /**
     * Factory method to get the named front controller pre-view listener.
     * The class must exist or be autoloadable or an exception will be thrown.
     *
     * @param string $className Class to load
     * @param XenForo_FrontController $fc The front controller instance. From
     * this, you can inspect the request, response, dependency loader, etc.
     * @param XenForo_ControllerResponse_Abstract $controllerResponse
     * @param XenForo_ViewRenderer_Abstract $viewRenderer
     * @param array $containerParams List of key-value parameters that will be
     * used to help prepare/render the necessary container template.
     *
     * @return ThemeHouse_Listener_FrontControllerPreView
     */
    public static function create($className, XenForo_FrontController $fc, 
        XenForo_ControllerResponse_Abstract &$controllerResponse, XenForo_ViewRenderer_Abstract &$viewRenderer, 
        array &$containerParams)
    {
        $createClass = XenForo_Application::resolveDynamicClass($className, 'listener_th');
        if (!$createClass) {
            throw new XenForo_Exception("Invalid listener '$className' specified");
        }
        
        return new $createClass($fc, $controllerResponse, $viewRenderer, $containerParams);
    }

    /**
     *
     * @param string $className Class to load
     * @param XenForo_FrontController $fc The front controller instance. From
     * this, you can inspect the request, response, dependency loader, etc.
     * @param XenForo_ControllerResponse_Abstract $controllerResponse
     * @param XenForo_ViewRenderer_Abstract $viewRenderer
     * @param array $containerParams List of key-value parameters that will be
     * used to help prepare/render the necessary container template.
     *
     * @return array
     */
    public static function createAndRun($className, XenForo_FrontController $fc, 
        XenForo_ControllerResponse_Abstract &$controllerResponse, XenForo_ViewRenderer_Abstract &$viewRenderer, 
        array &$containerParams)
    {
        $createClass = self::create($className, $fc, $controllerResponse, $viewRenderer, $containerParams);
        
        if (XenForo_Application::debugMode()) {
            return $createClass->run();
        }
        try {
            return $createClass->run();
        } catch (Exception $e) {
            return array(
                $this->_controllerResponse,
                $this->_viewRenderer,
                $this->_containerParams
            );
        }
    }
}