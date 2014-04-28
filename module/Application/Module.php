<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/**
 * @module Authnet
 * @description auto load configuration for authnet
 * @package module/Application/Module.php
 * @author Yassine Nachti <yassine.nachti@unlv.edu>
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use \Application\Model\StSessionStorage as StSession;

class Module {
	
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        // set the maintenance alert if any
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'getAlert'));
        // set session timeout to 8h
        $sm = $e->getApplication()->getServiceManager();
        
        $sharedEventManager->attach(
        		'ZfcUser\Authentication\Adapter\AdapterChain',
        		'authenticate.pre',
        		function() use ($sm) {
        			$this->initSession($sm);
        		}
        );
    }
    
    /**
     * Set session timeout to x amount
     */
    private function initSession($sm) {
    	//this would be for the standard zfcuser that uses the database, 
    	//$dbService = $sm->get('ZfcUser\Authentication\Storage\Db'); //uncomment if using db
		    
    	$mySessionStorage = new StSession();
    	$mySessionManager = $mySessionStorage->getSessionManager();
    	$globals = $sm->get('globals');
   
    	// value is set in the config/autoload/global.php
    	$mySessionManager->rememberMe($globals['ttl']);
    
    	//$dbService->setStorage($mySessionStorage); //uncomment if using db
    }
    
    public function getCurrentModule(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch(); /* @var $routeMatch RouteMatch */
        $routeMatchName = $routeMatch->getMatchedRouteName();
        $namespace = explode("\\", $routeMatch->getParam("__NAMESPACE__"));
        //our currently loaded module
        $module = strtolower($namespace[0]);
        return $module;
    }

    public function getGlobals(MvcEvent $e) {
        $sm = $e->getApplication()->getServiceManager();
        $application_globals = $sm->get('globals');
        return $application_globals;
    }

    /**
     * @desc if the currently loaded module is under maintenance set the 
     * variables alert message and the current module name for displaying
     * in the layout.phtml
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function getAlert(MvcEvent $e) {
        //get the service manager
        $serviceManager = $e->getApplication()->getServiceManager();
        //retrieve the name of the currently viewed application
        $module = $this->getCurrentModule($e);
        //retrieve the global settings from the /config/autoload/global.php
        $application_globals = $this->getGlobals($e);
        //set the alert and app variables
    
        if (in_array($module, $application_globals['is_maintenance'])):
            if($module != null){
            if ($application_globals['is_maintenance'][$module] == 1):
                $alert = $serviceManager->get('maintenance_alert');
                $applicationName = $module;
            endif;
            if ($application_globals['is_maintenance']['system'] == 1):
                $alert = $serviceManager->get('maintenance_alert');
                $applicationName = 'The System';
            endif;
            }
        endif;
    
        //add variable to the view 
        $vm = $e->getViewModel();
        $vm->setVariable('isMaintenance', $application_globals['is_maintenance']['system']);
        isset($alert) ? $vm->setVariable('alert', $alert) : NULL;
        isset($applicationName) ? $vm->setVariable('app', $applicationName) : NULL;
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'maintenance_alert' => function($sm) {
                    $alert = '<div class="alert in alert-block fade alert-alert">the system is under Maintenance at this time. Please check back later!</div>';
                    return $alert;
                },
                'get_countries_list' => function ($sm) {
                    //$sm = $sm->getServiceLocator();
                    //$em = $sm->getEntityManager();
                    //$repository = $em->getRepository('Transcript\Entity\Countries');
                    //return $repository->findAll();
                },
            ),
        );
    }

}
