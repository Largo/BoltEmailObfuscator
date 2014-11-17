<?php
// EMail Obfuscate Extension for Bolt
// Minimum version: 0.7

namespace EMailObfuscate;

class Extension extends \Bolt\BaseExtension
{

    function info() {

        $data = array(
            'name' =>"EMail Obfuscate",
            'description' => "A small extension to obfuscate email adresses, when using <code>{{ emailObfuscate(body.record) }}</code> in your templates.",
            'author' => "Largo",
            'link' => "http://bolt.cm",
            'version' => "1.1",
            'required_bolt_version' => "1.0",
            'highest_bolt_version' => "1.5",
            'type' => "Twig function",
            'first_releasedate' => "2014-11-17",
            'latest_releasedate' => "2014-11-17",
        );

        return $data;
    }

    function initialize() {
        $this->addTwigFunction('emailObfuscate', 'twigEmailObfuscate');
    }

    // http://kvz.io/blog/2012/10/09/reverse-a-multibyte-string-in-php/
    function mb_strrev ($string, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_detect_encoding($string);
        }

        $length   = mb_strlen($string, $encoding);
        $reversed = '';
        while ($length-- > 0) {
            $reversed .= mb_substr($string, $length, 1, $encoding);
        }

        return $reversed;
    }

    function twigEmailObfuscate($data) {
        /*** todo: .co.uk E-Mail adresses, new toplevel domains **/

        // replace mailto links with just the email adress
        $data = preg_replace("~<a .*?href=[\'|\"]mailto:(.*?)[\'|\"].*?>.*?</a>~", "$1", $data);

        // Regex String for email adresses
        $searchEmail = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';

        // Replace plaintext email strings
        $pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
        while (preg_match($pattern, $data, $regs, PREG_OFFSET_CAPTURE)) {
            $mail = $regs[1][0];

            // wrap reversed email adress with special class and mask at sign, so it won't be targeted again.
            $replacement = '<span class="emailObfuscate">' . str_replace('@','%#ATSIGN#%', $this->mb_strrev($mail)) . '</span>';

            $data = substr_replace($data, $replacement, $regs[1][1], strlen($mail));
        }

        // replace masked atsign with real at.
        $data = str_replace('%#ATSIGN#%', '@', $data);

        return new \Twig_Markup($data, 'UTF-8');
    }
}
