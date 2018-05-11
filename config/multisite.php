<?php

/**
 * Multi Site Config
 */


/*
  Multisite DB config must include a deployments table something like:

  CREATE TABLE `tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `subdomain` varchar(255) DEFAULT NULL,
    `domain` varchar(255) NOT NULL,
    `dbhost` varchar(50) NOT NULL,
    `dbname` varchar(50) NOT NULL,
    `dbuser` varchar(50) NOT NULL,
    `dbpassword` varchar(50) NOT NULL
    PRIMARY KEY (`id`),
    UNIQUE KEY `domains` (`subdomain`, 'domain'),
    KEY `domain` (`domain`),
    KEY `subdomain` (`subdomain`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

/**
 * The following options are available:
 *
 * - boolean  enabled     enable switching site based on subdomain           FALSE
 * - string   domain      parent domain for site ie. ushahidi.io
 * - string   email       from email for password resets, etc
 */
return [
    'enabled' => !empty(env("MULTISITE_DOMAIN")),
    'domain'  => env("MULTISITE_DOMAIN"),
    'email'   => env("MULTISITE_EMAIL"),
];
