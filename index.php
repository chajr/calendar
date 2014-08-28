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
    require_once 'vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName("Client_Library_Examples");
    $client->setClientId(CLIENT_ID);
    $client->setClientSecret(CLIENT_SECRET);
    $client->setRedirectUri(REDIRECT_URI);

    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
        header('Location: ' . $_SERVER['SCRIPT_URI']);
    }

    if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
    }

    if ($client->getAccessToken()) {
        $service    = new Google_Service_Drive($client);
        $file       = new Google_Service_Drive_DriveFile();
        $directory  = new Google_Service_Drive_DriveFile();
        $newParent  = new Google_Service_Drive_ParentReference();


//        $dirName = 'directory-' . time();
//        $directory->setTitle($dirName);
//        $directory->setMimeType('application/vnd.google-apps.folder');
//        $newDir = $service->files->insert($directory);
//
//        $fileTitle = 'testowy-plik-' . time();
//        $file->setFileExtension('txt');
//        $file->setTitle($fileTitle);
//        $newParent->setId($newDir->getId());
//
//        $newFile = $service->files->insert($file, [
//            'data'          => 'lorem ipsum donor',
//            'mimeType'      => 'text/plain',
//            'uploadType'    => 'media',
//        ]);

//        $service->parents->insert($newFile->getId(), $newParent);

        $structure      = [];
        $completeList   = $service->files->listFiles()->getItems();
        
        
        
        /** @var Google_Service_Drive_DriveFile $item */
        foreach ($completeList as $item) {
//            if (preg_match('#^testowy-plik-[\d]+(_[\w]+)?#', $item->getTitle())
//            || preg_match('#^directory-[\d]+#', $item->getTitle())
//            ) {
            
            $parents = $item->getParents();
            
            /** @var Google_Service_Drive_ParentReference $parent */
            foreach ($parents as $parent) {
                if ($parent->getIsRoot()) {
                    $structure[$parent->getId()][] = [
                        'main'      => $item->getTitle(),
                        'id'        => $item->getId(),
                        'mime'      => $item->getMimeType(),
                        'childs'    => check($item->getId(), $completeList)
                    ];
                } else {
//                    echo '<pre>';
//                    var_dump($parent->getKind());
//                    echo '</pre>';
                }
            }
            

            
//                echo '<pre>';
//                var_dump($item->getId(), $item->getTitle(), $parents);
//                echo '</pre>';
//            }
        }
        echo '<pre>';
        var_dump($structure);
        echo '</pre>';

//        $copiedFile = new Google_Service_Drive_DriveFile();
//        $copiedFile->setTitle($fileTitle . '_copy');
//
//        $copiedFile = $service->files->copy($newFile->getId(), $copiedFile);
//        $service->parents->insert($copiedFile->getId(), $newParent);
//
//        foreach ($service->files->listFiles()->getItems() as $item) {
//            if (preg_match('#^testowy-plik-[\d]+(_[\w]+)?#', $item->getTitle())
//                || preg_match('#^directory-[\d]+#', $item->getTitle())
//            ) {
//                echo '<pre>';
//                var_dump($item->getTitle());
//                echo '</pre>';
//            }
//        }
    }

    if (!isset($_GET['code'])
        && !isset($_SESSION['token'])
        && !$client->getAccessToken()
    ) {
        $client->addScope(Google_Service_Drive::DRIVE);
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

function check($id, $completeList)
{
    $sublist = [];
    /** @var Google_Service_Drive_DriveFile $item */
    foreach ($completeList as $item) {

        $parents = $item->getParents();

        if (isset($parents[0])) {
            /** @var Google_Service_Drive_ParentReference $father */
            $father = $parents[0];
            if ($father->getId() === $id) {
                $sublist[$father->getId()][] = [
                    'main'      => $item->getTitle(),
                    'childs'    => check($item->getId(), $completeList),
                    'id'        => $item->getId(),
                    'mime'      => $item->getMimeType(),
                ];
            }
            
            //$structure[$father->getId()]['files'][] = $item->getTitle();
        }
    }

    if (empty($sublist)) {
        return null;
    }
    return $sublist;
}


?>
    </body>
</html>
<?php
ob_end_flush();
?>