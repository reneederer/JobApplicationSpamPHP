<?php
    use iio\libmergepdf\Merger;
    use iio\libmergepdf\Pages;

    use PHPMailer\PHPMailer\PHPMailer;


    require_once('../app/odtFunctions.php');
    require_once('../app/websiteFunctions.php');
    require_once('../app/dbFunctions.php');
    require_once('../app/helperFunctions.php');

    session_start();
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);




$kernel->terminate($request, $response);

/*
$str = '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:rpt="http://openoffice.org/2005/report" xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:officeooo="http://openoffice.org/2009/office" xmlns:tableooo="http://openoffice.org/2009/table" xmlns:drawooo="http://openoffice.org/2010/draw" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0" xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0" xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0" xmlns:css3t="http://www.w3.org/TR/css3-text/" office:version="1.2"><office:scripts/><office:font-face-decls><style:font-face style:name="Arial Unicode MS1" svg:font-family="&apos;Arial Unicode MS&apos;" style:font-family-generic="swiss"/><style:font-face style:name="Cambria" svg:font-family="Cambria" style:font-family-generic="roman" style:font-pitch="variable"/><style:font-face style:name="Liberation Serif" svg:font-family="&apos;Liberation Serif&apos;" style:font-family-generic="roman" style:font-pitch="variable"/><style:font-face style:name="Calibri" svg:font-family="Calibri" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="Arial Unicode MS" svg:font-family="&apos;Arial Unicode MS&apos;" style:font-family-generic="system" style:font-pitch="variable"/><style:font-face style:name="Microsoft YaHei" svg:font-family="&apos;Microsoft YaHei&apos;" style:font-family-generic="system" style:font-pitch="variable"/><style:font-face style:name="SimSun" svg:font-family="SimSun" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="Tabelle1" style:family="table"><style:table-properties style:width="17.664cm" table:align="right"/></style:style><style:style style:name="Tabelle1.A" style:family="table-column"><style:table-column-properties style:column-width="8.811cm"/></style:style><style:style style:name="Tabelle1.B" style:family="table-column"><style:table-column-properties style:column-width="8.853cm"/></style:style><style:style style:name="Tabelle1.A1" style:family="table-cell"><style:table-cell-properties fo:padding="0.097cm" fo:border="none"/></style:style><style:style style:name="P1" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.577cm" fo:line-height="0.429cm" fo:text-align="center" style:justify-single-word="false" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Calibri" fo:font-size="11pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="11pt" style:font-size-complex="11pt"/></style:style><style:style style:name="P2" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.577cm" fo:line-height="0.429cm" fo:text-align="center" style:justify-single-word="false" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Calibri" fo:font-size="10pt" fo:letter-spacing="-0.019cm" officeooo:rsid="0004a313" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="10pt"/></style:style><style:style style:name="P3" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="10pt" fo:letter-spacing="-0.026cm" officeooo:rsid="0005e9dc" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="10pt"/></style:style><style:style style:name="P4" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.026cm" officeooo:rsid="0005e9dc" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P5" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.026cm" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P6" style:family="paragraph" style:parent-style-name="Table_20_Contents"><style:text-properties style:font-name="Cambria" fo:font-size="12pt" style:font-size-asian="12pt"/></style:style><style:style style:name="P7" style:family="paragraph" style:parent-style-name="Table_20_Contents"><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="12pt"/></style:style><style:style style:name="P8" style:family="paragraph" style:parent-style-name="Table_20_Contents"><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:rsid="000ed7a5" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P9" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="center" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="32pt" fo:font-weight="bold" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="32pt" style:font-weight-asian="bold" style:font-size-complex="32pt" style:font-weight-complex="bold"/></style:style><style:style style:name="P10" style:family="paragraph" style:parent-style-name="Standard"><style:text-properties style:font-name="Cambria" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02"/></style:style><style:style style:name="P11" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="end" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02"/></style:style><style:style style:name="P12" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="center" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="18pt" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="18pt" style:font-size-complex="18pt"/></style:style><style:style style:name="P13" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="end" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="18pt" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="18pt" style:font-size-complex="18pt"/></style:style><style:style style:name="P14" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="center" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="24pt" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="24pt" style:font-size-complex="24pt"/></style:style><style:style style:name="P15" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="end" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="24pt" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="24pt" style:font-size-complex="24pt"/></style:style><style:style style:name="P16" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:text-align="end" style:justify-single-word="false"/><style:text-properties style:font-name="Cambria" fo:font-size="36pt" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="36pt" style:font-size-complex="36pt"/></style:style><style:style style:name="P17" style:family="paragraph" style:parent-style-name="Standard"><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P18" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.079cm" fo:line-height="0.646cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="00122f4a" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P19" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.079cm" fo:line-height="0.646cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P20" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="2.408cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.018cm" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P21" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="2.408cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P22" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="2.408cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P23" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.026cm" fo:font-weight="bold" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="13pt" style:font-weight-asian="bold" style:font-size-complex="13pt" style:font-weight-complex="bold"/></style:style><style:style style:name="P24" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.026cm" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P25" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.577cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P26" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="12.268cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P27" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.048cm" fo:line-height="0.353cm" fo:text-align="end" style:justify-single-word="false" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.021cm" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P28" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.048cm" fo:line-height="0.515cm" fo:text-indent="12.859cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P29" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="0.048cm" fo:line-height="0.423cm" fo:text-indent="12.859cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P30" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="6.357cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P31" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="6.357cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:rsid="0012e981" officeooo:paragraph-rsid="0012e981" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P32" style:family="paragraph" style:parent-style-name="Standard"><loext:graphic-properties draw:fill="none"/><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false" fo:background-color="transparent"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.021cm" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P33" style:family="paragraph" style:parent-style-name="Standard" style:master-page-name=""><loext:graphic-properties draw:fill="none"/><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false" style:page-number="auto" fo:background-color="transparent"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.021cm" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P34" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="11.257cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.018cm" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P35" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="11.257cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P36" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="11.257cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P37" style:family="paragraph" style:parent-style-name="Standard" style:master-page-name=""><loext:graphic-properties draw:fill="none"/><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="6.9cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false" style:page-number="auto" fo:background-color="transparent"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.032cm" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P38" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="12.876cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.032cm" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-size-complex="13pt"/></style:style><style:style style:name="P39" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="12.876cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="10pt" fo:letter-spacing="-0.032cm" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="10pt"/></style:style><style:style style:name="P40" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="1.729cm" fo:line-height="1.328cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="20pt" fo:letter-spacing="-0.071cm" fo:font-weight="bold" officeooo:paragraph-rsid="000d231f" style:font-size-asian="20pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P41" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="1.616cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.041cm" fo:font-weight="bold" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P42" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.034cm" fo:margin-right="1.616cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.041cm" fo:font-weight="bold" officeooo:rsid="000ed7a5" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P43" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="3.276cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.044cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P44" style:family="paragraph" style:parent-style-name="Standard"><loext:graphic-properties draw:fill="none"/><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.3cm" fo:line-height="0.513cm" fo:text-indent="0cm" style:auto-text-indent="false" fo:background-color="transparent"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.044cm" fo:font-weight="bold" officeooo:rsid="00151819" officeooo:paragraph-rsid="00151819" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P45" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="2.805cm" fo:line-height="0.513cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.025cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P46" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="3.076cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.039cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P47" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="3.214cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.035cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P48" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="1.189cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.019cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P49" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="2.044cm" fo:line-height="0.513cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.028cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P50" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="2.124cm" fo:line-height="0.616cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.044cm" fo:font-weight="bold" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P51" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="1.284cm" fo:line-height="0.616cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="000d231f" style:font-size-asian="12pt"/></style:style><style:style style:name="P52" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="1.284cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P53" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="3.134cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.035cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P54" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="2.152cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P55" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="4.172cm" fo:line-height="0.776cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P56" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="6.396cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.049cm" fo:font-weight="bold" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P57" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="0.753cm" fo:line-height="0.617cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.035cm" fo:font-weight="bold" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P58" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="2.843cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P59" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="4.129cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.035cm" fo:font-weight="bold" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P60" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="4.528cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.028cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P61" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="0cm" fo:line-height="0.617cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.039cm" fo:font-weight="bold" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P62" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="1.725cm" fo:line-height="0.617cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.039cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P63" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.048cm" fo:margin-right="0.049cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.021cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P64" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="1.76cm" fo:line-height="0.614cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P65" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="0.474cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P66" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="3.306cm" fo:line-height="0.616cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.034cm" officeooo:paragraph-rsid="00204268" style:font-size-asian="12pt"/></style:style><style:style style:name="P67" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="1.461cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.032cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P68" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.049cm" fo:margin-right="5.39cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.028cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P69" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.393cm" fo:margin-right="2.147cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.032cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P70" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.048cm" fo:margin-right="6.803cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.046cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P71" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.155cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P72" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="4.076cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false" fo:break-before="column"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P73" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="0cm" fo:line-height="0.515cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P74" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.445cm" fo:line-height="0.519cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.026cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P75" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="6.777cm" fo:line-height="0.773cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.034cm" officeooo:paragraph-rsid="000ed7a5" style:font-size-asian="12pt"/></style:style><style:style style:name="P76" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="0.002cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P77" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.975cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.034cm" fo:font-weight="bold" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P78" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0.975cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P79" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.653cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.03cm" fo:font-weight="bold" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P80" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.653cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.03cm" fo:font-weight="normal" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="normal"/></style:style><style:style style:name="P81" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.653cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P82" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.685cm" fo:line-height="0.587cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.03cm" fo:font-weight="bold" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P83" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.685cm" fo:line-height="0.587cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.03cm" fo:font-weight="normal" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="normal"/></style:style><style:style style:name="P84" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0.346cm" fo:margin-right="0.143cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.023cm" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P85" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.99cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.028cm" fo:font-weight="bold" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="P86" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.99cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.028cm" fo:font-weight="normal" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="normal"/></style:style><style:style style:name="P87" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.849cm" fo:line-height="0.591cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt"/></style:style><style:style style:name="P88" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="12.876cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.046cm" officeooo:rsid="000b9d02" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="12pt"/></style:style><style:style style:name="P89" style:family="paragraph" style:parent-style-name="Standard"><loext:graphic-properties draw:fill="none"/><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="3.701cm" fo:line-height="0.353cm" fo:text-indent="0cm" style:auto-text-indent="false" fo:background-color="transparent"/><style:text-properties style:font-name="Cambria" fo:font-size="13pt" fo:letter-spacing="-0.03cm" fo:font-weight="bold" officeooo:paragraph-rsid="000b9d02" style:font-size-asian="13pt" style:font-weight-asian="bold" style:font-size-complex="13pt"/></style:style><style:style style:name="P90" style:family="paragraph" style:parent-style-name="Standard"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="1.998cm" fo:line-height="0.423cm" fo:text-indent="0cm" style:auto-text-indent="false"/><style:text-properties style:font-name="Cambria" fo:font-size="12pt" fo:letter-spacing="-0.046cm" fo:font-weight="bold" officeooo:paragraph-rsid="0010375c" style:font-size-asian="12pt" style:font-weight-asian="bold"/></style:style><style:style style:name="T1" style:family="text"><style:text-properties fo:letter-spacing="-0.019cm"/></style:style><style:style style:name="T2" style:family="text"><style:text-properties fo:letter-spacing="-0.019cm" officeooo:rsid="00151819"/></style:style><style:style style:name="T3" style:family="text"><style:text-properties fo:letter-spacing="-0.019cm" officeooo:rsid="0004a313"/></style:style><style:style style:name="T4" style:family="text"><style:text-properties officeooo:rsid="000897fd"/></style:style><style:style style:name="T5" style:family="text"><style:text-properties officeooo:rsid="000d231f"/></style:style><style:style style:name="T6" style:family="text"><style:text-properties officeooo:rsid="000ed7a5"/></style:style><style:style style:name="T7" style:family="text"><style:text-properties fo:letter-spacing="-0.03cm" fo:font-weight="bold" style:font-weight-asian="bold"/></style:style><style:style style:name="T8" style:family="text"><style:text-properties fo:letter-spacing="-0.03cm" fo:font-weight="normal" style:font-weight-asian="normal"/></style:style><style:style style:name="T9" style:family="text"><style:text-properties fo:letter-spacing="-0.034cm" fo:font-weight="normal" style:font-weight-asian="normal"/></style:style><style:style style:name="T10" style:family="text"><style:text-properties fo:letter-spacing="-0.034cm" fo:font-weight="normal" officeooo:rsid="0010375c" style:font-weight-asian="normal"/></style:style><style:style style:name="T11" style:family="text"><style:text-properties fo:letter-spacing="-0.025cm" fo:font-weight="bold" style:font-weight-asian="bold"/></style:style><style:style style:name="T12" style:family="text"><style:text-properties fo:letter-spacing="-0.025cm" fo:font-weight="normal" style:font-weight-asian="normal"/></style:style><style:style style:name="T13" style:family="text"><style:text-properties fo:letter-spacing="-0.018cm"/></style:style><style:style style:name="T14" style:family="text"><style:text-properties fo:letter-spacing="-0.018cm" officeooo:rsid="0015377b"/></style:style><style:style style:name="T15" style:family="text"><style:text-properties fo:letter-spacing="-0.011cm"/></style:style><style:style style:name="T16" style:family="text"><style:text-properties fo:letter-spacing="-0.016cm"/></style:style><style:style style:name="T17" style:family="text"><style:text-properties fo:letter-spacing="-0.012cm"/></style:style><style:style style:name="T18" style:family="text"><style:text-properties fo:letter-spacing="-0.014cm"/></style:style><style:style style:name="T19" style:family="text"><style:text-properties fo:letter-spacing="-0.007cm"/></style:style><style:style style:name="T20" style:family="text"><style:text-properties fo:letter-spacing="-0.032cm"/></style:style><style:style style:name="T21" style:family="text"><style:text-properties fo:letter-spacing="-0.032cm" officeooo:rsid="00171e88"/></style:style><style:style style:name="T22" style:family="text"><style:text-properties fo:letter-spacing="-0.032cm" officeooo:rsid="001be523"/></style:style><style:style style:name="T23" style:family="text"><style:text-properties officeooo:rsid="00151819"/></style:style><style:style style:name="T24" style:family="text"><style:text-properties officeooo:rsid="001be523"/></style:style><style:style style:name="T25" style:family="text"><style:text-properties officeooo:rsid="0020ba29"/></style:style><style:style style:name="T26" style:family="text"><style:text-properties officeooo:rsid="0027d0cb"/></style:style><style:style style:name="fr1" style:family="graphic" style:parent-style-name="Graphics"><style:graphic-properties style:vertical-pos="from-top" style:vertical-rel="paragraph" style:horizontal-pos="from-left" style:horizontal-rel="paragraph" style:mirror="none" fo:clip="rect(0cm, 0cm, 0cm, 0cm)" draw:luminance="0%" draw:contrast="0%" draw:red="0%" draw:green="0%" draw:blue="0%" draw:gamma="100%" draw:color-inversion="false" draw:image-opacity="100%" draw:color-mode="standard"/></style:style></office:automatic-styles><office:body><office:text text:use-soft-page-breaks="true"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/><text:sequence-decls><text:sequence-decl text:display-outline-level="0" text:name="Illustration"/><text:sequence-decl text:display-outline-level="0" text:name="Table"/><text:sequence-decl text:display-outline-level="0" text:name="Text"/><text:sequence-decl text:display-outline-level="0" text:name="Drawing"/></text:sequence-decls><text:p text:style-name="P9">BEWERBUNG </text:p><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P12">als </text:p><text:p text:style-name="P12"/><text:p text:style-name="P14">Fachinformatiker für Anwendungsentwicklung </text:p><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P10"><draw:frame draw:style-name="fr1" draw:name="Bild1" text:anchor-type="paragraph" svg:x="0.617cm" svg:y="0.474cm" svg:width="5.57cm" svg:height="7.482cm" draw:z-index="0"><draw:image xlink:href="Pictures/100000000000010700000161D1569820F3E57FEC.jpg" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/></draw:frame></text:p><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P13">$meinTitel $meinVorname $meinNachname</text:p><text:p text:style-name="P13">$meineStrasse</text:p><text:p text:style-name="P13">$meinePlz $meineStadt</text:p><text:p text:style-name="P13">$meineMobilnr</text:p><text:p text:style-name="P13">$meineEmail</text:p><text:p text:style-name="P10"/><text:p text:style-name="P10"><text:s/></text:p><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P10"/><text:p text:style-name="P11"/><text:p text:style-name="P16">Meine Stärken </text:p><text:p text:style-name="P11"/><text:p text:style-name="P11"/><text:p text:style-name="P11"/><text:p text:style-name="P15">Programmierbegeistert </text:p><text:p text:style-name="P15">Lernfreudig </text:p><text:p text:style-name="P15">Engagiert </text:p><text:p text:style-name="P11"/><text:p text:style-name="P3"><text:soft-page-break/></text:p><text:p text:style-name="P4"/><text:p text:style-name="P4"/><text:p text:style-name="P5">$chefAnredeBriefkopf</text:p><text:p text:style-name="P23">$chefTitel $chefVorname $chefNachname</text:p><text:p text:style-name="P24">$<text:span text:style-name="T26">f</text:span>irmaName</text:p><text:p text:style-name="P24">$firmaS<text:span text:style-name="T26">t</text:span>rasse</text:p><text:p text:style-name="P24">$firmaPlz $<text:span text:style-name="T26">firmaStadt</text:span></text:p><text:p text:style-name="P25"/><text:p text:style-name="P26"/><text:p text:style-name="P17"/><text:p text:style-name="P27">$meineStadt, $datumHeute</text:p><text:p text:style-name="P28"/><text:p text:style-name="P29"/><text:p text:style-name="P89">Bewerbung als Fachinformatiker für Anwendungsentwicklung </text:p><text:p text:style-name="P89"/><text:p text:style-name="P30"/><text:p text:style-name="P31"/><text:p text:style-name="P33">Sehr <text:span text:style-name="T23">$geehrter $chefTitel $chefAnrede $chefNachname,</text:span></text:p><text:p text:style-name="P32"/><text:p text:style-name="P19"><text:span text:style-name="T13">Als mir vor </text:span><text:span text:style-name="T14">3</text:span><text:span text:style-name="T13"> Jahren (nachdem mein damaliger Arbeitgeber die Firma geschlossen hatte) angeboten wurde, eine </text:span><text:span text:style-name="T15">Umschulung zu machen, stand für mich sofort fest, dass ich Anwendungsentwickler werden wollte. Ich hatte </text:span><text:span text:style-name="T13">schon in meiner Jugend Spaß am Programmieren. Angefangen hattee ich damals, Mitte der 90er, indem ich mir </text:span><text:span text:style-name="T16">HTML- und JavaScript-Kenntnisse aneignete, indem ich mir den Quelltext von Internetseiten anzeigen ließ und </text:span><text:span text:style-name="T17">ihn zu verstehen versuchte. Als ich in der 9. Klasse war, bot mein damaliger Mathematiklehrer den Wahlkurs </text:span><text:span text:style-name="T18">Java an. Ich meldete mich zusammen mit einem Freund an und war von den neuen Möglichkeiten begeistert. </text:span><text:span text:style-name="T19">Seitdem habe ich immer wieder programmiert, hin und wieder fast wie besessen, manchmal auch einige </text:span><text:span text:style-name="T16">Monate überhaupt nicht. Mein größtes Projekt war ein Programm, in das man Umfragedaten eintragen und auf </text:span><text:span text:style-name="T17">einfache Weise Korrelationen finden konnte. Meine Schwester und zwei ihrer Freundinnen nutzten es für ihre </text:span><text:span text:style-name="T20">Staatsexamen. </text:span><text:span text:style-name="T21">Während meiner Umschulung arbeitete ich mit C++ und C# (und COBOL), außerdem eignete ich mir selbstständig einige Kenntnisse in funktionaler Programmierung an. Ich möchte meine Kenntnisse </text:span><text:span text:style-name="T22">gerne</text:span><text:span text:style-name="T21"> noch viel mehr erweitern und <text:s/>dadurch bei Ihnen zum Erfolg <text:s/>Ihres Unternehmens beitragen.</text:span></text:p><text:p text:style-name="P18"/><text:p text:style-name="P20"><text:span text:style-name="T24">Ü</text:span>ber eine Einladung zu einem persönlichen Vorstellungsgespräch würde ich mich sehr freuen. </text:p><text:p text:style-name="P21"/><text:p text:style-name="P22"/><text:p text:style-name="P34">Mit freundlichen Grüßen </text:p><text:p text:style-name="P35"/><text:p text:style-name="P35"/><text:p text:style-name="P36"/><text:p text:style-name="P37">$meinTitel $meinVorname $meinNachname</text:p><text:p text:style-name="P38"/><text:p text:style-name="P38"><text:soft-page-break/></text:p><text:p text:style-name="P38"/><text:p text:style-name="P38"/><text:p text:style-name="P39"/><table:table table:name="Tabelle1" table:style-name="Tabelle1"><table:table-column table:style-name="Tabelle1.A"/><table:table-column table:style-name="Tabelle1.B"/><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P40">Lebenslauf </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P41">Persönliche Daten: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P43">Name: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P44">$meinTitel $meinVorname $meinNachname</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P45">Anschrift: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meineStrasse</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P46"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meinePlz $meineStadt</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P46"><text:span text:style-name="T5">Mobil</text:span>: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meineMobilnr</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P47">E-Mail: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meineEmail</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P48">Geburtsdatum, -ort: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meinGeburtsdatum $meinGeburtsort</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P49">Familienstand: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meinFamilienstand</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P50">Schulbildung: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P51">09/2014 - 07/2016 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P53">PHOENIX Group IT GmbH, Fürth </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P54">Umschulung zum Fachinformatiker für </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P55">Anwendungsentwicklung </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P56">Abschluss: </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P57">Fachinformatiker für Anwendungsentwicklung </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P52">09/1992 - 08/2000 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P58">Labenwolf-Gymnasium, Nürnberg </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P59">Abschluss: Mittlere Reife </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P52">09/1988 - 08/1992 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P60">Grundschule Nürnberg </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P61">Kenntnisse und Interessen: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P62">EDV-Kenntnisse: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P63">Office-Standardprogramme, Windows, Linux (<text:span text:style-name="T25">Ubuntu</text:span>), </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P64">MS Visual Studio, Vim, <text:span text:style-name="T4">VirtualBox</text:span></text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P65">Programmierkenntnisse: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P66">C#, <text:span text:style-name="T6">F#,</text:span> <text:s/>C++, <text:span text:style-name="T6">Java <text:s/>JavaScript, PHP, HTML</text:span></text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P8">SQL, XML</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P67">Sprachkenntnisse: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P68">deutsch, englisch </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P69">Führerschein: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P70">Klasse B</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><text:soft-page-break/><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P90"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P71"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P90"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P71"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P90"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P71"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P90">Hobbies: </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P71">Programmieren, Schach, Klavier spielen, Sport </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/><text:p text:style-name="P6"/><text:p text:style-name="P6"/><text:p text:style-name="P6"/><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P42">Berufspraxis : </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P72"><text:span text:style-name="T7">Zeitungsausträger</text:span><text:span text:style-name="T8"> (teilzeit) </text:span></text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P73">03/2011 - 10/2014</text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P74">Nordbayerische Anzeigenverwaltung GmbH, </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P75">Nürnberg </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P76">11/2008 - 09/2014 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P77">Postzusteller</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P78"><text:span text:style-name="T9">NordbayernPost GmbH &amp; Co. KG, </text:span><text:span text:style-name="T10">Nürnberg</text:span></text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P76">04/2007 - 12/2007 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P79">Postsortierer</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P80">Deutsche Post AG, Nürnberg </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P76">07/2003 - 03/2007 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P82">Postzusteller</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P83">Deutsche Post AG, Nürnberg</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P84">03/2002 - 07/2002 </text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P85">Marktforscher</text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P86">Krämer GmbH, Nürnberg </text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P84">09/2001 - 02/2002</text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P87"><text:span text:style-name="T11">Produktionshelfer</text:span><text:span text:style-name="T12"> über Zeitarbeitsfirmen </text:span></text:p></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P7">$meineStadt, $datumHeute</text:p></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row><table:table-row><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P6"/></table:table-cell><table:table-cell table:style-name="Tabelle1.A1" office:value-type="string"><text:p text:style-name="P81"/></table:table-cell></table:table-row></table:table><text:p text:style-name="P88"/></office:text></office:body></office:document-content>';
$str = '<text:p>$<text:span text:style-name="T26">f</text:span>irmaName</text:p><text:p text:style-name="P24">$firmaS<text:span text:style-name="T26">t</text:span>rasse</text:p><text:p text:style-name="P24">$firmaPlz $<text:span text:style-name="T26">firmaStadt</text:span></text:p><text:p text:style-name="P25"/><text:p text:style-name="P26"/><text:p text:style-name="P17"/><text:p text:style-name="P27">';
//$str = '$<text:span text:style-name="T26">f</text:span>';
$dict = [ "\$firmaStrasse" => 'Die Strasse der Firma', "\$firmaName" => "FirmaName", "\$firmaStadt" => "FirmaStadt", "\$firmaPlz" => "11111" ];
//$dict = [ "\$f" => "FirmaStadt" ];
echo htmlspecialchars($str);
echo "<br><br><br>";
$str = replaceAllInStringIgnoreTags($str, $dict);

echo htmlspecialchars($str);

die("");
 */
    echo Config::get('rene.unoconv');
    die("");

    $dbConn = new PDO('mysql:host=localhost;dbname=' . env('DB_DATABASE'), env('DB_USERNAME'), env('DB_PASSWORD'));
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
            $email->Username = Config::get('mail.username');
            $email->Password = Config::get('mail.password');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<script src="js/jquery-3.2.1.slim.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css">
<title>Meine Bewerbung</title>
</head>
<body>
<?php
?>

<!-- create loginDiv

-->
<?php
    if(isset($_SESSION['user']) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] >= 1)
    {
?>
        <div id="loggedInDiv" style="position:absolute;top:20;right:20;">
            Eingeloggt als
<?php
        echo $_SESSION['user']['name'];
?>
        <br />
        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
        </div>
<?php
    }
    else if(!isset($_POST['sbmShowRegisterForm']))
    {
?>
        <div id="loginForm">
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
            Neu?<form action="" method="post"><input type="submit" value="Registrieren" name="sbmShowRegisterForm" /></form>
        </div>
<?php
    } else if(isset($_POST['sbmShowRegisterForm']))
    {
?>
        <div id="registerForm">
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
?>

<?php
    }
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
<div class="container">
    <ul class="nav nav-pills">
        <li <?php if(shouldSelectUploadJobApplicationTemplateTab()){ echo 'class="active"'; } ?>>
            <a data-toggle="pill" href="#divUploadJobApplicationTemplate">Bewerbungsvorlage hochladen</a>
        </li>
        <li <?php if(shouldSelectSetUserValuesTab()) { echo 'class="active"'; } ?>>
            <a data-toggle="pill" href="#divSetUserValues">Benutzer bearbeiten</a>
        </li>
        <li <?php if(shouldSelectAddEmployerTab()) { echo 'class="active"'; } ?>>
            <a data-toggle="pill" href="#divAddEmployer">Arbeitgeber hinzuf&uuml;gen</a>
        </li>
        <li <?php if(shouldSelectApplyNowTab()) { echo 'class="active"'; } ?>>
            <a data-toggle="pill" href="#divApplyNow">Jetzt bewerben</a>
        </li>
        <li>
            <a data-toggle="pill" href="#divSentApplications">Abgeschickte Bewerbungen</a>
        </li>
    </ul>
    <div class="tab-content">
    <div id="divUploadJobApplicationTemplate" class="tab-pane fade <?php if(shouldSelectUploadJobApplicationTemplateTab()) { echo ' in active'; } ?>">
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
    <div id="divSetUserValues" class="tab-pane fade <?php if(shouldSelectSetUserValuesTab()) { echo ' in active'; } ?>">
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
    <div id="divAddEmployer" class="tab-pane fade <?php if(shouldSelectAddEmployerTab()) { echo ' in active'; } ?>">
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




    <div id="divApplyNow" class="tab-pane fade<?php if(shouldSelectApplyNowTab()) { echo ' in active'; } ?>">
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

    <div id="divSentApplications" class="tab-pane fade<?php if(false) { echo ' in active'; } ?>">
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
<!--
<form action="" method="post">
<input type="submit" name="sbmDownloadPDF" value="PDF downloaden" />
</form>

-->


<!-- sentApplications
-->

<!--
<div id="sentApplications">
<table>
<php
    if(isset($_SESSION['user']['id']))
    {
        $sentApplications = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0);
        foreach($sentApplications as $application)
        {
            echo "<tr>";
                echo "<td>";
                    echo $application['date'];
                echo "</td>";
                echo "<td>";
                    echo $application['companyName'];
                echo "</td>";
            echo "</tr>";
        }
    }
?>
</table>
</div>
-->
</div>


</body>
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
















