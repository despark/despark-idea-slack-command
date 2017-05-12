<?php
    require_once 'config.php';

    if ($_POST['token'] !== $slackConfirmToken) {
        exit('This post request isn not sent from Slack Despark!');
    }

    $text = $_POST['text'];

    // Getting Separate Arrays For Title And Description
    preg_match_all('|(?<=\[)(.*?)(?=\])|U', $text, $textArray, PREG_SET_ORDER);

    // Check If We Got A Valid Title & Description Syntax
    if (empty($textArray)) {
        exit('Bad syntax. Please try again with a valid one. Example: [Your Title][Your Description]');
    }

    $textExploded = explode(']', $textArray[0][0]);

    // Check If We Got A Valid Title & Description Syntax. If It's Not 2 Then We Should Exit
    if (count($textExploded) != 2) {
        exit('Bad syntax. Please try again with a valid one. Example: [Your Title][Your Description]');
    }

    $ideaTitle = $textExploded[0];
    // Removing First [ If The String Has And Assign It To Variable
    $ideaDescription = ltrim($textExploded[1], '[');

    $slackUserId = $_POST['user_id'];
    $slackChannelName = $_POST['channel_name'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT `trello_id` FROM `ids` WHERE `slack_id` = '".$slackUserId."'");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();

        if (empty($data)) {
            $conn = null;
            exit('You\'re profile wasn\'t found');
        }
        $trelloUserId = $data[0]['trello_id'];
    } catch (PDOException $e) {
        exit('Connection failed: '.$e->getMessage());
    }

    $conn = null;

    $getTrelloListsUrl = 'https://api.trello.com/1/boards/'.$trelloBoardId.'/lists?key='.$trelloApiKey.'&token='.$trelloAuthToken;

    // Open Connection
    $ch = curl_init();

    // Get Trello Board Lists
    curl_setopt($ch, CURLOPT_URL, $getTrelloListsUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $trelloListsRaw = curl_exec($ch);
    $trelloLists = json_decode($trelloListsRaw, true);

    // Close Connection
    curl_close($ch);

    // Check If We Got The Despark Project Ideas Board lists
    if (is_null($trelloLists)) {
        exit('Couldn\'t find Despark Project Ideas board lists.');
    }

    $listExists = false;

    foreach ($trelloLists as $trelloList) {
        if ($slackChannelName === $trelloList['name']) {
            $listExists = true;
            $trelloListId = $trelloList['id'];
            break;
        }
    }

    if (! $listExists) {
        $trelloCreateListUrl = 'https://trello.com/1/boards/'.$trelloBoardId.'/lists';
        $fields = [
            'name' => $slackChannelName,
            'key' => $trelloApiKey,
            'token' => $trelloAuthToken,
        ];

        // open connection
        $ch = curl_init();

        // set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $trelloCreateListUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

        // exec
        $trelloCreateListRaw = curl_exec($ch);
        $trelloCreateList = json_decode($trelloCreateListRaw, true);

        // close connection
        curl_close($ch);

        // Check If We Made A List
        if (is_null($trelloCreateList)) {
            exit('Couldn\'t make a list for the Despark Project Ideas board. Please try again.');
        }

        $trelloListId = $trelloCreateList['id'];
    }

    $trelloCreateCardUrl = 'https://trello.com/1/cards/';
    $fields = [
        'name' => $ideaTitle,
        'desc' => $ideaDescription,
        'idList' => $trelloListId,
        'idLabels' => [$trelloLabelId],
        'idMembers' => [$trelloUserId],
        'key' => $trelloApiKey,
        'token' => $trelloAuthToken,
    ];

    // open connection
    $ch = curl_init();

    // set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $trelloCreateCardUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

    // exec
    $replyRaw = curl_exec($ch);
    $reply = json_decode($replyRaw, true);

    // close connection
    curl_close($ch);

    // Check If We Made A Card
    if (is_null($reply)) {
        exit('Couldn\'t create a card. Please try again.');
    }

    echo 'Thanks for sharing your idea! You can find your thought here: '.$reply['url'];
