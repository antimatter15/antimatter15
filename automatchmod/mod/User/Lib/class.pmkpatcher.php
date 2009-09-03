<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Peter Klein (peter@umloud.dk)
*  All rights reserved
*
*  This script is part free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license 
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Example:
 * $diff = file_get_contents('diff/dif6.diff');
 * $path = 'F:/apachefriends/xampp/htdocs/typo3-4.2/typo3conf/ext/tinymce_rte/';
 * $path = '';
 *
 * $x = pmkpatcher::patch($diff,$path);
 * if (!is_array($x)) echo $x;
 * else echo $x['data'];
 */

/** 
 * class.pmkpatcher.php
 *
 * Class for patching files using unified .diff format
 *
 * @author	Peter Klein <peter@umloud.dk8>
 */
class pmkpatcher {
	protected $errorMsg ='';
	protected $destinationFile = '';
	protected $sourceFile = '';

  // -- Public Static Methods --------------------------------------------------

	/**
	 * Patch file using .diff file in unified diff format.
	 *
	 * @param	string			Content of unified .diff file to process
	 * @param	string			Otional path which is prepended to the source name found in the .diff
	 * @param	boolean			Optional flag for reversing the patching (patch will be removed from destination file)
	 * @return	array/string	If patching was sucessful, an array containing the patched file (data), an optional comment (comment), the source file name (source) and the destination file name (destination).
	 *							If patching failed, a string is returned, containing the error msg.
	 */
	public static function patch($diffFile,$path='',$rev=false) {
		$patcher = new pmkpatcher($path);
		$diffArray = $patcher->_parseDiff($diffFile,$rev);
		$patcheData = $patcher->_applyDiff($diffArray,$rev);
		return ($patcheData) ? array(
			'data' => $patcheData,
			'source' => $patcher->sourceFile,
			'destination' => $patcher->destinationFile,
			'comment' => $patcher->comment
		) : $patcher->errorMsg;
	}
	
	/**
	 * Unpatch file using .diff file in unified diff format.
	 *
	 * @param	string			Content of unified .diff file to process
	 * @param	string			Otional path which is prepended to the source name found in the .diff
	 * @return	array/string	If patching was sucessful, an array containing the patched file (data), an optional comment (comment), the source file name (source) and the destination file name (destination).
	 *							If patching failed, a string is returned, containing the error msg.
	 */
	public static function unpatch($diffFile,$path='') {
		return pmkpatcher::patch($diffFile,$path,true);
	}
	
	/**
	 * Returns parsed .diff file as array
	 *
	 * @param	string			Content of unified .diff file to process
	 * @param	string			Otional path which is prepended to the source name found in the .diff
	 * @param	boolean			Optional flag for reversing the patching (patch will be removed from destination file)
	 * @return	array/string	If parsing was sucessful, an array containing the parsed diff data (diffdata), an optional comment (comment), the source file name (source) and the destination file name (destination).
	 *							If parsing failed, a string is returned, containing the error msg.
	 */
	public static function parseDiff($diffFile,$path='',$rev=false) {
		$patcher = new pmkpatcher($path);
		$diffArray = $patcher->_parseDiff($diffFile,$rev);
		return is_array($diffArray) ? $diffArray : $patcher->errorMsg;
	}
	
	public static function applyDiff($diffArray,$path='',$rev=false) {
		$patcher = new pmkpatcher($path);
		$patchedData = $patcher->_applyDiff($diffArray,$rev);
		return is_array($patchedData) ? $patchedData : $patcher->errorMsg;
	}
	// -- Public Instance Methods ------------------------------------------------
	
	public function __construct($path = '') {
		$this->path = $path;
	}

  // -- Protected Instance Methods ---------------------------------------------
	
	protected function _parseDiff($diffFile, $rev=false) {
		
		$lines = preg_split('/\r\n|\r|\n/', $diffFile);
		$diffArray = array();
		reset($lines);
		$line = current($lines);
		$counter = 0;
		while ($line!==false) {
			// Continue looping until we find --- at the beginning of a line.
			// Everything before that is treated as a comment.
			$comment = array();
			while (!preg_match('/^---/', $line) && $line!==false) {
				$comment[] = preg_replace('%^//\s*%', '', $line);
				$line = next($lines);
			}
			
			if (preg_match('/^--- ([^\t]*)/', $line, $regs)) {
				$sourceFile = $regs[1];
			} else {
				if ($line===false) continue; // EOF
				// No source filename specified.
				$this->errorMsg = 'No source filename specified.<br />';
				return false;
			}
			$line = next($lines);
			if (preg_match('/^\+\+\+ ([^\t]*)/', $line, $regs)) {
				$destinationFile = $regs[1];
			} else {
				if ($line===false) continue; // EOF
				// No destination filename specified.
				$this->errorMsg = 'No destination filename specified.<br />';
				return false;
			}
			$line = next($lines);
			// Parse custom marker for adding or removing binary files
			if (preg_match('/^@@\s*(binary-file)\s*@@$/', $line, $regs) && $line!==false) {
				$diffArray[$counter]['comment'] = $comment;
				$diffArray[$counter]['sourcefile'] = $sourceFile;
				$diffArray[$counter]['destinationfile'] = $destinationFile;
				$diffArray[$counter]['type'] = $regs[1];
				$line = next($lines);
			}
			else {
				while (preg_match('/^@@\s+-(\d+)(,(\d+))?\s+\+(\d+)(,(\d+))?\s+@@$/', $line, $regs) && !preg_match('/^---/', $line) && $line!==false) {
					$srcline = intval($regs[4]);
					$srcsize = $pc = (!isset($regs[6])) ? 1 : intval($regs[6]);
					$dstline = intval($regs[1]);
					$dstsize = $mc = (!$regs[3]) ? 1 : intval($regs[3]);
					
					$diffArray[$counter]['comment'] = $comment;
					$diffArray[$counter]['sourcefile'] = $sourceFile;
					$diffArray[$counter]['destinationfile'] = $destinationFile;
					
					$data = array();
					while (!($mc==0 && $pc==0) && $line!==false) {
						$line = (string)next($lines);
						if (!preg_match('/\+|-| /i', $line{0})) {
							$line = prev($lines);
							break;
						}
						$mc -= ($line{0}!='+' ? 1 : 0);
						$pc -= ($line{0}!='-' ? 1 : 0);
						$data[] = $line;
					}
					$diffArray[$counter]['range'][] = array(
						'srcline' => $rev ? $dstline : $srcline,
						'srcsize' => $rev ? $dstsize : $srcsize,
						'dstline' => $rev ? $srcline : $dstline,
						'dstsize' => $rev ? $srcsize : $dstsize,
						'data' => $data
					);
					$line = next($lines);
				}
			}
			$counter++;
		}
		return $diffArray;
	}
	
	protected function _applyDiff($diffArray, $rev=false) {
		// Process diff data
		foreach ($diffArray as $key => $diffParts) {
					
			if (isset($diffParts['range']) && $diffParts['destinationfile'] != '/dev/null' && ($diffParts['sourcefile'] != '/dev/null' || ($diffParts['sourcefile'] == '/dev/null' && !$rev) ) && $diffParts['type'] != 'binary-file') {
				if ($diffParts['sourcefile'] == '/dev/null') {
					$source = '';
				} else {
					$sourceFile = $this->path.$diffParts['sourcefile'];
					$source = @file_get_contents($sourceFile);
					if (!$source) {
						$this->errorMsg = '<p>Error: sourcefile not found.<br/>'.$sourceFile.'</p>';
						return false;
					}
				}
				$sLines = preg_split('/\r\n|\r|\n/', $source);
				$destLines = $sLines;
				$offset = 0;
				foreach ($diffParts['range'] as $data) {
					$diffPart = array_slice($this->array_filterPM($data['data'],'+',$rev),0,$data['srcsize']);
					$comparePart = array_slice($destLines,$data['dstline']-1+$offset,count($diffPart));
					// Compare diff part with (presumed) same part in destination file.
					$fail = array_diff($diffPart,$comparePart);
					if ($fail) {
						$this->errorMsg = '<p>Error: Diff file doesn\'t match sourcefile.<br/>';
						foreach ($fail as $linenum => $line) {
							$this->errorMsg .= 'Line: '.$linenum.' => '.htmlspecialchars($line).'<br />';
						}
						$this->errorMsg .= '</p>';
						return false;
					}
					else {
						// Apply diff data to destination file.
						$replace = $this->array_filterPM($data['data'],'-',$rev);
						// Make sure no unwanted lines (such as extra linefeeds) is included in the replacement data
						$replace = array_slice($replace,0,$data['srcsize']);
						array_splice($destLines,$data['dstline']-1+$offset,$data['dstsize'],$replace);
						$offset += $data['srcsize']-$data['dstsize'];
					}
				}
				// Get rid of the extra linefeed at the end, before returning result
				//$destLines = array_slice($destLines,0,count($sLines)+$offset+1);
				$diffArray[$key]['patcheddata'] = implode(chr(10),$destLines);
			}	elseif ($diffParts['type'] == 'binary-file') {
				// Process custom marker for adding or removing binary files
				$file = (!$rev) ? $this->path.$diffParts['sourcefile'] : dirname($this->path.$diffParts['sourcefile']) . '/undo/' . basename($this->path.$diffParts['sourcefile']);
				if (!file_exists($file) || !is_file($file)) {
				  if ($rev) {
						$diffArray[$key]['sourcefile'] = $diffArray[$key]['destinationfile'];
						$diffArray[$key]['destinationfile'] = '/dev/null';
					} else {
						$this->errorMsg = '<p>Error: sourcefile not found.<br/>'.$file.'</p>';
						return false;
					}
				}
			}
		}
		return $diffArray;
	}
	
	protected static function array_filterPM($arr,$excl,$rev=false) {
		if ($rev) $excl = $excl=='+' ? '-' : '+';
		$res = array();
		foreach ($arr as $v) {
			if ($v{0}!=$excl) $res[] = (string)substr($v,1);
		}
		return $res;
	}

}
?>
