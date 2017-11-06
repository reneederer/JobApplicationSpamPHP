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
        if(trim($str) === '')
        {
            return $str;
        }
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

    function getPDF($odt, $dict, $tmpDirectory)
    {
        $unzipODT = function($odt, $unzipTo)
        {
            echo $odt;
            echo "<br>$unzipTo";
            $zip = new ZipArchive();
            $result = $zip->open($odt);
            if($result === false)
            {
                die("FASELKFASDL");
            }
            $zip->extractTo($unzipTo);
            $zip->close();
        };

        $zipAsODT = function($zipFileFullPath, $directory)
        {
            $zipAsODT1 = function($zip, $baseDirectory, $currentDirectory) use(&$zipAsODT1)
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
            $zip->open($zipFileFullPath, ZipArchive::CREATE);
            $zipAsODT1($zip, $directory, '');
            $zip->close();
        };

        $replaceInDirectory = function($directory, $dict) use (&$replaceInDirectory)
        {
            $replaceInFile = function($file, $dict)
            {
                $str = file_get_contents($file);
                $str = replaceAllInString($str, $dict);
                file_put_contents($file, $str);
            };
            $directoryContent = scandir($directory);
            foreach($directoryContent as $currentItem)
            {
                if($currentItem == '.' || $currentItem == '..')
                {
                    continue;
                }
                if(is_file($directory . $currentItem))
                {
                    //if($currentItem == 'content.xml')
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

        if(!file_exists($tmpDirectory))
        {
            mkdir($tmpDirectory, 0777, true);
        }

        $odtFile = $odt;
        $outDirectory = getNonExistingFileName($tmpDirectory, '') . '/';
        if(!file_exists($outDirectory . 'pdf/'))
        {
            mkdir($outDirectory . 'pdf/', 0777, true);
        }
        if(!file_exists($outDirectory . 'unzipped/'))
        {
            mkdir($outDirectory . 'unzipped/', 0777, true);
        }
        $unzipODT($odtFile, $outDirectory . 'unzipped/');
        $replaceInDirectory($outDirectory . 'unzipped/', $dict);
        $zipAsODT($outDirectory . 'pdf/bewerbung123.odt', $outDirectory . 'unzipped/');
        exec('unoconv ' . $outDirectory . 'pdf/bewerbung123.odt' . ' 2>&1', $output);
        echo '<br><br>unoconv ' . $outDirectory . 'pdf/bewerbung123.odt' . ' 2>1';
        echo "<br>";
        if(count($output) >= 1)
        {
            return [];
        }
        echo "<br>$tmpDirectory";
        echo "<br>odt: $outDirectory$odtFile.pdf<br>";

        return [$outDirectory, substr($odtFile, 0, strlen($odtFile) - 4) . '.pdf'];
    }
