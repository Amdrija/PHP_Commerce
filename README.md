# Demo Projekat

This is a vanilla PHP implementation of a shop catalog website. It's built on PHP 7.4.9, MySQL 5.7.13 and uses a nginx web server.

## Installation

*Pre-requisites: Host machine has docker and docker-compose installed.*  

 1. Create a new directory (shop root directory in the text) and copy these files into it:  
    
  * docker-compose.yml
  * initDB.sql
  
2. In shop root directory create a *.env* file where you define database connection
environemnt varibles (MY_SQL_USER, MY_SQL_PASSWORD, MY_SQL_ROOT_PASSWORD).
3. Open a new terminal window in the shop root directory.  
4. Create a subdirectory called *uploads* and give all users read and write access to the directory.
5. In the terminal window type in the next command:
```docker-compose up -d```  
This command will initialize the docker containers need to run the app and initialize the database that the app uses. The containers will be running in the detached mode.    
  
After this process has finished, you'll be able to access the application.

## Note on write access errors

There is a bug where the server won't be able to create/delete product images because it doesn't have write access.
If this happens, just give every user write access of the *uploads* directory defined in step 3 of installation.

## docker-compose.yml

```
version: '3'
   
   services:
       php:
           image: andrijajlogeecom/demo_projekat:phpImage
           environment:
               MYSQL_USER: ${MYSQL_USER}
               MYSQL_PASSWORD: ${MYSQL_PASSWORD}
               MYSQL_DATABASE: ${MYSQL_DATABASE}
               HOST: mysql
           networks:
               - app-network
           depends_on:
               - mysql
           volumes:
               - ./uploads:/var/www/demo_projekat/public/img
   
       nginx:
           image: andrijajlogeecom/demo_projekat:nginxImage
           ports:
               - 80:80
           networks:
               - app-network
           depends_on:
               - php
           volumes:
               - ./uploads:/var/www/demo_projekat/public/img
   
       mysql:
           image: mysql
           environment:
               MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
               MYSQL_USER: ${MYSQL_USER}
               MYSQL_PASSWORD: ${MYSQL_PASSWORD}
               MYSQL_DATABASE: ${MYSQL_DATABASE}
           volumes:
               - ./database/data:/var/lib/mysql
               - ./initDB.sql:/docker-entrypoint-initdb.d/initDB.sql
           networks:
               - app-network
   
   networks:
       app-network:
           driver: bridge  
```  
  

## initDB.sql

```
   CREATE TABLE Statistics
   (
       Id            INT AUTO_INCREMENT NOT NULL,
       HomeViewCount INT                NOT NULL,
       PRIMARY KEY (Id)
   );
   CREATE TABLE Admin
   (
       Id       INT AUTO_INCREMENT NOT NULL,
       Username VARCHAR(64)        NOT NULL,
       Password VARCHAR(64)        NOT NULL,
       Token    VARCHAR(64),
       PRIMARY KEY (Id)
   );
   CREATE TABLE Category
   (
       Id          INT AUTO_INCREMENT NOT NULL,
       ParentId    INT,
       Code        VARCHAR(32)        NOT NULL UNIQUE,
       Title       VARCHAR(64)        NOT NULL,
       Description VARCHAR(256),
       PRIMARY KEY (Id),
       CONSTRAINT Parent_Category_Constraint
           FOREIGN KEY (ParentId)
               REFERENCES Category (Id)
               ON DELETE CASCADE
   );
   CREATE TABLE Product
   (
       Id               INT AUTO_INCREMENT NOT NULL,
       CategoryId       INT                NOT NULL,
       SKU              VARCHAR(64)        NOT NULL UNIQUE,
       Title            VARCHAR(64)        NOT NULL,
       Brand            VARCHAR(64)        NOT NULL,
       Price            INT                NOT NULL,
       ShortDescription VARCHAR(128)       NOT NULL,
       Description      VARCHAR(256)       NOT NULL,
       Image            VARCHAR(256)       NOT NULL,
       Enabled          BOOL               NOT NULL,
       Featured         BOOL               NOT NULL,
       ViewCount        INT                NOT NULL,
       PRIMARY KEY (Id),
       FOREIGN KEY (CategoryId) REFERENCES Category (Id)
   );
   INSERT INTO Statistics(HomeViewCount)
   VALUES (0);
   INSERT INTO Admin(Username, Password, Token)
   VALUES ('andrija','$2y$10$unL8TXcXCtzR4bKFM4XezeTNSoTkyGoG1SUOPQzJUtm6YrwkFTnIC','123');
```


