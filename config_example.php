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
