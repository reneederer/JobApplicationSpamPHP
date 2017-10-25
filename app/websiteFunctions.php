<?php
    function readEmployerFromWebsite($url)
    {
        //Helper function to throw an exception with a helpful message when function f returns null or throws an exception.
        $tryGet = function($f, $errorMessage)
        {
            $el = null;
            try
            {
                $el = $f();
            }
            catch(\Exception $e)
            {
                throw new \Exception($errorMessage);
            }
            if(is_null($el)) { throw new \Exception($errorMessage); }
            return $el;
        };



        $readEmployerFromJobBoerseArbeitsAgenturDom = function($dom) use (&$tryGet)
        {
            try
            {
                $companyNameElFunc = function () use (&$dom) { return $dom->getElementById('ruckfragenundbewerbungenan_-2147483648'); };
                $companyNameFunc = function () use (&$companyNameEl) { return $companyNameEl->nodeValue; };
                $anredeFunc =
                    function () use (&$companyNameEl)
                    {
                        preg_match("/" . $companyNameEl->nodeValue . "\\s*(Herr|Frau)/", $companyNameEl->parentNode->nodeValue, $matches);
                        $chefAnrede = (count($matches) == 2) ? $matches[1] : "";
                        return $chefAnrede;
                    };
                $vornameFunc =
                    function() use(&$dom) {
                        $firstNameEl = $dom->getElementById('vorname_-2147483648');
                        if(is_null($firstNameEl))
                        {
                            return '';
                        }
                        return $firstNameEl->nodeValue;
                    };
                $nachnameFunc = function() use(&$dom) { return $dom->getElementById('nachname_-2147483648')->nodeValue; };
                $companyStreetFunc = function() use(&$dom) { return $dom->getElementById("ruckfragenUndBewerbungenAn.wert['adresse']Strasse_-2147483648")->nodeValue; };
                $companyPLZFunc = function() use(&$dom) { return $dom->getElementById("ruckfragenUndBewerbungenAn.wert['adresse']Plz_-2147483648")->nodeValue; };
                $companyCityFunc = function() use(&$dom) { return ($dom->getElementById("ruckfragenUndBewerbungenAn.wert['adresse']Ort_-2147483648")->nodeValue); };
                $companyPhoneNumberFunc =
                    function() use(&$companyNameEl)
                    {
                        preg_match("/.*Telefonnummer:\\s*(.*)/", $companyNameEl->parentNode->parentNode->parentNode->nodeValue, $matches);
                        $phoneNr = $matches[1];
                        return $phoneNr;
                    };
                $companyEmailFunc =
                    function() use(&$dom)
                    {
                        return substr($dom->getElementById("vermittlung.stellenangeboteverwalten.detailszumstellenangebot.email")->getAttribute('href'), 7);
                    };

                $companyNameEl = $tryGet($companyNameElFunc, "Company element could not be read");
                $companyName = htmlspecialchars_decode($tryGet($companyNameFunc, "Company name could not be found"));
                $chefAnrede = htmlspecialchars_decode($tryGet($anredeFunc, "Salutation not found"));
                $chefVorname = htmlspecialchars_decode($tryGet($vornameFunc, "First name not found"));
                $chefNachname = htmlspecialchars_decode($tryGet($nachnameFunc, "Last name not found"));
                $companyStreet = htmlspecialchars_decode($tryGet($companyStreetFunc, "Street not found"));
                $companyPLZ = htmlspecialchars_decode($tryGet($companyPLZFunc, "Post code not found"));
                $companyCity = htmlspecialchars_decode($tryGet($companyCityFunc, "City not found"));
                $companyPhoneNumber = htmlspecialchars_decode($tryGet($companyPhoneNumberFunc, "Phone number not found"));
                $email = htmlspecialchars_decode($tryGet($companyEmailFunc, "Email not found"));
                return ['$chef_anrede_briefkopf' => $chefAnrede === 'Frau' ? 'Frau' : 'Herrn'
                       , '$sehr_geehrter' => ($chefAnrede === 'Frau') ? 'Sehr geehrte' : 'Sehr geehrter'
                       , '$chef_anrede' => $chefAnrede
                       , '$chef_titel' => ''
                       , '$chef_vorname' => $chefVorname
                       , '$chef_nachname' => $chefNachname
                       , '$firma_name' => $companyName
                       , '$firma_strasse' => $companyStreet
                       , '$firma_plz' => $companyPLZ
                       , '$firma_stadt' => $companyCity
                       , '$firma_mobil' => ''
                       , '$firma_telefon' => $companyPhoneNumber
                       , '$firma_email' => $email ];

            }
            catch(\Exception $e)
            {
                echo "Unable to create employer data: " . $e->getMessage();
            }
        };

        $readEmployerFromJobBoerseArbeitsAgentur = function($url) use (&$tryGet, &$readEmployerFromJobBoerseArbeitsAgenturDom)
        {
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $content = file_get_contents($url);
            $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            return $readEmployerFromJobBoerseArbeitsAgenturDom($dom);
        };

        $readEmployerFromJobBoerseArbeitsAgenturByReferenzNummer = function($referenzNummer) use(&$readEmployerFromJobBoerseArbeitsAgentur, &$readEmployerFromJobBoerseArbeitsAgenturDom)
        {
            $url = 'https://jobboerse.arbeitsagentur.de/vamJB/schnellsuche.html';
            $data = array(
                "siesuchen.wert.wert" => "leer",
                "suchbegriff.wert" => substr($referenzNummer, strpos($referenzNummer, ':') + 1),
                "arbeitsort.lokation" => "",
                "_eventId_suchen" => "Suchen",
                "_eventId_erweitertesuch" => "Erweiterte Suche"
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { echo "Failed"; }

            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($result, 'HTML-ENTITIES', 'UTF-8'));
            //$r = $dom->getElementById("ruckfragenundbewerbungenan_-2147483648");
            return $readEmployerFromJobBoerseArbeitsAgenturDom($dom);
        };


        $readEmployerFromFirmenRegister = function($url)
        {
            $dom = new DomDocument();
            libxml_use_internal_errors(true);
            $content = file_get_contents($url);
            $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            $employerTable = $dom->getElementById('hellblau_mitte')->getElementsByTagName("tbody")[0];
            $readValueInsideElement =
                function($key)
                {
                    return $key->nextSibling->firstChild;
                };
            $readPostCode =
                function($key)
                {
                    return $key->nextSibling->firstChild->childNodes[0];
                };
            $readCity =
                function($key)
                {
                    return $key->nextSibling->firstChild->childNodes[2];
                };
            $f = null;
            foreach($employerTable->getElementsByTagName('tr') as $currentRow)
            {
                $key = $currentRow->firstChild;
                $value = null;
                switch($key->nodeValue)
                {
                    case 'Adresse':
                    case 'Mobil':
                    case 'Firmenname':
                    case 'E-Mail':
                    case 'Kontakt':
                        $value = $readValueInsideElement($key);
                        //echo $key->nodeValue . ": " . htmlspecialchars_decode($value->nodeValue) . "<br>";
                        break;
                    case 'PLZ / Ort':
                        $postCodeValue = $readPostCode($key);
                        //echo $key->nodeValue . ": " . htmlspecialchars_decode($postCodeValue->nodeValue) . "<br>";
                        $cityValue = $readCity($key);
                        //echo $key->nodeValue . ": " . htmlspecialchars_decode($cityValue->nodeValue) . "<br>";
                        break;
                    case 'Telefon':
                        $vorwahlKey = $readValueInsideElement($key);
                        $phoneNumber = $key->nextSibling->childNodes[1];
                        //echo $key->nodeValue . ": "
                        //    . htmlspecialchars_decode($vorwahlKey->nodeValue)
                        //    . htmlspecialchars_decode($phoneNumber->nodeValue) . "<br>";
                        break;
                }
            }
        };


        $websiteDict = Array("jobboerse.arbeitsagentur" => $readEmployerFromJobBoerseArbeitsAgentur,
            "jobboerse_arbeitsagentur" => $readEmployerFromJobBoerseArbeitsAgentur,
            "firmenregister" => $readEmployerFromFirmenRegister, 
            "ref:" => $readEmployerFromJobBoerseArbeitsAgenturByReferenzNummer);
        foreach($websiteDict as $hostname => $websiteFunc)
        {
            if(strpos($url, $hostname) !== false)
            {
                return $websiteFunc($url);
            }
        }
    }
?>
