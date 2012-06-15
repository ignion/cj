<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012  <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'cj links in T3' for the 'ign_cjlinks2t3' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_igncjlinks2t3
 */
class tx_igncjlinks2t3_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_igncjlinks2t3_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_igncjlinks2t3_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ign_cjlinks2t3';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
                // CJ Account stuff
		$conf['cjaccount.']['websiteid'] = $this->cObj->stdWrap($conf['cjaccount.']['websiteid'], $conf['cjaccount.']['websiteid.']);
		$conf['cjaccount.']['advertiserids'] = $this->cObj->stdWrap($conf['cjaccount.']['advertiserids'], $conf['cjaccount.']['advertiserids.']);
		$conf['cjaccount.']['serviceablearea'] = $this->cObj->stdWrap($conf['cjaccount.']['serviceablearea'], $conf['cjaccount.']['serviceablearea.']);
		$conf['cjaccount.']['advertisersku'] = $this->cObj->stdWrap($conf['cjaccount.']['advertisersku'], $conf['cjaccount.']['advertisersku.']);
                $conf['cjaccount.']['currency'] = $this->cObj->stdWrap($conf['cjaccount.']['currency'], $conf['cjaccount.']['currency.']);
		$conf['cjaccount.']['cjkey'] = $this->cObj->stdWrap($conf['cjaccount.']['cjkey'], $conf['cjaccount.']['cjkey.']);
                
                // CJ Search Options
                $conf['cjmode.']['productsearch.'] = $this->cObj->stdWrap($conf['cjmode.']['productsearch'], $conf['cjmode.']['productsearch.']);
                $conf['cjmode.']['maxresults'] = $this->cObj->stdWrap($conf['cjmode.']['maxresults'], $conf['cjmode.']['maxresults.']);
                $conf['cjmode.']['keywords.'] = $this->cObj->stdWrap($conf['cjmode.']['keywords'], $conf['cjmode.']['keywords.']);
                
                // Response format (XML or json)
                $conf['cjresultformat'] = $this->cObj->stdWrap($conf['cjresultformat'], $conf['cjresultformat']);
                
                $conf['url'] = $this->cObj->stdWrap($conf['url'], $conf['url']);
		
				//  URL depends on search option
                
                if ($conf['cjmode.']['productsearch'] == 1) {
                    $url =  $conf['url']."&website-id=".$conf['cjaccount.']['websiteid']."&advertiser-ids=".$conf['cjaccount.']['advertiserids']."&keywords=".$conf['cjmode.']['keywords']."&records-per-page=".$conf['cjmode.']['maxresults'];
                }
               
               
                $connectionSession = curl_init();
                curl_setopt($connectionSession, CURLOPT_URL, $url);
                curl_setopt($connectionSession, CURLOPT_POST, FALSE);
                curl_setopt($connectionSession, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($connectionSession, CURLOPT_HTTPHEADER, array('Authorization: '.$conf['cjaccount.']['cjkey']));
                curl_setopt($connectionSession, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($connectionSession);
                curl_close($connectionSession);
                
                // Response could have been JSON. But it wasn't. 
                if (strtolower($conf['cjresultformat']) == ('json')) {
                    $json_decoded = json_decode($response, TRUE);
                    
                    if ($json_decoded) {
                        foreach ($json_decoded as $responseitem) {
                            $content .= buildLinkItem($responseitem);
                        } 
                    } else {
                        $content .=  "Argh. No JSON response from CJ!";
                    }
                } elseif (strtolower($conf['cjresultformat']) == ('xml')) {
                    if ($response) {
                        $responsexml = simplexml_load_string($response);
                        $responsearray = toArray($responsexml);
                        
                        $content .= "<ul>";
                        foreach ($responsearray['products']['product'] as $item) {
                            $content .= buildLinkItem($item);
                        }
                        $content .= "</ul>";
                    } else {
                        $content .=  "Argh. No XML response from CJ!";
                    }
                }

                return $this->pi_wrapInBaseClass($content);
	}
     }   
     
    function toArray($xml) {
            $array = json_decode(json_encode($xml), TRUE);

            foreach ( array_slice($array, 0) as $key => $value ) {
                if ( empty($value) ) $array[$key] = NULL;
                elseif ( is_array($value) ) $array[$key] = toArray($value);
            }

            return $array;
        }
  
    function buildLinkItem($item) {
			
		$imagelink = "<img alt=\"".$item['name']."\" src=\"".$item['image-url']."\"">";
		return "<li><a href=\"".$item['buy-url']."\">".$item['name'].".$imagelink</a>"."<br />Beschreibung: ".$item['description']."</li>";
    }
        

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ign_cjlinks2t3/pi1/class.tx_igncjlinks2t3_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ign_cjlinks2t3/pi1/class.tx_igncjlinks2t3_pi1.php']);
}

?>