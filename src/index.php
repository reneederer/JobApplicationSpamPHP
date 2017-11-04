<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../vendor/phpmailer/phpmailer/src/Exception.php');
    require_once('../vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require_once('../vendor/phpmailer/phpmailer/src/SMTP.php');

    require_once('config.php');
    require_once('odtFunctions.php');
    require_once('websiteFunctions.php');
    require_once('dbFunctions.php');
    require_once('helperFunctions.php');

    session_start();

    phpinfo();
    die("");


    $dbConn = new PDO('mysql:host=localhost;dbname=' . $config["database"]["database"], $config['database']['username'], $config['database']['password']);
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConn->exec("SET NAMES utf8");


    if(isset($_POST['sbmLoginForm']))
    {
        $_SESSION['user'] = identifyUser($dbConn, $_POST['txtName'], $_POST['txtPassword']);
        if(count($_SESSION['user']) > 0)
        {
            $userValues = getUserValues($dbConn, $_SESSION['user']['id']);
            foreach($userValues as $key => $value)
            {
                $_SESSION['user'][$key] = $value;
            }
            $_SESSION['user']['userName'] = $_POST['txtName'];
        }
    }
    else if(isset($_POST['sbmLogout']))
    {
        logout();
    }
    else if(isset($_POST['sbmSetUserValues']))
    {
        updateUserValues($dbConn, $_SESSION['user']['id'], $_POST['txtFirstName'], $_POST['txtLastName'], $_POST['txtSalutation'], $_POST['txtTitle'], $_POST['txtStreet'], $_POST['txtPostCode'], $_POST['txtCity'], $_POST['txtEmail'], $_POST['txtMobilePhone'], $_POST['txtPhone']);
    }
    else if(isset($_POST['sbmAddEmployer']))
    {
        addEmployer($dbConn, $_SESSION['user']['id'], $_POST['txtCompany'], $_POST['txtStreet'], $_POST['txtPostCode'], $_POST['txtCity'], $_POST['txtSalutation'], $_POST['txtTitle'], $_POST['txtFirstName'], $_POST['txtLastName'], $_POST['txtEmail'], $_POST['txtMobilePhone'], $_POST['txtPhone']);
    }
    else if(isset($_POST['sbmDownloadPDF']))
    {
        $dict = readEmployerFromWebsite('http://localhost/jobApplicationSpam/jobboerseArbeitsagentur.html');
        $directoryAndFileName = getPDF($directory, $odtFile, $dict);
        addToDownloads($dbConn, $directoryAndFileName[0], $_SESSION['user']['id']);
        header('Content-type:application/pdf');
        header("Content-Disposition:attachment;filename=jobApplication.pdf");
        echo file_get_contents($directoryAndFileName[0] .  $directoryAndFileName[1]);
    }
    else if(isset($_POST['sbmUploadJobApplicationTemplate']))
    {
        $baseDir = "c:/uniserverz/user/" . $_SESSION['user']['userName'] . '/';
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }
        $odtFileName = getNonExistingFileName($baseDir);
        move_uploaded_file($_FILES['fileODT']['tmp_name'], $odtFileName);
        addJobApplicationTemplate( $dbConn
                                 , $_SESSION['user']['id']
                                 , $_POST['txtJobApplicationTemplateName']
                                 , $_POST['txtUserAppliesAs']
                                 , $_POST['txtEmailSubject']
                                 , $_POST['txtEmailBody']
                                 , $odtFileName);
        $templateId = getTemplateIdByName($dbConn, $_SESSION['user']['id'], $_POST['txtJobApplicationTemplateName']);
        for($i = 0; $i < count($_FILES['fileAppendices']['tmp_name']); ++$i)
        {
            if($_FILES['fileAppendices']['tmp_name'][$i] !== '')
            {
                $pdfAppendixFileName = getNonExistingFileName($baseDir);
                move_uploaded_file($_FILES['fileAppendices']['tmp_name'][$i], $pdfAppendixFileName);
                addPdfAppendix( $dbConn
                              , $_POST['txtJobApplicationTemplateName']
                              , $templateId
                              , $pdfAppendixFileName);
            }
        }
    }
    else if(isset($_POST['sbmApplyNowForReal']) || isset($_POST['sbmApplyNowForTest']))
    {
        $employerIndex = getEmployerIndex($dbConn, $_SESSION['user']['id'], $_POST['hidEmployerIndex']);
        $employerValuesDict = getEmployer($dbConn, $_SESSION['user']['id'], $employerIndex);
        $userValuesDict =
            [ '$meinTitel' => $_SESSION['user']['title']
            , '$meineAnrede' => $_SESSION['user']['salutation']
            , '$meinVorname' => $_SESSION['user']['firstName']
            , '$meinNachname' => $_SESSION['user']['lastName']
            , '$meineStrasse' => $_SESSION['user']['street']
            , '$meinePlz' => $_SESSION['user']['postCode']
            , '$meineStadt' => $_SESSION['user']['city']
            , '$meineEmail' => $_SESSION['user']['email']
            , '$meineTelefonnr' => $_SESSION['user']['phone']
            , '$meineMobilnr' => $_SESSION['user']['mobilePhone']
            , '$meinGeburtsdatum' => $_SESSION['user']['birthday']
            , '$meinGeburtsort' => $_SESSION['user']['birthplace']
            , '$meinFamilienstand' => $_SESSION['user']['maritalStatus'] ];
        $dict = $employerValuesDict + $userValuesDict +
            [ "\$geehrter" => $employerValuesDict["\$chefAnrede"] === "Herr" ? 'geehrter' : 'geehrte'
            , "\$chefAnredeBriefkopf" => $employerValuesDict["\$chefAnrede"] === "Herr" ? 'Herrn' : 'Frau'
            , "\$datumHeute" => date('d.m.Y')];

        $jobApplicationTemplate = getJobApplicationTemplate($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
        $pdfDirectoryAndFile = getPDF(file_get_contents($jobApplicationTemplate['odtFile']), $dict);
        addToDownloads($dbConn, $pdfDirectoryAndFile[0], $_SESSION['user']['id']);
        $templateId = getTemplateIdByIndex($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
        addJobApplication($dbConn, $_SESSION['user']['id'], $employerIndex, $templateId);
        $pdfAppendices = getPdfAppendices($dbConn, $templateId);
        $m = new Merger();
        $m->addFromFile($pdfDirectoryAndFile[0] . $pdfDirectoryAndFile[1]);
        foreach($pdfAppendices as $currentPdfAppendix)
        {
            $m->addFromFile($currentPdfAppendix['pdfFile']);
        }
        $pdfFileName = $pdfDirectoryAndFile[0] . str_replace(" ", "_", mb_strtolower($_SESSION['user']['lastName'] . '_bewerbung_als_' . $jobApplicationTemplate['userAppliesAs'])) . '.pdf';
        file_put_contents($pdfFileName, $m->merge());
        sendMail( $_SESSION['user']['email']
            , $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']
            , replaceAllInStringIgnoreTags($jobApplicationTemplate['emailSubject'], $dict)
            , replaceAllInStringIgnoreTags($jobApplicationTemplate['emailBody'], $dict)
            , isset($_POST['sbmApplyNowForReal']) ? $employerValuesDict['$firmaEmail'] : $_SESSION['user']['email']
            , [$pdfFileName]);
    }
    else if(isset($_POST['sbmDownloadSentApplications']))
    {
        $data = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0);
        $pdf = new FPDF();
        //    $pdf->AddPage();
         //   $pdf->SetFont('Arial', '', 13);
          //  $pdf->Cell(40, 10, 'hello world');
        // Colors, line width and bold font
        $pdf->AddPage();
        $pdf->SetFont('Arial','B', 15);
        //foreach($header as $col)
        //{
         //   $pdf->Cell(40,7,$col,1);
          //  $pdf->Ln();
        //}
        $pdf->MultiCell(0, 10, "Rene Ederer");
        $pdf->MultiCell(0, 10, "Bewerbungen 01.10.2017 - 24.10.2017\n");
        $pdf->MultiCell(0, 15, "");
        $pdf->SetFont('Arial','', 13);
        $w = [50, 135];
        foreach($data as $row)
        {
            $i = 0;
            foreach($row as $col)
            {
                $pdf->Cell($w[$i],6,$col,1);
                ++$i;
                if($i >= 2){break;}
            }
            $pdf->Ln();
        }
        $pdf->Output();
    }


    function logout()
    {
        $_SESSION['user'] = Array();
    }

    function sendMail($from, $fromName, $subject, $body, $to, $attachments)
    {
        try
        {
            $email = new PHPMailer(true);
            $email->CharSet = 'UTF-8';
            $email->Host = 'tls://smtp.gmail.com';
            $email->Port = 587;
            $email->SMTPAuth = true;
            $email->SMTPSecure = 'tls';
            $email->IsSMTP();
            $email->Username = $config['email']['username'];
            $email->Password = $config['email']['password'];
            $email->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true));
            //$email->SMTPDebug = 4;
            //echo "<br>" . $email->Username . "," . $email->Password . ".";
            $email->From = $from;
            $email->FromName = $fromName;
            $email->Subject = $subject;
            $email->Body = $body;
            $email->AddBCC($_SESSION['user']['email'], $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']);
            $email->AddAddress($to);
            $email->AddAttachment($attachments[0], $attachments[0]);
            $email->Send();
        }
        catch(phpmailerException $e)
        {
            echo $e->errorMessage();
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<script src="js/jquery-3.2.1.slim.min.js"></script>

<title>Meine Bewerbung</title>


    <script>
     Sticky Header
     $(window).scroll(function() {
    
        if ($(window).scrollTop() > 100) {
                $('.main_h').addClass('sticky');
                    } else {
                            $('.main_h').removeClass('sticky');
                                }
                                });
   
                                // Mobile Navigation
                                $('.mobile-toggle').click(function() {
                                    if ($('.main_h').hasClass('open-nav')) {
                                            $('.main_h').removeClass('open-nav');
                                                } else {
                                                        $('.main_h').addClass('open-nav');
                                                            }
                                                            });
   
                                                            $('.main_h li a').click(function() {
                                                                if ($('.main_h').hasClass('open-nav')) {
                                                                        $('.navigation').removeClass('open-nav');
                                                                                $('.main_h').removeClass('open-nav');
                                                                                    }
                                                                                    });
   
                                                                                    // Navigation Scroll - ljepo radi materem
                                                                                    $('nav a').click(function(event) {
                                                                                        var id = $(this).attr("href");
                                                                                            var offset = 70;
                                                                                                var target = $(id).offset().top - offset;
                                                                                                    $('html, body').animate({
                                                                                                            scrollTop: target
                                                                                                                }, 500);
                                                                                                                    event.preventDefault();
                                                                                                                    });
    </script>

<style>



@mixin small {
  @media only screen and (max-width: 766px) {
    @content;
  }
}

 // colores
 $color: #8f8f8f;
 $color2: #e8f380;

 // Navigation
 .main_h {
   position: fixed;
     max-height: 70px;
       z-index: 999;
         width: 100%;
           padding-top: 17px;
             background: none;
               overflow: hidden;
                 -webkit-transition: all 0.3s;
                   transition: all 0.3s;
                     opacity: 0;
                       top: -100px;
                         padding-bottom: 6px;
                           font-family: "Montserrat", sans-serif;
                             @include small {
                                 padding-top: 25px;
                                   }
                                   }

                                   .open-nav {
                                     max-height: 400px !important;
                                       .mobile-toggle {
                                           transform: rotate(-90deg);
                                               -webkit-transform: rotate(-90deg);
                                                 }
                                                 }

                                                 .sticky {
                                                   background-color: rgba(255, 255, 255, 0.93);
                                                     opacity: 1;
                                                       top: 0px;
                                                         border-bottom: 1px solid lighten($color, 30%);
                                                         }

                                                         .logo {
                                                           width: 50px;
                                                             font-size: 25px;
                                                               color: $color;
                                                                 text-transform: uppercase;
                                                                   float: left;
                                                                     display: block;
                                                                       margin-top: 0;
                                                                         line-height: 1;
                                                                           margin-bottom: 10px;
                                                                             @include small {
                                                                                 float: none;
                                                                                   }
                                                                                   }

                                                                                   nav {
                                                                                     float: right;
                                                                                       width: 60%;
                                                                                         @include small {
                                                                                             width: 100%;
                                                                                               }

                                                                                                 ul {
                                                                                                     list-style: none;
                                                                                                         overflow: hidden;
                                                                                                             text-align: right;
                                                                                                                 float: right;
                                                                                                                     @include small {
                                                                                                                           padding-top: 10px;
                                                                                                                                 margin-bottom: 22px;
                                                                                                                                       float: left;
                                                                                                                                             text-align: center;
                                                                                                                                                   width: 100%;
                                                                                                                                                       }

                                                                                                                                                           li {
                                                                                                                                                                 display: inline-block;
                                                                                                                                                                       margin-left: 35px;
                                                                                                                                                                             line-height: 1.5;
                                                                                                                                                                                   @include small {
                                                                                                                                                                                           width: 100%;
                                                                                                                                                                                                   padding: 7px 0;
                                                                                                                                                                                                           margin: 0;
                                                                                                                                                                                                                 }
                                                                                                                                                                                                                     }
                                                                                                                                                                                                                         a {
                                                                                                                                                                                                                               color: #888888;
                                                                                                                                                                                                                                     text-transform: uppercase;
                                                                                                                                                                                                                                           font-size: 12px;
                                                                                                                                                                                                                                               }
                                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                                                 }

                                                                                                                                                                                                                                                 .mobile-toggle {
                                                                                                                                                                                                                                                   display: none;
                                                                                                                                                                                                                                                     cursor: pointer;
                                                                                                                                                                                                                                                       font-size: 20px;
                                                                                                                                                                                                                                                         position: absolute;
                                                                                                                                                                                                                                                           right: 22px;
                                                                                                                                                                                                                                                             top: 0;
                                                                                                                                                                                                                                                               width: 30px;
                                                                                                                                                                                                                                                                 -webkit-transition: all 200ms ease-in;
                                                                                                                                                                                                                                                                   -moz-transition: all 200ms ease-in;
                                                                                                                                                                                                                                                                     transition: all 200ms ease-in;
                                                                                                                                                                                                                                                                       @include small {
                                                                                                                                                                                                                                                                           display: block;
                                                                                                                                                                                                                                                                             }

                                                                                                                                                                                                                                                                               span {
                                                                                                                                                                                                                                                                                   width: 30px;
                                                                                                                                                                                                                                                                                       height: 4px;
                                                                                                                                                                                                                                                                                           margin-bottom: 6px;
                                                                                                                                                                                                                                                                                               border-radius: 1000px;
                                                                                                                                                                                                                                                                                                   background: $color;
                                                                                                                                                                                                                                                                                                       display: block;
                                                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                                                         }

                                                                                                                                                                                                                                                                                                         .row {
                                                                                                                                                                                                                                                                                                           width: 100%;
                                                                                                                                                                                                                                                                                                             max-width: 940px;
                                                                                                                                                                                                                                                                                                               margin: 0 auto;
                                                                                                                                                                                                                                                                                                                 position: relative;
                                                                                                                                                                                                                                                                                                                   padding: 0 2%;
                                                                                                                                                                                                                                                                                                                   }


                                                                                                                                                                                                                                                                                                                   // Page Style
                                                                                                                                                                                                                                                                                                                   * {
                                                                                                                                                                                                                                                                                                                     box-sizing: border-box;
                                                                                                                                                                                                                                                                                                                     }

                                                                                                                                                                                                                                                                                                                     body {
                                                                                                                                                                                                                                                                                                                       color: $color;
                                                                                                                                                                                                                                                                                                                         background: white;
                                                                                                                                                                                                                                                                                                                           font-family: "Cardo", serif;
                                                                                                                                                                                                                                                                                                                             font-weight: 300;
                                                                                                                                                                                                                                                                                                                               -webkit-font-smoothing: antialiased;
                                                                                                                                                                                                                                                                                                                               }

                                                                                                                                                                                                                                                                                                                               a {
                                                                                                                                                                                                                                                                                                                                 text-decoration: none;
                                                                                                                                                                                                                                                                                                                                 }

                                                                                                                                                                                                                                                                                                                                 h1 {
                                                                                                                                                                                                                                                                                                                                   font-size: 30px;
                                                                                                                                                                                                                                                                                                                                     line-height: 1.8;
                                                                                                                                                                                                                                                                                                                                       text-transform: uppercase;
                                                                                                                                                                                                                                                                                                                                         font-family: "Montserrat", sans-serif;
                                                                                                                                                                                                                                                                                                                                         }

                                                                                                                                                                                                                                                                                                                                         p {
                                                                                                                                                                                                                                                                                                                                           margin-bottom: 20px;
                                                                                                                                                                                                                                                                                                                                             font-size: 17px;
                                                                                                                                                                                                                                                                                                                                               line-height: 2;
                                                                                                                                                                                                                                                                                                                                               }

                                                                                                                                                                                                                                                                                                                                               .content {
                                                                                                                                                                                                                                                                                                                                                 padding: 50px 2% 250px;
                                                                                                                                                                                                                                                                                                                                                 }

                                                                                                                                                                                                                                                                                                                                                 .hero {
                                                                                                                                                                                                                                                                                                                                                   position: relative;
                                                                                                                                                                                                                                                                                                                                                     background: #333 url(http://srdjanpajdic.com/slike/2.jpg) no-repeat center center fixed;
                                                                                                                                                                                                                                                                                                                                                       -webkit-background-size: cover;
                                                                                                                                                                                                                                                                                                                                                         -moz-background-size: cover;
                                                                                                                                                                                                                                                                                                                                                           background-size: cover;
                                                                                                                                                                                                                                                                                                                                                             text-align: center;
                                                                                                                                                                                                                                                                                                                                                               color: #fff;
                                                                                                                                                                                                                                                                                                                                                                 padding-top: 110px;
                                                                                                                                                                                                                                                                                                                                                                   min-height: 500px;
                                                                                                                                                                                                                                                                                                                                                                     letter-spacing: 2px;
                                                                                                                                                                                                                                                                                                                                                                       font-family: "Montserrat", sans-serif;

                                                                                                                                                                                                                                                                                                                                                                         h1 {
                                                                                                                                                                                                                                                                                                                                                                             font-size: 50px;
                                                                                                                                                                                                                                                                                                                                                                                 line-height: 1.3;

                                                                                                                                                                                                                                                                                                                                                                                         span {
                                                                                                                                                                                                                                                                                                                                                                                               font-size: 25px;
                                                                                                                                                                                                                                                                                                                                                                                                     color: $color2;
                                                                                                                                                                                                                                                                                                                                                                                                           border-bottom: 2px solid $color2;
                                                                                                                                                                                                                                                                                                                                                                                                                 padding-bottom: 12px;
                                                                                                                                                                                                                                                                                                                                                                                                                       line-height: 3;
                                                                                                                                                                                                                                                                                                                                                                                                                           }
                                                                                                                                                                                                                                                                                                                                                                                                                             }
                                                                                                                                                                                                                                                                                                                                                                                                                             }

                                                                                                                                                                                                                                                                                                                                                                                                                             .mouse {
                                                                                                                                                                                                                                                                                                                                                                                                                               display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                 margin: 0 auto;
                                                                                                                                                                                                                                                                                                                                                                                                                                   width: 26px;
                                                                                                                                                                                                                                                                                                                                                                                                                                     height: 46px;
                                                                                                                                                                                                                                                                                                                                                                                                                                       border-radius: 13px;
                                                                                                                                                                                                                                                                                                                                                                                                                                         border: 2px solid $color2;
                                                                                                                                                                                                                                                                                                                                                                                                                                           bottom: 40px;
                                                                                                                                                                                                                                                                                                                                                                                                                                             position: absolute;
                                                                                                                                                                                                                                                                                                                                                                                                                                               left: 50%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                 margin-left: -14px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                   span {
                                                                                                                                                                                                                                                                                                                                                                                                                                                       display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                                           margin: 6px auto;
                                                                                                                                                                                                                                                                                                                                                                                                                                                               width: 2px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                   height: 2px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       border-radius: 4px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                           background: $color2;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                               border: 1px solid transparent;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   -webkit-animation-duration: 1s;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       animation-duration: 1s;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           -webkit-animation-fill-mode: both;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               animation-fill-mode: both;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   -webkit-animation-iteration-count: infinite;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       animation-iteration-count: infinite;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           -webkit-animation-name: scroll;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               animation-name: scroll;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 @-webkit-keyframes scroll {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   0% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       opacity: 1;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           -webkit-transform: translateY(0);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               transform: translateY(0);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   100% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       opacity: 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           -webkit-transform: translateY(20px);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               transform: translateY(20px);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 }


                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 @keyframes scroll {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   0% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       opacity: 1;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           -webkit-transform: translateY(0);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               -ms-transform: translateY(0);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   transform: translateY(0);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       100% {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           opacity: 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               -webkit-transform: translateY(20px);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   -ms-transform: translateY(20px);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       transform: translateY(20px);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         }














</style>




</head>
<body>
<link href='https://fonts.googleapis.com/css?family=Montserrat|Cardo' rel='stylesheet' type='text/css'>
  
<header class="main_h">

    <div class="row">
        <a class="logo" href="#">L/F</a>

        <div class="mobile-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav>
            <ul>
                <li><a href=".sec01">Section 01</a></li>
                <li><a href=".sec02">Section 02</a></li>
                <li><a href=".sec03">Section 03</a></li>
                <li><a href=".sec04">Section 04</a></li>
            </ul>
        </nav>

    </div> <!-- / row -->

</header>

<div class="hero">

    <h1><span>loser friendly</span><br>Batman nav.</h1>

    <div class="mouse">
        <span></span>
    </div>

</div>

<div class="row content">
    <h1 class="sec01">Section 01</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, recusandae, at, labore velit eligendi amet nobis repellat natus sequi sint consectetur excepturi doloribus vero provident consequuntur accusamus quisquam nesciunt cupiditate soluta alias illo et deleniti voluptates facilis repudiandae similique dolore quaerat architecto perspiciatis officiis dolor ullam expedita suscipit neque minima rem praesentium inventore ab officia quos dignissimos esse quam placeat iste porro eius! Minus, aspernatur nesciunt consectetur. Sit, eius, itaque, porro, beatae impedit officia tenetur reiciendis autem vitae a quae ipsam repudiandae odio dolorum quaerat asperiores possimus corporis optio animi quisquam laboriosam nihil quam voluptatum quidem veritatis iste culpa iure modi perspiciatis recusandae ipsa libero officiis aliquam doloremque similique id quasi atque distinctio enim sapiente ratione in quia eum perferendis earum blanditiis. Nobis, architecto, veniam molestias minus iste necessitatibus est ab in earum ratione eveniet soluta molestiae sed illo nostrum nemo debitis. Minus, quod totam aliquam ea asperiores fugit quaerat excepturi dolores ratione numquam consequatur id unde alias provident vero incidunt exercitationem similique consequuntur hic possimus? Fuga, eveniet quaerat inventore corporis laborum eligendi enim soluta obcaecati aliquid veritatis provident amet laudantium est quisquam dolore exercitationem modi? Distinctio, pariatur, ab velit praesentium vitae quidem consequatur deleniti recusandae odit officiis. Quidem, cupiditate.</p>
    <h1 class="sec02">Section 02</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, recusandae, at, labore velit eligendi amet nobis repellat natus sequi sint consectetur excepturi doloribus vero provident consequuntur accusamus quisquam nesciunt cupiditate soluta alias illo et deleniti voluptates facilis repudiandae similique dolore quaerat architecto perspiciatis officiis dolor ullam expedita suscipit neque minima rem praesentium inventore ab officia quos dignissimos esse quam placeat iste porro eius! Minus, aspernatur nesciunt consectetur. Sit, eius, itaque, porro, beatae impedit officia tenetur reiciendis autem vitae a quae ipsam repudiandae odio dolorum quaerat asperiores possimus corporis optio animi quisquam laboriosam nihil quam voluptatum quidem veritatis iste culpa iure modi perspiciatis recusandae ipsa libero officiis aliquam doloremque similique id quasi atque distinctio enim sapiente ratione in quia eum perferendis earum blanditiis. Nobis, architecto, veniam molestias minus iste necessitatibus est ab in earum ratione eveniet soluta molestiae sed illo nostrum nemo debitis. Minus, quod totam aliquam ea asperiores fugit quaerat excepturi dolores ratione numquam consequatur id unde alias provident vero incidunt exercitationem similique consequuntur hic possimus? Fuga, eveniet quaerat inventore corporis laborum eligendi enim soluta obcaecati aliquid veritatis provident amet laudantium est quisquam dolore exercitationem modi? Distinctio, pariatur, ab velit praesentium vitae quidem consequatur deleniti recusandae odit officiis. Quidem, cupiditate.</p>
    <h1 class="sec03">Section 03</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, recusandae, at, labore velit eligendi amet nobis repellat natus sequi sint consectetur excepturi doloribus vero provident consequuntur accusamus quisquam nesciunt cupiditate soluta alias illo et deleniti voluptates facilis repudiandae similique dolore quaerat architecto perspiciatis officiis dolor ullam expedita suscipit neque minima rem praesentium inventore ab officia quos dignissimos esse quam placeat iste porro eius! Minus, aspernatur nesciunt consectetur. Sit, eius, itaque, porro, beatae impedit officia tenetur reiciendis autem vitae a quae ipsam repudiandae odio dolorum quaerat asperiores possimus corporis optio animi quisquam laboriosam nihil quam voluptatum quidem veritatis iste culpa iure modi perspiciatis recusandae ipsa libero officiis aliquam doloremque similique id quasi atque distinctio enim sapiente ratione in quia eum perferendis earum blanditiis. Nobis, architecto, veniam molestias minus iste necessitatibus est ab in earum ratione eveniet soluta molestiae sed illo nostrum nemo debitis. Minus, quod totam aliquam ea asperiores fugit quaerat excepturi dolores ratione numquam consequatur id unde alias provident vero incidunt exercitationem similique consequuntur hic possimus? Fuga, eveniet quaerat inventore corporis laborum eligendi enim soluta obcaecati aliquid veritatis provident amet laudantium est quisquam dolore exercitationem modi? Distinctio, pariatur, ab velit praesentium vitae quidem consequatur deleniti recusandae odit officiis. Quidem, cupiditate.</p>
    <h1 class="sec04">Section 04</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, recusandae, at, labore velit eligendi amet nobis repellat natus sequi sint consectetur excepturi doloribus vero provident consequuntur accusamus quisquam nesciunt cupiditate soluta alias illo et deleniti voluptates facilis repudiandae similique dolore quaerat architecto perspiciatis officiis dolor ullam expedita suscipit neque minima rem praesentium inventore ab officia quos dignissimos esse quam placeat iste porro eius! Minus, aspernatur nesciunt consectetur. Sit, eius, itaque, porro, beatae impedit officia tenetur reiciendis autem vitae a quae ipsam repudiandae odio dolorum quaerat asperiores possimus corporis optio animi quisquam laboriosam nihil quam voluptatum quidem veritatis iste culpa iure modi perspiciatis recusandae ipsa libero officiis aliquam doloremque similique id quasi atque distinctio enim sapiente ratione in quia eum perferendis earum blanditiis. Nobis, architecto, veniam molestias minus iste necessitatibus est ab in earum ratione eveniet soluta molestiae sed illo nostrum nemo debitis. Minus, quod totam aliquam ea asperiores fugit quaerat excepturi dolores ratione numquam consequatur id unde alias provident vero incidunt exercitationem similique consequuntur hic possimus? Fuga, eveniet quaerat inventore corporis laborum eligendi enim soluta obcaecati aliquid veritatis provident amet laudantium est quisquam dolore exercitationem modi? Distinctio, pariatur, ab velit praesentium vitae quidem consequatur deleniti recusandae odit officiis. Quidem, cupiditate.</p>
</div>


<?php die(""); ?>
<section class="section">
<div id="layout" class="container">

<aside class="menu">
  <p class="menu-label">
    General
  </p>
  <ul class="menu-list">
    <li><a>Dashboard</a></li>
    <li><a>Customers</a></li>
  </ul>
  <p class="menu-label">
    Administration
  </p>
  <ul class="menu-list">
    <li><a>Team Settings</a></li>
    <li>
      <a class="is-active">Manage Your Team</a>
      <ul>
        <li><a>Members</a></li>
        <li><a>Plugins</a></li>
        <li><a>Add a member</a></li>
      </ul>
    </li>
    <li><a>Invitations</a></li>
    <li><a>Cloud Storage Environment Settings</a></li>
    <li><a>Authentication</a></li>
  </ul>
  <p class="menu-label">
    Transactions
  </p>
  <ul class="menu-list">
    <li><a>Payments</a></li>
    <li><a>Transfers</a></li>
    <li><a>Balance</a></li>
  </ul>
</aside>






    <!-- Menu toggle -->
    <a href="#menu" id="menuLink" class="menu-link">
        <!-- Hamburger icon -->
        <span></span>
    </a>

    <div id="menu">
        <div class="pure-menu">
            <a class="pure-menu-heading" href="#">Company</a>

            <ul class="pure-menu-list">
                <li class="pure-menu-item"><a href="#addEmployer1" class="pure-menu-link">Bewerbungs-Vorlage hochladen</a></li>
                <li class="pure-menu-item"><a href="#addEmployer" class="pure-menu-link">Deine Daten</a></li>
                <li class="pure-menu-item"><a href="#addEmployer1" class="pure-menu-link">Arbeitgeber hinzuf&uuml;gen</a></li>
                <li class="pure-menu-item"><a href="#addEmployer1" class="pure-menu-link">Jetzt bewerben</a></li>
                <li class="pure-menu-item"><a href="#addEmployer1" class="pure-menu-link">Abgeschickte Bewerbungen anzeigen</a></li>

                                <li class="pure-menu-item" class="menu-item-divided pure-menu-selected">
                                                    <a href="#" class="pure-menu-link">Services</a>
                                                                    </li>

                <li class="pure-menu-item"><a href="#" class="pure-menu-link">Contact</a></li>
            </ul>
        </div>
    </div>

    <div id="main">
        <div class="header">
            <h1>Page Title</h1>
            <h2>A subtitle for your page goes here</h2>
        </div>
        <div class="content">
            <?php
                if(isset($_SESSION['user']) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] >= 1)
                {
            ?>
                    <div class="page" id="loggedInDiv" style="">
                        Eingeloggt als <?php echo $_SESSION['user']['name']; ?>
                        <br />
                        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
                    </div>
            <?php
                }
                else if(!isset($_POST['sbmShowRegisterForm']))
                {
            ?>
                    <div id="loginForm" class="page">
                        <form action="" method="post">
                            <table>
                                <tr>
                                    <td>Benutzername:</td>
                                    <td><input type="text" value="" name="txtName" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtPassword" /></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="sbmLoginForm" value="Einloggen" /></td>
                            </table>
                        </form>
                        <form action="" method="post">Neu?<input type="submit" value="Registrieren" name="sbmShowRegisterForm" /></form>
                    </div>
            <?php
                } else if(isset($_POST['sbmShowRegisterForm']))
                {
            ?>
                    <div id="registerForm" class="page">
                        <form action="" method="post">
                            <table>
                                <tr>
                                    <td>Benutzername:</td>
                                    <td><input type="text" value="" name="txtName" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtPassword" /></td>
                                </tr>
                                <tr>
                                    <td>Password wiederholen:</td>
                                    <td><input type="password" value="" name="txtPassworRepeated" /></td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" value="" name="txtEmail" /></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="sbmRegisterForm" value="Registrieren" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>

            <?php
                }
            ?>





            <?php
            ?>



            <!-- uploadApplicationTemplate()

            -->
            <?php
                function shouldSelectUploadJobApplicationTemplateTab()
                {
                    return isset($_POST['sbmUploadJobApplicationTemplate'])
                        || (
                               !isset($_POST['sbmSetUserValues'])
                            && !isset($_POST['sbmAddEmployer'])
                            && !isset($_POST['sbmApplyNowForReal'])
                            && !isset($_POST['sbmApplyNowForTest']));
                }
                function shouldSelectSetUserValuesTab()
                {
                    return isset($_POST['sbmSetUserValues']);
                }

                function shouldSelectAddEmployerTab()
                {
                    return isset($_POST['sbmAddEmployer']);
                }
                function shouldSelectApplyNowTab()
                {
                    return isset($_POST['sbmApplyNowForReal'])
                        || isset($_POST['sbmApplyNowForTest']);
                }
            ?>
                <div id="divUploadJobApplicationTemplate" class="page">
                        <h2>Bewerbungsvorlage hochladen</h2>
                        <form action="" method="post" enctype="multipart/form-data">
                        <table id="tblUploadJobApplicationTemplate">
                            <tr>
                                <td>Name der Vorlage</td>
                                <td><input type="text" name="txtJobApplicationTemplateName" /></td>
                            </tr>
                            <tr>
                                <td>Bewerbung als</td>
                                <td><input type="text" name="txtUserAppliesAs" />
                            </tr>
                            <tr>
                                <td>Email-Betreff</td>
                                <td><input type="text" name="txtEmailSubject" />
                            </tr>
                            <tr>
                                <td>Email-Body</td>
                                <td><textarea name="txtEmailBody" cols="100" rows="15"></textarea>
                            </tr>
                            <tr>
                                <td>Vorlage (*.odt oder *.docx)</td>
                                <td><input type="file" name="fileODT" id="fileODT" /></td>
                            </tr>
                            <tr>
                                <td>PDF Anhang</td>
                                <td><input type="file" name="fileAppendices[]" value="PDF Anhang" onChange="templateAppendixSelected(1);" /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" /></td>
                                <td />
                            </tr>
                        </table>
                        </form>
                    </div>

                <!-- setUserValues()
                -->
                <div id="divSetUserValues" class="page">
                    <form action="#" method="post">
                        <table>
                            <tr>
                                <td>Anrede</td>
                                <td><input type="text" name="txtSalutation" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['salutation'])) echo $_SESSION['user']['salutation']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Titel</td>
                                <td><input type="text" name="txtTitle" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['title'])) echo $_SESSION['user']['title']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Vorname</td>
                                <td><input type="text" name="txtFirstName" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['firstName'])) echo $_SESSION['user']['firstName']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Nachname</td>
                                <td><input type="text" name="txtLastName" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['lastName'])) echo $_SESSION['user']['lastName']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Stra&szlig;e</td>
                                <td><input type="text" name="txtStreet" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['street'])) echo $_SESSION['user']['street']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Postleitzahl</td>
                                <td><input type="text" name="txtPostCode" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['postCode'])) echo $_SESSION['user']['postCode']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Stadt</td>
                                <td><input type="text" name="txtCity" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['city'])) echo $_SESSION['user']['city']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input type="text" name="txtEmail" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['email'])) echo $_SESSION['user']['email']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Telefon mobil</td>
                                <td><input type="text" name="txtMobilePhone" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['mobilePhone'])) echo $_SESSION['user']['mobilePhone']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Telefon fest</td>
                                <td><input type="text" name="txtPhone" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['phone'])) echo $_SESSION['user']['phone']; ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmSetUserValues" value="Deine Werte &auml;ndern"/></td>
                            </tr>
                        </table>
                    </form>
                </div>



            <!-- addEmployer()

            -->
                <a name="addEmployer">hallo</a>
                <div id="divAddEmployer" name="addEmployer1" class="page">
                    <form action="" method="post">
                        <table>
                            <tr>
                                <td><input type="text" name="txtReadEmployerValuesFromWebSite" /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" /></td>
                            </tr>
                        </table>
                    </form>
                    <form action="" method="post">
                        <table>
                        <?php
                            $currentEmployer = ['$chefAnredeBriefkopf' => ''
                                               , '$geehrter' => ''
                                               , '$chefAnrede' => ''
                                               , '$chefVorname' => ''
                                               , '$chefNachname' => ''
                                               , '$firmaName' => ''
                                               , '$firmaStrasse' => ''
                                               , '$firmaPlz' => ''
                                               , '$firmaStadt' => ''
                                               , '$firmaTelefon' => ''
                                               , '$firmaEmail' => '' ];
                            if(isset($_POST['sbmReadEmployerValuesFromWebSite']))
                            {
                                $currentEmployer = readEmployerFromWebsite($_POST['txtReadEmployerValuesFromWebSite']);
                            }
                        ?>
                            <tr>
                                <td>Firma</td>
                                <td><input type="text" name="txtCompany" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaName']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Stra&szlig;e</td>
                                <td><input type="text" name="txtStreet" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStrasse']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Postleitzahl</td>
                                <td><input type="text" name="txtPostCode" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaPlz']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Stadt</td>
                                <td><input type="text" name="txtCity" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStadt']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Chef-Anrede</td>
                                <td><input type="text" name="txtSalutation" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefAnrede']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Chef-Titel</td>
                                <td><input type="text" name="txtTitle" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefTitel']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Chef-Vorname</td>
                                <td><input type="text" name="txtFirstName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefVorname']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Chef-Nachname</td>
                                <td><input type="text" name="txtLastName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefNachname']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><input type="text" name="txtEmail" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaEmail']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Telefon mobil</td>
                                <td><input type="text" name="txtMobilePhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaMobil']; ?>"/></td>
                            </tr>
                            <tr>
                                <td>Telefon fest</td>
                                <td><input type="text" name="txtPhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaTelefon']; ?>"/></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" /></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>
                </div>




                <div id="divApplyNow" class="page">
                    <table id="selectEmployerTable" class="table table-hover table-border table-sm">
                    <?php
                        $employers = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $employers = getEmployers($dbConn, $_SESSION['user']['id']);
                        }
                        if(count($employers) > 0)
                        {
                            echo '<tr>';
                            echo "\n";
                            foreach($employers[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($employers as $employer)
                            {
                                echo '<tr onClick="selectEmployerRowIndex(this)">';
                                echo "\n";
                                foreach($employer as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
                            }
                        }
                    ?>
                    </table>
                    <table id="selectTemplateTable" class="selectableTable">
                    <?php
                        $jobApplicationTemplates = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $jobApplicationTemplates = getJobApplicationTemplates($dbConn, $_SESSION['user']['id']);
                        }
                        if(count($employers) > 0 && count($jobApplicationTemplates) > 0)
                        {
                            echo '<tr>';
                            echo "\n";
                            foreach($jobApplicationTemplates[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($jobApplicationTemplates as $jobApplicationTemplate)
                            {
                                echo '<tr onClick="selectTemplateRowIndex(this)">';
                                echo "\n";
                                foreach($jobApplicationTemplate as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
                            }
                        }
                    ?>
                    </table>
                    <form action="" method="post">
                        <input type="hidden" id="hidEmployerIndex" name="hidEmployerIndex" value="" />
                        <input type="hidden" id="hidTemplateIndex" name="hidTemplateIndex" value="" />
                        <table>
                            <tr>
                                <td><input type="submit" name="sbmApplyNowForReal" value="Bewerbung abschicken" /><td>
                            <tr>
                                <td><input type="submit" name="sbmApplyNowForTest" value="Bewerbung zum Testen an mich selbst schicken" /></td>
                            </tr>
                        </table>
                    </form>
                </div>

            <!-- Sent applications

            -->

                <div id="divSentApplications" class="page">
                    <table class="table table-hover table-border">
                    <?php
                        $sentApplications = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $sentApplications = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0); //TODO Fix parameters
                        }
                        if(count($sentApplications) > 0)
                        {
                            echo '<tr>';
                            echo "\n";
                            foreach($sentApplications[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($sentApplications as $currentApplication)
                            {
                                echo '<tr>';
                                echo "\n";
                                foreach($currentApplication as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
                            }
                        }
                    ?>
                    </table>
                    <form action="" method="post" enctype="multipart/form-data">
                        <table>
                            <tr>
                                <td>Von Datum:</td>
                                <td><input type="date" value="<?php 
                                    $firstOfMonth = strtotime("-" . (date('d') - 1) . " days", time());
                                    echo date('Y-m-d', $firstOfMonth);
                                ?>" name="dateDownloadSentApplicationsFromDate" /></td>
                            </tr>
                            <tr>
                                <td>Bis Datum:</td>
                                <td><input type="date" value="<?php echo date('Y-m-d'); ?>" name="dateDownloadSentApplicationsToDate" /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmDownloadSentApplications" value="Liste als PDF downloaden" /></td>
                            </tr>
                        </table>
                    </form>
                </div>
        </div>


</section>
</body>
<script>

(function (window, document) {

    var layout   = document.getElementById('layout'),
        menu     = document.getElementById('menu'),
        menuLink = document.getElementById('menuLink'),
        content  = document.getElementById('main');

    function toggleClass(element, className) {
        var classes = element.className.split(/\s+/),
            length = classes.length,
            i = 0;

        for(; i < length; i++) {
            if (classes[i] === className) {
                classes.splice(i, 1);
                break;
            }
        }
        if (length === classes.length) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    }

    function toggleAll(e) {
        var active = 'active';

        e.preventDefault();
        toggleClass(layout, active);
        toggleClass(menu, active);
        toggleClass(menuLink, active);
    }

    menuLink.onclick = function (e) {
        toggleAll(e);
    };

    content.onclick = function(e) {
        if (menu.className.indexOf('active') !== -1) {
            toggleAll(e);
        }
    };

}(this, this.document));





</script>
<script>
    selectedEmployerRowIndex = 0;
    lastEmployerBackgroundColor = "white";
    selectedTemplateRowIndex = 0;
    lastTemplateBackgroundColor = "white";
    function selectTemplateRowIndex(row)
    {
        document.getElementById("selectTemplateTable").getElementsByTagName("tr")[selectedTemplateRowIndex].style.backgroundColor = lastTemplateBackgroundColor;
        selectedTemplateRowIndex = row.rowIndex;
        document.getElementById('hidTemplateIndex').value = row.rowIndex;
        lastTemplateBackgroundColor = row.style.backgroundColor;
        row.style.backgroundColor = 'lightgreen';
    }
    function selectEmployerRowIndex(row)
    {
        document.getElementById("selectEmployerTable").getElementsByTagName("tr")[selectedEmployerRowIndex].style.backgroundColor = lastEmployerBackgroundColor;
        selectedEmployerRowIndex = row.rowIndex;
        document.getElementById('hidEmployerIndex').value = row.rowIndex;
        lastEmployerBackgroundColor = row.style.backgroundColor;
        row.style.backgroundColor = 'lightgreen';
    }


    function templateAppendixSelected(fileNr)
    {
        lastTableRow = $("#tblUploadJobApplicationTemplate tr").eq(-1);
        fileAppendices = $("#tblUploadJobApplicationTemplate [name = 'fileAppendices[]'");
        if(fileAppendices.length == fileNr)
        {
            td1 = $("<td />").text("PDF Anhang");
            fileInput = $("<input></input>")
                    .attr("type", "file")
                    .attr("name", "fileAppendices[]")
                    .attr("value", "PDF Anhang")
                    .attr("onChange", "templateAppendixSelected(" + (fileNr + 1) + ");");
            td2 = $("<td />").append(fileInput);
            tr = $("<tr />");
            tr.append(td1);
            tr.append(td2);
            tr.insertBefore(lastTableRow);
        }


    }

</script>
</html>
















