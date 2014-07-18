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
                width: 80%;
                margin: 10px auto;
                border: 2px solid #f94949;
                color: #d6201d;
                text-align: center;
                background: #efc9c9;
            }
        </style>
    </head>
    <body>
<?php
try {
    set_include_path("src/");
    include_once 'keys.php';
    require_once 'Google/Client.php';
    require_once 'Google/Service/Calendar.php';

    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $apiKey = YOUR_API_KEY;
    $client->setDeveloperKey($apiKey);

    $service = new Google_Service_Calendar($client);


    echo '<pre>';
    var_dump(get_class_methods($service));
    var_dump($service);

    echo '</pre>';


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
