<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        CodeMake.Org
 * @package        Module_Webinar
 */
class Webinar_Service_Utils extends Phpfox_Service
{
    public function __construct()
    {

    }

    private function _utf8ToUnicode($str, $bForUrl = false)
    {
        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);

            if ($thisValue < 128) {
                $unicode[] = $thisValue;
            } else {
                if (count($values) == 0) $lookingFor = ($thisValue < 224) ? 2 : 3;

                $values[] = $thisValue;

                if (count($values) == $lookingFor) {
                    $number = ($lookingFor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
                        (($values[0] % 32) * 64) + ($values[1] % 64);

                    $unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }

        return $this->_unicodeToEntitiesPreservingAscii($unicode, $bForUrl);
    }

    private function _unicodeToEntitiesPreservingAscii($unicode, $bForUrl = false)
    {
        $entities = '';
        foreach ($unicode as $value) {
            if ($bForUrl === true) {
                if ($value == 42 || $value > 127) {
                    $sCacheValue = Phpfox::getLib('locale')->parse('&#' . $value . ';', false);

                    $entities .= (preg_match('/[^a-zA-Z]+/', $sCacheValue) ? '-' . $value : $sCacheValue);
                } else {
                    $entities .= (preg_match('/[^0-9a-zA-Z]+/', chr($value)) ? ' ' : chr($value));
                }
            } else {
                $entities .= ($value == 42 ? '&#' . $value . ';' : ($value > 127) ? '&#' . $value . ';' : chr($value));
            }
        }
        $entities = str_replace("'", '&#039;', $entities);
        return $entities;
    }

    private function _shorten($sTxt, $iLetters)
    {
        if (!preg_match('/(&#[0-9]+;)/', $sTxt)) {
            return mb_substr($sTxt, 0, $iLetters, 'utf-8');
        }
        $sOut = '';
        $iOutLen = 0;
        $iPos = 0;
        $iTxtLen = strlen($sTxt);
        for ($iPos; $iPos < $iTxtLen && $iOutLen <= $iLetters; $iPos++) {
            if ($sTxt[$iPos] == '&') {
                $iEnd = strpos($sTxt, ';', $iPos) + 1;
                $sTemp = mb_substr($sTxt, $iPos, $iEnd - $iPos, 'utf-8');
                if (preg_match('/(&#[0-9]+;)/', $sTemp)) {
                    $sTmp = $sOut;
                    $sOut .= $sTemp; // add the entity altogether
                    if (strlen($sOut) > $iLetters) {
                        return $sTmp;
                    }
                    $iOutLen++; // increment the length of the returning string
                    $iPos = $iEnd - 1; // move the pointer to skip the entity in the next run
                    continue;
                }
            }
            $sOut .= $sTxt[$iPos];
            $iOutLen++;
        }
        return $sOut;
    }

    public function text($sTxt, $iShorten = null, $sEnding = null)
    {
        $sTxt = strip_tags($sTxt);
        $sTxt = Phpfox::getLib('parse.output')->htmlspecialchars($sTxt);

        // Parse for language package
        $sTxt = $this->_utf8ToUnicode($sTxt);
        $sTxt = str_replace('\\', '&#92;', $sTxt);

        if ($iShorten !== null) {
            if (strlen($sTxt) >= $iShorten) {
                $sTxt = $this->_shorten($sTxt, $iShorten);
                if ($sEnding !== null) {
                    $sTxt = $sTxt . $sEnding;
                }
            }
        }

        return $sTxt;
    }

    public function convertTime($iTime, $sFormat = null){
        return Phpfox::getLib('date')->convertTime($iTime, $sFormat);
    }

    public function findInArray($sFind, $aArray){
        if ( empty($sFind) || !is_array($aArray) ){
            return false;
        }

        foreach($aArray as $sItem){
            if (!stripos($sItem, $sFind)){
                continue;
            }
            return true;
        }

        return false;
    }
}

?>