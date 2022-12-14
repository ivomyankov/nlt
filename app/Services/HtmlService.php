<?php

namespace App\Services;

use App\Http\Traits\CacheTrait;
use DOMDocument;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManagerStatic as Image;

class HtmlService 
{
    use CacheTrait;

    //private $config;

    public function __construct()
    {
        //$this->config = Cache::get('config');
    }
    
    public function handle($html_content) {
       
        dd('HtmlServiceClass');
    }

    public function escapeSpecialCharacters($html_content) {
        $html_content = htmlspecialchars($html_content, ENT_QUOTES);

        return $html_content;
    }

    public function imagesHandler($html_content) {
        $i=0;
        $images = array();
        $config = Cache::get('config');

        if (Str::of($config['server'])->contains('mediaservices')){
            $this->saveMxpLogo($config);
            if (Str::of($config['server'])->contains('Tarox')){
                $this->saveTaroxLogos($config);
            }
        }
        
        preg_match_all("{<img\\s*(.*?)src=('.*?'|\".*?\"|[^\\s]+)(.*?)\\s*/?>}ims", $html_content, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            //dd($val[2]);

            // removed " from beginging and end of url
            $link = substr($val[2],1,-1); 
           
            $path_parts = pathinfo($link);
            
            if (!array_key_exists($link, $images)) { 
                //dump($path_parts);
                $i++;
                $images[$link] = $path_parts['basename'];
                // if image url is unknown format, will replace name and extention with number and jpg => $i.jpg
                if( isset($path_parts['extension']) && in_array($path_parts['extension'] , array('jpg', 'jpeg', 'png', 'gif', 'wepp')) ) {
                    //dump($path_parts['dirname']. '/' .$path_parts['basename']);
                    $this->saveImage($link, $path_parts['basename'], $config);
                    // if image has no url in link
                    if ($path_parts['dirname'] == '.'){
                        $html_content = Str::replace($path_parts['basename'], $config['server']. $config['folder']. '/' . $path_parts['basename'], $html_content);
                    } else {
                        $html_content = Str::replace($path_parts['dirname']. '/' .$path_parts['basename'], $config['server']. $config['folder']. '/' . $path_parts['basename'], $html_content);
                    }
                    
                } else {
                    //dump($path_parts['dirname']. '/' .$path_parts['basename']);
                    $this->saveImage($link, $i.'.jpg', $config);  
                    // if image has no url in link
                    if ($path_parts['dirname'] == '.'){
                        $html_content = Str::replace($path_parts['basename'], $config['server']. $config['folder']. '/' . $i .'.jpg' , $html_content);
                    } else {
                        $html_content = Str::replace($path_parts['dirname']. '/' .$path_parts['basename'], $config['server']. $config['folder']. '/' . $i .'.jpg' , $html_content);  
                    }              
                }                             
            }      
        }
        
        //dump($html_content); 
        return $html_content;
    }

    private function saveImage($image, $baseName, $config) { //$baseName ='https://d39vk9zh1kj304.cloudfront.net/assets/files/Media/iQ7dAkGifrALjh8Sw/medium/Omen%20by%20HP%20Laptop.png';
        if (Str::of($image)->contains(' ')){
            $image = Str::replace(' ', '%20', $image);
        }
        try {
            Image::make($image)->save($config['storage_path'] . $config['folder'] . '/' . $baseName);
        }
        catch(Exception $e) {
            if (!file_exists($config['storage_path'] . $config['folder'] . '/' . $baseName)) {
                dd($e->getMessage(), $image, $baseName);
            }            
        }
    }

    private function saveTaroxLogos($config) { 
        Image::make(storage_path('app/public/images/f.png'))->save($config['storage_path'] . $config['folder']  . '/f.png');
        Image::make(storage_path('app/public/images/t.png'))->save($config['storage_path'] . $config['folder']  . '/t.png');
        Image::make(storage_path('app/public/images/i.png'))->save($config['storage_path'] . $config['folder']  . '/i.png');
        Image::make(storage_path('app/public/images/x.png'))->save($config['storage_path'] . $config['folder']  . '/x.png');
        Image::make(storage_path('app/public/images/l.png'))->save($config['storage_path'] . $config['folder']  . '/l.png');
    }

    private function saveMxpLogo($config) { 
        Image::make(storage_path('app/public/images/logo_mxp.png'))->save($config['storage_path'] . $config['folder']  . '/logo_mxp.png');
    }

    public function replaceAsciiInLinks($html_content) {
        $pattern = '/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i';

        if($links_found = preg_match_all($pattern, $html_content, $links)) {
            
            foreach ($links[0] as $key => $link) {
                if (Str::of($link)->contains('www.w3.org')){
                    unset($links[0][$key]);
                    continue;
                }
                $link_updated = Str::replace('http:', 'https:', $link);
                $html_content = Str::replace($link, $link_updated, $html_content);
            }
            
            return $html_content;
            //echo "FOUND ".$links_found." LINKS:\n";
            //dd($links, $html_content);
        }
    }

    public function fixCommonIssues($html_content) {
        $config = Cache::get('config');

        $html_content = Str::replace('&amp;', '&', $html_content);
        $html_content = Str::replace(';;', ';', $html_content);
        $html_content = Str::replace('charset=ISO-8859-1', 'charset=utf-8', $html_content);
        $html_content = Str::replace(';;', ';', $html_content);
        
        if ($config['company'] == 'Tarox') {
            $html_content = Str::replace('service@tripicchio', 'info@tarox', $html_content);
            $html_content = Str::replace('Sehr geehrter Herr Mustermann,', 'Lieber Leser,', $html_content);
            $html_content = Str::replace('Lieber Herr Mustermann,', 'Lieber Leser,', $html_content);

            $old = '<td align="left" class="SWYN" style="padding:27px 10px 0px 10px;"><a href="https://www.facebook.com/TAROXAG/"><img alt="" border="0" src="http://port-neo.scnem.com/art_resource.php?sid=nq1.je79nd" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 33px !important;" width="33" /></a> <a href="https://www.xing.com/company/taroxag"><img alt="" border="0" src="http://port-neo.scnem.com/art_resource.php?sid=nq2.2omn1rp" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 26px !important;" width="26" /></a> <a href="https://twitter.com/taroxag"><img alt="" border="0" src="http://port-neo.scnem.com/art_resource.php?sid=nq0.2e09bqd" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 21px !important;" width="21" /></a> <a href="https://www.linkedin.com/company/tarox-ag/"><img alt="LinkedIn" src="http://port-neo.scnem.com/art_resource.php?sid=95nd.2oooc2" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 32px !important;" width="32" /></a> <a href="https://www.instagram.com/taroxag/"><img alt="Instagram" src="http://port-neo.scnem.com/art_resource.php?sid=95ne.2894hk4" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 34px !important;" width="34" /></a></td>';
            $old = $this->replaceAsciiInLinks($old);
            $new = '<td align="left" class="SWYN" style="padding:27px 10px 0px 10px;"><a href="https://www.facebook.com/TAROXAG/"><img alt="" border="0" src="https://newsletter.mediaservices.biz/242/2022-11-17/img/scn30745.png" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 33px !important;" width="33" /></a> <a href="https://www.xing.com/company/taroxag"><img alt="" border="0" src="https://newsletter.mediaservices.biz/242/2022-11-17/img/scn30746.png" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 26px !important;" width="26" /></a> <a href="https://twitter.com/taroxag"><img alt="" border="0" src="https://newsletter.mediaservices.biz/242/2022-11-17/img/scn30744.png" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 21px !important;" width="21" /></a> <a href="https://www.linkedin.com/company/tarox-ag/"><img alt="LinkedIn" src="https://newsletter.mediaservices.biz/242/2022-11-17/img/scn427225.png" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 32px !important;" width="32" /></a> <a href="https://www.instagram.com/taroxag/"><img alt="Instagram" src="https://newsletter.mediaservices.biz/242/2022-11-17/img/scn427226.png" style="font-family: Arial, Helvetica\ Neue, Helvetica, sans-serif; font-size: 16px !important; width: 34px !important;" width="34" /></a></td>';
            $html_content = Str::replace($old, $new, $html_content);
                //dd(Str::contains($html_content, $old), $config['base_url'].'/storage/images/' .'f.png');
            // find last occurrence and add id="rmv"
            $pos = strrpos($html_content, '<td align="center" bgcolor="#EDEDED">');
            if($pos !== false)
            {
                $html_content = substr_replace($html_content, '<td id="rmv" align="center" bgcolor="#EDEDED; display:none;">', $pos, strlen('<td align="center" bgcolor="#EDEDED">'));
                $html_content = preg_replace('#<td id="rmv" (.*?)</body>#is', '</tbody></table></body>', $html_content);
            }
        } else if ($config['company'] == 'Jarltech') {
            $html_content = Str::replace('https://www.jarltech.com/2007/jarltech.php?language=at_en&reqd=at&link=5', 'https://bit.ly/3UaNmhr', $html_content);
            $html_content = Str::replace('width="1200"', 'width="800"', $html_content);
        } else if ($config['company'] == 'CDS') {
            str_replace("'", "\'", $html_content);
            //dd($html_content);
            $html_content = Str::replace("&#39;HCo Gotham&#39;, ", '', $html_content);
            //$html_content = $this->escapeSpecialCharacters($html_content);
            //$html_content = Str::replace("HCo Gotham", '.', $html_content);
            $html_content = Str::replace('<div style="margin:0px auto;max-width:600px;">                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">          <tbody>            <tr>              <td style="direction:ltr;font-size:0px;padding:0;text-align:center;">                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->                  <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">              <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">        <tbody>                        <tr>                <td align="left" style="font-size:0px;padding:10px 25px;padding-top:10px;padding-right:20px;padding-bottom:30px;padding-left:20px;word-break:break-word;">                        <div style="font-family:Calibri, Arial;font-size:13px;font-weight:500;line-height:18px;text-align:left;color:#00354c;">Sie erhalten diesen E-Mail Newsletter auf eigenen Wunsch. Wenn Sie diese E-Mail in Zukunft\t\t\tnicht mehr empfangen m??chten, k??nnen Sie sich hier\t\t\t<a style="color: #00b8f1; text-decoration: underline" href="https://026ui.mjt.lu/unsub2?m=AUsAAAOP9yEAAAACyHgAAAEwtpYAAAAAIewAACAwABvbSgBjgK4y_2rPWygXTeeyK3cedrY9GgAascQ&b=3049c969&e=9c8d1723&x=EJRP1XtKsJQC2PEnmbXAYaaj6lczCyS3arXENyTbWmk&hl=DE?utm_medium=email">abmelden</a>\t\t\t<br/>\t\t\tEmpf??nger: sven_bent@cds-it-systeme.de<br/>\t\t\tMeine Daten\t\t\t<a style="color: #00b8f1; text-decoration: underline" href="https://newsletter.cds.blue/form/preferences?email=%5B%5BEMAIL_TO%5D%5D&utm_medium=email">??ndern</a></div>                    </td>              </tr>                    </tbody>      </table>          </div>', '', $html_content);
        }
           
        $html_content = Str::replace('><', '>
        <', $html_content);
        

        

        //dd($html_content);
        return $html_content;        
    }


    public function checkHtmlStructure($html_content) {
        $config = Cache::get('config');

        if (!Str::of($html_content)->contains('<body')){
            $html_content = "<body>$html_content</body>";
        }
        if (!Str::of($html_content)->contains('<head>')){
            $head = '';
            if (!Str::of($html_content)->contains('Content-Type')){
                $head = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            }
            if (!Str::of($html_content)->contains('viewport')){
                $head = $head.'<meta name="viewport" content="width=device-width, initial-scale=1" />';
            }
            if (!Str::of($html_content)->contains('<title>')){
                $head = $head.'<title>'.$config['company'].'</title>';
            } 

            $head = "<head>$head</head>";
        } else {
            $head = '';
            // if title is empty fill with company name
            if (Str::of($html_content)->contains('<title></title>')){
                $html_content = Str::replace('<title></title>', '<title>'.$config['company'].'</title>', $html_content);
            } 
        }
        if (!Str::of($html_content)->contains('<html')){
            $html_content = '<html xmlns="http://www.w3.org/1999/xhtml">' . $head . $html_content . '</html>';
        }
        if (!Str::of($html_content)->contains('<!DOCTYPE html')){
            $html_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . $html_content;
        }

        //dd($html_content);
        return $html_content;
    }

    public function detectLanguage($html_content) { 
        if (Str::contains($html_content, ['??', '??', '??'])){
            $this->addToCache(['language' => 'de']);
        } else {
            $this->addToCache(['language' => 'en']);
        }
      
        return;
    }

    public function reverseColor($color) {
        //dd(hexdec($color));

        list($r, $g, $b) = sscanf('#' . $color, "#%02x%02x%02x");
        if (($r+$g+$b) < 300) {
            $color = 'fff';
        } else {
            $color = '000';
        }

        return $color;
    }

    public function findBackgroundColor($header, $body) {
        //dd($body, $header);
        $color = false;
        $class = false;
        $id    = false;

        // find background color in body tag or class
        $pattern = '/(background-color)\:\#?\w+\;/i';
        if (preg_match($pattern, $body, $matches)) {
            $color = Str::between($matches[0], 'background-color:#', ';');
        } else {
            $body_parts = Str::of($body)->explode(' ');
            //dd($body_parts);
            foreach ($body_parts as $part) {
                if (Str::of($part)->startsWith('bgcolor')) {
                    $color = Str::between($part, 'bgcolor="#', '"');
                }
                if (Str::of($part)->startsWith('class')) {
                    $class = Str::between($part, 'class="', '"');
                }
                if (Str::of($part)->startsWith('id')) {
                    $id = Str::between($part, 'id="', '"');
                }
            }
        }

        // if still color not found but there is stylesheet then surch in style
        if (!$color && ($class || $id)) {
            //dd('To Do');
        }

        // if still color not found use black
        if (!$color) {
            $text_color = '000';
        } else {
            $text_color = $this->reverseColor($color);
        }        
        
        $this->addToCache(['text_color' => $text_color]);
        
        return $text_color;
    }

    public function addHeader($html_content) {
        $config = Cache::get('config');
        $de = 'Sollte diese E-Mail nicht korrekt dargestellt werden, klicken Sie bitte hier.';
        $en = 'If having problems viewing this announcement please click here.';

        if ($config['language'] == 'de') {
            $lang = $de;
        } else {
            $lang = $en;
        }
        

        try {
            /* split the string contained in $html_content in three parts: 
            * everything before the <body> tag
            * the body tag with any attributes in it
            * everything following the body tag
            */
            $parts = preg_split('/(<body.*?>)/i', $html_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
          
            $text_color = $this->findBackgroundColor($parts[0], $parts[1]);
            //dd($text_color);

            //dd($parts[1], $html_content);
        }
        catch(Exception $e) {
            dd($e->getMessage());
        }

        $header = '<div style="width:100%; padding:10px; text-align:center;font-family: arial;">
                    <a href="[AltBrowserLink]" target="_blank" style="font-size:12px; text-decoration:none; color: #'.$text_color.';font-family:Tahoma,Verdana,Segoe,sans-serif;font-size:14px;">'.$lang.'</a>
                </div>';

        $html_content = $parts[0] . $parts[1] . "\r\r\n" . $header . $parts[2]; 

        return $html_content;         
    }

    public function addFooter($html_content) {
        $config = Cache::get('config');

        if (Str::contains($config['server'], 'resellerdirect')) {
            $html_content = Str::replace('</body>', '[footer700]</body>', $html_content);

            return $html_content; 
        }
        
        $de = '<!-- Footer -->
        <br />
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="690">
        <tbody>
        <tr>
        <td colspan="2">
        <hr>
        <br>
        </td>
        </tr>
        <tr valign="middle">
        <td valign="top" width="200">
        <img src="'.$config['server'] . $config['folder'] . '/' .'logo_mxp.png" height="56" width="181">
        </td>
        <td>
        <div style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';text-align:justify;">Dieses E-Mail ist kein Spam! Sie erhalten es als registrierter User, 
                    als Interessent oder als Kunde der pro connect. pro connect mail-X-press informiert Sie regelm????ig ??ber topaktuelle Sonderangebote leistungsstarker 
                    Lieferanten und Hersteller. Es gelten die 
                    <a href="https://pro-connect.de/privacy-policy/" style="font-family: arial, verdana; color: #'.$config["text_color"].'; font-size:10px;" target="_blank">Datenschutz-Bedingungen</a> und die <a href="https://pro-connect.de/terms-and-conditions/" style="font-family: arial, verdana; color: #'.$config["text_color"].'; font-size:10px;" target="_blank">AGBs</a> 
                    der pro connect, ein Gesch??ftsbereich der Flotte Medien GmbH, Theaterstrasse 22, 53111 Bonn. Gesch??ftsf??hrung: Bernd Franke, UID: DE 815 331 978, 
                    Steuernummer 205/5716/1746, Handelsregister: AG Bonn, HRB 19053. Alle Preise in ??? (Euro) zzgl. MWSt., Irrt??mer, Preis??nderungen vorbehalten. ?? <a href="https://pro-connect.de/" style="font-family: arial, verdana; color: #'.$config["text_color"].'; font-size:10px;" target="_blank">pro connect</a>, Telefon +49-228-286294-30, E-Mail: 
                    <a href="mailto:info@pro-connect.de" style="font-family: arial, verdana; color: #'.$config["text_color"].'; font-size:10px;" target="_blank">info@pro-connect.de</a>
        </div>
        </td>
        </tr>
        <tr>
        <td colspan="2">&nbsp;<br>
        <hr>
        </td>
        </tr>
        <tr>
        <td colspan="2" align="center">&nbsp;<br>
        <a href="[UnsubscribeLink]" style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';" target="_blank">E-Mails abbestellen!</a>
        </td>
        </tr>
        </tbody>
        </table>
        <br />';

        $en = '<!-- English Footer -->
        <br />
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:800px;">
        <tbody>
        <tr>
        <td colspan="2" >
        <hr />
        <br />
        </td>
        </tr>
        <tr valign="middle">
        <td valign="top" width="200">
        <img src="'.$config['server'] . $config['folder'] . '/' .'logo_mxp.png" height="56" width="181">
        </td>
        <td>
        <div style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';text-align:justify;">This e-mail is not a spam! You recieve this e-mail as a registered client or as an intrested person 
                    of pro connect. pro connect mail-X-press informs you regularly about current special offers from key suppliers and manufacturers. 
                    <a href="https://pro-connect.de/en/terms-and-conditions/" style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';" target="_blank">General Terms and Conditions</a> and 
                    <a href="https://pro-connect.de/privacy-policy/" style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';" target="_blank">data-protection</a> conditions of pro connect, a 
                    division of Flotte Medien GmbH, Theaterstrasse 22, 53111 Bonn. Manager: Bernd Franke, UID: DE 815 331 978, tax number 205/5716/1746, commercial register: AG Bonn, HRB 19053. All prices in &euro; 
                    (Euros) plus tax, errors and prices subject to alteration. &copy; <a href="https://pro-connect.de/" style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';" target="_blank">pro connect</a>, 
                    phone +49-228-286294-30, mail: <a href="mailto:info@pro-connect.de" style="font-family: arial, verdana; color: #'.$config["text_color"].';" target="_blank" >info@pro-connect.de</a>
        </div>
        </td>
        </tr>
        <tr>
        <td colspan="2" >&nbsp;<br />
        <hr />
        </td>
        </tr>
        <tr>
        <td colspan="2" align="center">&nbsp;<br />
        <a href="[UnsubscribeLink]" style="font-family: arial, verdana; font-size: 10px; color: #'.$config["text_color"].';" target="_blank">Unsubscribe!</a>
        </td>
        </tr>
        </tbody>
        </table>
        <br />';
        
        if ($config['language'] == 'de') {
            $lang = $de;
        } else {
            $lang = $en;
        }

        $html_content = Str::replace('</body>', $lang . '</body>', $html_content);
        //dd($html_content);

        return $html_content; 
    }

}