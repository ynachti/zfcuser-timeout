<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
/**
 * @module Authnet
 * @description auto load configuration for authnet
 * @package config/autoload/global.php
 * @author Yassine Nachti <nachtis@gmail.com>
 */
return array(
    'service_manager' => array(
        'services' => array(
            'globals' => array(
            	'ttl' => 1800, 					//set the timeout of the login session for the user logged in	
                'is_maintenance' => array(
                    'system' => 0,
                    'application' => 0 // do not edit this one keep value at 0
                )
            )
        ),
    		
    ),
);