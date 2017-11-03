<?php
    function replaceAllInString($str, $dict)
    {
        foreach($dict as $search => $replaceWith)
        {
            $pattern = "\\\$" . substr($search, 1, strlen($search)) . "(?=([^a-zA-Z0-9_]|\$))";
            if($replaceWith === '')
            {
                $pattern = "/(\\s$pattern|$pattern\\s|$pattern)/";
            }
            else
            {
                $pattern = "/$pattern/";
            }
            $count = 0;
            $str = preg_replace($pattern, $replaceWith, $str, -1, $count);
        }
        return $str;
    }

    function replaceAllInStringRemoveSpanTags($str, $dict)
    {
        $dom = new DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
        $spanElements = $dom->getElementsByTagName('span');
        for($i = $spanElements->length - 1; $i >= 0; --$i)
        {
            $currentNode = $spanElements->item($i);
            $currentNode->parentNode->replaceChild($dom->createTextNode($currentNode->nodeValue), $currentNode);
        }
        $str = $dom->saveXML();
        foreach($dict as $search => $replaceWith)
        {
            $pattern = '/' . str_replace("\$", "\\$", $search) . '/';
            $str = preg_replace($pattern, $replaceWith, $str);
        }
        return $str;
    }

    function rrmAllExcept($directory, $name)
    {
        foreach(scandir($directory) as $currentItem)
        {
            if($currentItem === '.' || $currentItem === '..' || $currentItem === $name)
            {
                continue;
            }
            if(is_file($directory . $currentItem))
            {
                try
                {
                    if(unlink($directory . $currentItem) === false)
                    {
                    }
                }
                catch(Exception $e)
                {
                }
            }
            else if(is_dir($directory . $currentItem))
            {
                rrmdir($directory . $currentItem . '/');
            }
        }
        if(count(scandir($directory)) === 2)
        {
            rmdir($directory);
        }
    }


    function rrmdir($directory)
    {
        foreach(scandir($directory) as $currentItem)
        {
            if($currentItem === '.' || $currentItem === '..')
            {
                continue;
            }
            if(is_file($directory . $currentItem))
            {
                try
                {
                    if(unlink($directory . $currentItem) === false)
                    {
                    }
                }
                catch(Exception $e)
                {
                }
            }
            else if(is_dir($directory . $currentItem))
            {
                rrmdir($directory . $currentItem . '/');
            }
        }
        rmdir($directory);
    }

    function getPDF($odt, $dict)
    {
        $unzipODT = function($odt, $unzipTo)
        {
            $zip = new ZipArchive;
            $result = $zip->open($odt);
            $zip->extractTo($unzipTo);
            $zip->close();
        };

        $zipAsODT = function($zipFileName, $directory)
        {
            $zipAsODT1 = function ($zip, $baseDirectory, $currentDirectory) use(&$zipAsODT1)
            {
                $directoryContent = scandir($baseDirectory . $currentDirectory);
                foreach($directoryContent as $currentItem)
                {
                    if($currentItem == '.' || $currentItem == '..' || strpos(".odt", $currentItem) === true)
                    {
                        continue;
                    }
                    if(is_file($baseDirectory . $currentDirectory . $currentItem))
                    {
                        $zip->addFile($baseDirectory . $currentDirectory . $currentItem, str_replace('\\', '/', $currentDirectory) . $currentItem);
                    }
                    else if(is_dir($baseDirectory . $currentDirectory. $currentItem))
                    {
                        $zip->addEmptyDir(str_replace('\\', '/', $currentDirectory) . $currentItem);
                        $zipAsODT1($zip, $baseDirectory, $currentDirectory . $currentItem . '/');
                    }
                }
            };
            $zip = new ZipArchive;
            $zip->open($directory . $zipFileName, ZipArchive::CREATE);
            $zipAsODT1($zip, $directory, '');
            $zip->close();

        };

        $replaceInDirectory = function($directory, $dict) use (&$replaceInDirectory)
        {
            $replaceInFile = function($file, $dict)
            {
                $str = file_get_contents($file);
                $str = replaceAllInStringRemoveSpanTags($str, $dict);
                file_put_contents($file, $str);
            };
            $directoryContent = scandir($directory);
            foreach($directoryContent as $currentItem)
            {
                if($currentItem == "." || $currentItem == "..")
                {
                    continue;
                }
                if(is_file($directory . $currentItem))
                {
                    //if($currentItem == "content.xml")
                    {
                        $replaceInFile($directory . $currentItem, $dict);
                    }
                }
                else if(is_dir($directory . $currentItem))
                {
                    $replaceInDirectory($directory . $currentItem . '/', $dict);
                }
            }
        };
        $tmpDirectory = '';
        do
        {
            $tmpDirectory = sys_get_temp_dir() . '/' . uniqid() . '/';
        }
        while(is_dir($tmpDirectory));
        mkdir($tmpDirectory);
        $odtFile = 'yourJobApplication.odt';
        $directory = $tmpDirectory;
        file_put_contents($tmpDirectory . $odtFile, $odt);
        $outDirectory = $tmpDirectory . "out/";
        $unzipODT($tmpDirectory . $odtFile, $outDirectory);
        $replaceInDirectory($outDirectory, $dict);
        $zipAsODT($odtFile, $outDirectory);
        $output = "";
        exec($config['unoconv'] . ' ' . $outDirectory . $odtFile . '" 2>&1', $output);
        if(count($output) >= 1)
        {
            var_dump($output);
            return [];
        }
        //rrmAllExcept($tmpDirectory, substr($odtFile, 0, strlen($odtFile) - 4) . '.pdf');
        return [$outDirectory, substr($odtFile, 0, strlen($odtFile) - 4) . '.pdf'];
    }

    function getPDFFromFile($directory, $odtFile, $dict)
    {


        $unzipODT = function($odt, $unzipTo)
        {
            $zip = new ZipArchive;
            $result = $zip->open($odt);
            $zip->extractTo($unzipTo);
            $zip->close();
        };

        $zipAsODT = function($zipFileName, $directory)
        {
            $zipAsODT1 = function ($zip, $baseDirectory, $currentDirectory) use(&$zipAsODT1)
            {
                $directoryContent = scandir($baseDirectory . $currentDirectory);
                foreach($directoryContent as $currentItem)
                {
                    if($currentItem == '.' || $currentItem == '..' || strpos(".odt", $currentItem) === true)
                    {
                        continue;
                    }
                    if(is_file($baseDirectory . $currentDirectory . $currentItem))
                    {
                        $zip->addFile($baseDirectory . $currentDirectory . $currentItem, str_replace('\\', '/', $currentDirectory) . $currentItem);
                    }
                    else if(is_dir($baseDirectory . $currentDirectory. $currentItem))
                    {
                        $zip->addEmptyDir(str_replace('\\', '/', $currentDirectory) . $currentItem);
                        $zipAsODT1($zip, $baseDirectory, $currentDirectory . $currentItem . '/');
                    }
                }
            };
            $zip = new ZipArchive;
            $zip->open($directory . $zipFileName, ZipArchive::CREATE);
            $zipAsODT1($zip, $directory, '');
            $zip->close();

        };


        $replaceInDirectory = function($directory, $dict) use (&$replaceInDirectory)
        {
            $replaceInFile = function($file, $dict)
            {
                $str = file_get_contents($file);
                $str = replaceAllInStringRemoveSpanTags($str, $dict);
                file_put_contents($file, $str);
            };
            $directoryContent = scandir($directory);
            foreach($directoryContent as $currentItem)
            {
                if($currentItem == "." || $currentItem == "..")
                {
                    continue;
                }
                if(is_file($directory . $currentItem))
                {
                    //if($currentItem === "content.xml")
                    {
                        $replaceInFile($directory . $currentItem, $dict);
                    }
                }
                else if(is_dir($directory . $currentItem))
                {
                    $replaceInDirectory($directory . $currentItem . '/', $dict);
                }
            }
        };
        $tmpDirectory = '';
        do
        {
            $tmpDirectory = sys_get_temp_dir() . '/' . uniqid() . '/';
        }
        while(is_dir($tmpDirectory));
        mkdir($tmpDirectory);
        $unzipODT($directory . $odtFile, $tmpDirectory);
        $replaceInDirectory($tmpDirectory, $dict);
        $zipAsODT($odtFile, $tmpDirectory);
        $output = "";
        exec($config['unoconv'] . ' -f pdf -eUseLossLessCompression=true "' . $tmpDirectory . $odtFile . '" 2>&1', $output);
        rrmAllExcept($tmpDirectory, substr($odtFile, 0, strlen($odtFile) - 4) . '.pdf');
        return [$tmpDirectory, substr($odtFile, 0, strlen($odtFile) - 4) . '.pdf'];
    }
?>