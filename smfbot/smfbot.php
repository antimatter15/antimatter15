<?php

/**
 *	SMFBot Class
 *	Copyright (C) 2008 Mori [http://moriakaice.wordpress.com/] & Mchl
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@version 1.1
 */

class SMFBot
{

	/**
	 *	Username to be used when logging in to SMF
	 */
	protected $username;
	
	/**
	 *	User's password to be used when logging in to SMF
	 */
	protected $password;
	
	/**
	 *	URI if SMF top directory (one where index.php resides)
	 */
	protected $url;

	/**
	 *	User's total logged in time in seconds
	 */
	protected $totalLogged;

	/**
	 *	Name of last poster in the topic (as got by SMFBot::getLastPost() )
	 */
	protected $lastPoster;
	
	/**
	 *	ID of last poster in the topic (as got by SMFBot::getLastPost() )
	 */
	protected $lastPosterID;
	
	/**
	 *	Contents of last message in the topic (as got by SMFBot::getLastPost() )
	 */
	protected $lastMessage;
	
	/**
	 * Is the SMFBot logged in
	 */
	protected $loggedIn = false;
	
	/**
	 * User agent string
	 */
	protected $userAgent = "Opera/9.6 (Windows NT 5.1; U; Antimatter15; en)";
	
	/**
	 *	Internal
	 */
	protected $sc;
	
	/**
	 *	Internal
	 */
	protected $seqnum;
	
	/**
	 *	Internal
	 */
	protected $sesc;
	


	/**
	 *	Constructor
	 *	
	 *	@param[in] string $username Username to log into SMF
	 *	@param[in] string $password Password to log into SMF
	 *	@param[in] string $url URI if SMF top directory (one where index.php resides)
	 *	@param[in] string $userAgent User agent string.
	 */
	public function __construct($username, $password, $url, $userAgent = NULL)
	{
		if( !file_exists('smf.cookie.file.txt') ) file_put_contents('smf.cookie.file.txt', '');
		if(isset($userAgent)) $this->userAgent = $userAgent;
		$this->username = $username;
		$this->password = $password;
		$this->url = $url;
		
		$urlLen = strlen($this->url)-1;
		if($this->url[$urlLen] != '/') $this->url.='/';
		
		$this->login();
	}
	
	
	
	/**
	 *	Uses cURL to fetch page from SMF
	 *	
	 *	@param[in] string $url2 URI of page to get
	 *	@param[in] string $ref Refferer page URL
	 *	@param[in] string $post POST data to be send
	 */
	protected function getPage ($url2, $ref='', $post='')
	{
		if(!extension_loaded('curl')) trigger_error("cURL extension unavailable",E_USER_ERROR);
		$ch = curl_init();

		if( !empty($post) )
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		else
		{
				curl_setopt($ch, CURLOPT_POST, 0);
		}
		
		if( empty($ref) or $ref == '' ) $ref = $url2;
			
		curl_setopt($ch, CURLOPT_REFERER, $ref);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).'/smf.cookie.file.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/smf.cookie.file.txt');
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url2);

		$r = curl_exec($ch);		
		return $r;
	}
	
	/**
	 *	SMF log in action
	 */
	protected function login()
	{
		$this->loggedIn = true;
		return $this->getPage($this->url.'index.php?action=login2', $this->url, 'user='.$this->username.'&passwrd='.$this->password.'&cookielength=-1&hash_passwrd=');
	}
	
	/**
	 *	SMF log out action.
	 */
	public function logout()
	{
		$this->getSesc();
		$this->getPage($this->url.'index.php?action=logout;sesc='.$this->sesc);
	}
	
	/**
	 *	Gets last message and its author's username and ID from topic indicated by ID
	 *	
	 * @param[in] int $topicID ID of topic from which a message is to be get.
	*/
	public function getLastPost($topicID)
	{
		if($this->loggedIn == true)
		{
			$r = $this->getPage($this->url.'index.php/topic,'.$topicID.'.0.html');
			
			if( eregi('class="navPages"', $r) )
			{
				$r = explode('class="navPages" href="', $r);
				$n = count($r)-1;
				$r = $r[$n];
				$r = explode('">', $r);
				$r = $r[0];
				$r = $this->getPage($r);
			}

			$r = explode($this->url.'index.php?action=profile;u=', $r);
			$n = count($r)-2;
			$r = $r[$n].$r[++$n];
			$r = explode('<a name="lastPost"></a>', $r);
			$r = $r[0];
			
			$this->lastPoster = explode('title="Zobacz profil ', $r);
			$this->lastPosterID = $this->lastPoster[0];
			$this->lastPoster = $this->lastPoster[1];
			$this->lastPoster = explode('">', $this->lastPoster);
			$this->lastPoster = $this->lastPoster[0];
			
			$this->lastPosterID = (int)str_replace(array($url."index.php?action=profile;u=","\" "),"",$this->lastPosterID);

			preg_match("@<div class=\"post\"(.*)</div>@", $r, $this->lastMessage);
			$this->lastMessage = $this->lastMessage[0];
			$this->lastMessage = substr($this->lastMessage, 17, -6);
			
			if($this->lastMessage[0] != '>')	$pos = strpos($this->lastMessage, '>')+1;
			else $pos = 1;
			
			$this->lastMessage = substr($this->lastMessage, $pos);
		}
		else
		{
			$this->login();
			$this->getLastPost($topicID);
		}
	}
	
	/**
	 * Gets sc and seqnum from topic indicated by topicID
	 * Required to post a reply. Uses quick reply.
	 *
	 * @param[in] int $topicID ID of topic
	 */
	protected function scSeqnum($topicID)
	{
		if($this->loggedIn == true)
		{
			$r = $this->getPage($this->url.'index.php/topic,'.$topicID.'.0.html');
		
			$sc = explode('<input type="hidden" name="sc" value="', $r);
			$sc = $sc[2];
			$sc = explode('"', $sc);
			$this->sc = $sc[0];
			
			$seqnum = explode('<input type="hidden" name="seqnum" value="', $r);
			$seqnum = $seqnum[1];
			$seqnum = explode('"', $seqnum);
			$this->seqnum = $seqnum[0];
		}
		else
		{
			$this->login();
			$this->scSeqnum($topicID);
		}
	}
	
	/**
	 * Gets sesc - needed for editing and logging out
	 */
	protected function getSesc()
	{
		if($this->loggedIn == true)
		{
			$r = $this->getPage($this->url);
			preg_match("@(sesc\=)[a-z0-9]{32}@", $r, $sesc);
			$this->sesc = substr($sesc[0], 5);
		}
		else
		{
			$this->login();
			$this->getSesc();
		}
	}
	
	/**
	 * Posts a new message
	 *
	 * @param[in] int $topicID ID of topic in which to post
	 * @param[in] string $message Message contents
	 * @param[in] string $subject Message title
	 * @param[in] string $icon Message icon
	 */
	public function postMessage($topicID, $message, $subject='', $icon='exclamation')
	{
		if($this->loggedIn == true)
		{
			$this->scSeqnum($topicID);
			
			$message = $this->replace($message);
			$subject = $this->replace($subject);
		
			$ref = $this->url.'index.php?action=post;topic='.$topicID.'.0;num_replies=0';
			$tar = $this->url.'index.php?action=post2';
			$post = 'postmodify&topic='.$topicID.'&subject='.$subject.'&icon='.$icon.'&num_replies=99999999&message='.$message.'&notify=0&goback=1&sticky=0&showsig=1&ns=NS&post=Send&sc='.$this->sc.'&seqnum='.$this->seqnum;
			
			$this->getPage($tar, $ref, $post);
		}
		else
		{
			$this->login();
			$this->postMessage($topicID, $message, $subject, $icon);
		}
	}
	
	/**
	 * Edits existing message
	 *
	 * @param[in] int $topicID ID of topic in which message to be edited exists
	 * @param[in] int $messageID ID of message to be edited
	 * @param[in] string $message Message contents
	 * @param[in] string $subject Message title
	 * @param[in] string $icon Message icon
	 * @param[in] bool $append Set to true to append message to existing content, set to false to overwrite
	 */
	public function editMessage($topicID, $messageID, $message, $subject='', $icon='', $append=false)
	{
		if($this->loggedIn == true)
		{
			$this->getSesc();
			$ref = $this->url.'index.php?action=post;msg='.$messageID.';topic='.$topicID.'.0;sesc='.$this->sesc;
			$r = $this->getPage($ref);
			
			if($subject == '')
			{
				preg_match("@(<input type=\"text\" name=\"subject\" value=\")(.*)(\" tabindex)@", $r, $subject);
				$subject = $subject[2];
			}
			
			if($icon == '')
			{
				preg_match("@(<option value=\")(.*)(\" selected=\"selected\">)@", $r, $icon);
				$icon = $icon[2];
			}
			
			if($append == true)
			{
				preg_match("@(<textarea class=\"editor\" name=\"message\")(.*)(\">)(.*)(</textarea>)@", $r, $oldMessage);
				$oldMessage = $oldMessage[4];
				$message = $oldMessage.' '.$message;
			}
			
			preg_match("@(<link rel=\"index\" href=\"".$this->url."index.php/board,)([0-9]{1,5})(.0.html)\" />@", $r, $board);
			$board = $board[2];
			
			$message = $this->replace($message);
			$subject = $this->replace($subject);
			
			$tar = $this->url.'index.php?action=jsmodify;start=0;msg='.$messageID.';sesc='.$this->sesc.';board='.$board.';xml';
			$post = 'postmodify&topic='.$topicID.'&subject='.$subject.'&icon='.$icon.'&num_replies=50&message='.$message.'&notify=0&lock=0&goback=1&sticky=0&showsig=1&post=Zachowaj&sc='.$this->sc.'&seqnum='.$this->seqnum.'&additional_options=0';
			$r = $this->getPage($tar, $ref, $post);
		}
		else
		{
			$this->login();
			$this->editMessage($topicID, $messageID, $message, $subject, $icon, $append);
		}
	}
	
	/**
	 *	Get (current) user's total logged in time and store it into SMFBot::totalLogged
	 */
	public function totalLogged()
	{
		if($this->loggedIn == true)
		{
			$r = $this->getPage($this->url);
			$r = explode('class="middletext"', $r);
			$r = $r[1];
			$r = explode('<br />', $r);
			$r = explode(':', $r[2]);
			$r = explode(' ', $r[1]);
			$this->totalLogged = (($r[1] * 24 + $r[3]) * 60 + $r[5]) * 60;
		}
		else
		{
			$this->login();
			$this->totalLogged();
		}
	}
	
	/**
	 *	Locks/unlocks topic. User must have privileges to lock topic.
	 *
	 *	@param[in] integer $topicID ID of topic to be locked
	 */
	public function lockTopic($topicID)
	{
		if($this->loggedIn == true)
		{
			$this->getSesc();
			$r = $this->getPage($this->url.'index.php?action=lock;topic='.$topicID.';sesc='.$this->sesc);
		}
		else
		{
			$this->login();
			$this->lockTopic($topicID);
		}
	}
	
	/**
	 *	Helper function for urlencoding post title and contents.
	 *	Called from SMFBot::postMessage() and SMFBot::editMessage()
	 *
	 *	@param[in] string $string String to be encoded
	 *	@returns string Encoded string
	 */
	protected function replace($string)
	{
		$string = str_replace('&quot;', '"', $string);
		$string = str_replace('&nbsp;', ' ', $string);
		$string = rawurlencode($string);
		return $string;
	}
	
	
	/**
	 *	Gets SMFBot::username value
	 *
	 * @returns string
	 */
	public function getUsername()
	{
		return $this->username;
	}
	
	/**
	 *	Gets SMFBot::lastPoster value
	 *
	 * @returns string
	 */
	public function getLastPoster()
	{
		return $this->lastPoster;
	}
	
	/**
	 *	Gets SMFBot::lastPosterID value
	 *
	 * @returns int
	 */
	public function getLastPosterID()
	{
		return $this->lastPosterID;
	}
	
	/**
	 *	Gets SMFBot::lastMessage value
	 *
	 * @returns string
	 */
	public function getLastMessage()
	{
		return $this->lastMessage;
	}
	
	/**
	 *	Gets SMFBot::totalLogged value
	 *
	 * @returns int
	 */
	public function getTotalLogged()
	{
		return $this->totalLogged;
	}
	
	/**
	 * Gets user data from xml data provided by SMF
	 *
	 * @param[in] $ID User ID
	 *
	 * @returns SimpleXMLElement Object with user data. Structure may vary depending on SMF addons and user privileges.
	 */
	public function getUser($ID) 
	{
		$xmluri = $this->url . "index.php?action=.xml;sa=profile;u=$ID";
		if(true == $this->loggedIn)
		{
			if ($xml = @simplexml_load_file($xmluri, 'SimpleXMLElement',LIBXML_NOCDATA)) 
			{
				return $xml;
			}
			else
			{
				return new SimpleXMLElement("<response><success>false</success><message>XML data unavailable</message></response>");
			}
		}
		else
		{
			$this->login();
			return $this->getUser($ID);
		}
	}
	
	/**
	 * Sets SMFBot::userAgent
	 *
	 * $param[in] $userAgent User agent string
	 */
	public function setUserAgent($userAgent) 
	{
		if (!empty($userAgent)) $this->userAgent = $userAgent;
	}
}
?>
