# Despark's Idea Slack Command

**Despark's Idea Slack Command** is a slash command for <a href="https://slack.com/" target="_blank">Slack</a>, with which you can easily add your thoughts to your <a href="https://trello.com/" target="_blank">Trello</a> Organization Board.

## Usage
```
    /commandName [Title][Description(Leave empty if you don't want any)]
    
```
The idea will be automatically added to the Trello board you provided in the config.php, under a list with the name of the Slack channel, in which you initiated the command.

## Requirements

 - PHP >= 5.6
 - MySQL (required only for full functionallity)
 - Trello and Slack Accounts

## Installation

1. Clone/download this repo:

  ```
    git clone git@github.com:despark/despark-idea-slack-command.git
    
  ```

2. Create a config.php file with the data from config_example.php and update appropriately to match your setup.

```php
   <?php
    $config = [
        // Set True If You Want To Subscribe The User To The Created Idea
        'subscribeUser' => false,
        // Database
        'servername' => '127.0.0.1',
        'username' => 'username',
        'password' => 'password',
        'db' => 'database',
        // Trello
        // Admin Auth Token For Using The API
        'trelloAuthToken' => 'trelloAuthToken',
        // Admin API Key For Using The API
        'trelloApiKey' => 'trelloApiKey',
        // Trello Board ID Where The Ideas Will Be Created
        'trelloBoardId' => 'trelloBoardId',
        // ID Of The Label That Should Be Applied On The Idea. Set NULL If You Don't Want To Apply Any
        'trelloLabelId' => null,
        // Slack
        // Confirm Token Given From The Slash Command App
        'slackConfirmToken' => 'slackConfirmToken',
    ];
  ```

3. Add the slash command to your Slack Team:

  - Go to: https://{your-team-name}.slack.com/apps and find the Slash Commands menu.</li>
  - Click the Add Configuration button and register the new command's name.</li>
  - Fill up the blanks
  
      <strong>URL:</strong> The URL address to your index.php file you've just clonned/downloaded.

      <strong>Method:</strong> POST

      <strong>Customize Name:</strong> Your desired name.

      <strong>Descriptive Label:</strong> Some description about the slash command.
  - Paste the <strong>Token</strong> string in the config file.
  
4. Get your <a href="https://trello.com/" target="_blank"> Trello</a> API key and Auth Token:

  - Login with your account <a href="https://trello.com/app-key" target="_blank">here</a> and paste the generated API key in the  config file.
  - Get your Auth Token from <a href="https://trello.com/1/authorize?expiration=never&scope=read,write,account&response_type=token&name=Server%20Token&key=5d222c8fdc009236f0c95e0d03b57785" target="_blank">here</a> and paste it in the config file.

5. Create a new <a href="https://trello.com/" target="_blank">Trello</a> Board where your ideas will be stored and add your users to it.

6. <strong>(Optional)</strong> Create a new label for your Idea Cards.

7. Get the Board and Label IDs:
  - Copy the board URL and paste it in a new window with .json at the end.
  - The first element is the Trello Board ID. Paste it in the config file.
  - <strong>(Optional)</strong> Search for the labels array. Find and paste it in the config file, the ID, corresponding to the name of the label you've just created.
  
8. <strong>Only if you want to assign people to the cards: </strong>
  - Change the subscribeUser to true in the config file.
  ```php
   ...
  // Set True If You Want To Subscribe The User To The Created Idea
        'subscribeUser' => true,
   ...
  ```
  - Update the config file with your database settings.
  ```php
   ...
  // Database
        'servername' => '127.0.0.1',
        'username' => 'username',
        'password' => 'password',
        'db' => 'database',
   ...
  ```
  - Run the database_example.sql (Change the database name to your desired one).
  ```mysql
CREATE DATABASE `database`;

USE `database`;

DROP TABLE IF EXISTS `ids`;

CREATE TABLE `ids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slack_id` varchar(255) NOT NULL DEFAULT '',
  `trello_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  ```
  - From 7. search for idOrganization and copy it.
  - Make a GET Request to https://trello.com/1/organizations/idOrganization/members?key=trelloApiKey&token=trelloAuthToken
  - Generate a Slack token from <a href="https://api.slack.com/custom-integrations/legacy-tokens" target="_blank">here</a>.
  - Make a GET Request to https://slack.com/api/users.list?token=slackToken
  - Insert in the database each team member's name and id in Trello and Slack
  

## Copyright and License

Despark's Idea Slack Command was written by Despark and is released under the MIT License. See the LICENSE file for details.
