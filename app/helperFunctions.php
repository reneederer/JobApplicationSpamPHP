<?php
    function getNonExistingFileName($baseDir)
    {
        $fileName = "";
        do
        {
            $fileName = $baseDir . uniqid() . '.odt';
        } while(file_exists($fileName));
        return $fileName;
    }





?>
