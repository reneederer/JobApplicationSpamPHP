<?php
    function getNonExistingFileName($baseDir, $ext)
    {
        $fileName = "";
        do
        {
            $fileName = $baseDir . uniqid() . '.' . $ext;
        } while(file_exists($fileName));
        return $fileName;
    }





?>
