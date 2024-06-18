Project Set up instructions

# Project Setup

## Introduction:

The Shortened URL Manager is built using:
Laminas Framework, MySQL/Doctrine, Tailwaingd/Daisy UI Components.
The application addresses the following use cases:
-It allows you to Create, Review, Update, Delete,Restore Shortened URLs.
-One can also visit the target link via this application. Please note the app just helps you to manage the URLs and do not generate the short URLs.
-It allows the admin to view the list of Users currently registered with the system.

## Prerequisites
1. Ensure you have PHP and Composer installed on your system.
2. Install MySQL (or the database system you are using).


## Steps to Follow:

## Cloning the Repository
1. Start by cloning the repository from GitHub. 

Open your terminal (or command prompt) and execute the following command:
   
   https://github.com/sanjivsharmalv/Shortened-URLs-Manager.git
   
## Go to Project Directory
$> cd ~/<project_dir>

## Install Backend dependencies via Composer

$>composer install

Abovd command reads the composer.json file and installs all required PHP libraries and packages into the vendor directory.

#Install frontend dependencies

Next, install frontend dependencies using npm (Node Package Manager):
$> npm install

This command reads the package.json file and installs all necessary packages and dependencies for the frontend assets.

## Setting Up the Database

    You can choose either the following options to configure the database with the app. 
    1: Importing SQL Dump
 
    If you prefer to import the database schema and initial data via an SQL dump file, you can use the provided SQL file(<project dir>/data/globaltickets.sql). 
    
    You can import the above using tools such as phpmyadmin, workbench or any other such data base tool.
    or you can execute the following from the terminal:

    > mysql -u your_username -p your_database_name < <project dir>/data/globaltickets.sql

    Please note that globaltickets.sql contains the sample data for the assessor for ready reference. Or you may choose to enter data of you own.

    2. Or You may want to create the database and table of your own liking and/or update the following conigurations in Laminas.

    Refer to the file: <project_dir>/config/autoload/global.php

    'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host' => '127.0.0.1',
                    'port' => '8889',
                    'user' => 'root',
                    'password' => 'root',
                    'dbname' => 'globaltickets',
                    'encoding' => 'utf8',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
                ]
            ]
        ],

        Update the above params as applicable in your setup, such as:
        host, port,user,password,dbname,
        After updating this configuration file with your database details, Doctrine will use these settings to establish a connection to your database server when your application runs.
        
        

## Running the Project

Once dependencies are installed and the database connection is configured, you can proceed to run your PHP project. This typically involves starting your web server (if it's not already running) and accessing your application through a web browser.
You can either configure the application with apache, as follows:
a) Either you can visit any of the resources in the internet that give detailed steps of configuring your website with Apache/ Nginx or any other web server.
b)For simplicity, you may want to run the built in php webserver for testing purpose, such as :

$<projet dir>/php -S localhost:2222 -t public

## Known Issues
No glaring broken links are known. 

I hope you will enjoy testing the app as much as I enjoyed building the same.

Thanks for reading (and executing ) it this far.
