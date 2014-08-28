<?php
ob_start();
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            html{
                background-color: #000;
                color: #fff;
            }
            pre{
                background-color: #333;
                padding: 10px;
                border: 1px solid #555;
                overflow: auto;
            }
            form{
                background-color: #161616;
                padding: 10px;
                border: 1px dashed #555;
            }
            .error {
                padding:10px;
                width: 90%;
                margin: 10px auto;
                border: 2px solid #f94949;
                color: #d6201d;
                text-align: center;
                background: #efc9c9;
                word-wrap: break-word;
            }
        </style>
    </head>
    <body>
<?php
try {
    session_start();
    set_include_path("src/");
    include_once 'keys.php';
    require_once 'Google/Client.php';
    require_once 'Google/Service/Calendar.php';

    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $client->setClientId(CLIENT_ID);
    $client->setClientSecret(CLIENT_SECRET);
    $client->setRedirectUri(REDIRECT_URI);

    $service = new Google_Service_Calendar($client);
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
        header('Location: ' . $_SERVER['SCRIPT_URI']);
    }

    if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
    }

    if ($client->getAccessToken()) {
        $calendar = $service->calendarList->listCalendarList();

        while(true) {
            foreach ($calendar->getItems() as $calendarListEntry) {
                echo $calendarListEntry->getSummary();
                echo '<br/>';
            }
            $pageToken = $calendar->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendar = $service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }
    }

    if (!isset($_GET['code'])
        && !isset($_SESSION['token'])
        && !$client->getAccessToken()
    ) {
        $client->addScope(Google_Service_Calendar::CALENDAR);
        header('Location:' . $client->createAuthUrl());
    }



} catch (Exception $e) {
    echo <<<EOT
<div class="error">
{$e->getFile()}
{$e->getLine()}
{$e->getMessage()}

{$e->getTraceAsString()}
</div>
EOT;
}
?>
    </body>
</html>
<?php
ob_end_flush();
?>