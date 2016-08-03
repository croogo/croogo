<?php
/**
 * Akismet anti-comment spam service
 *
 * The class in this package allows use of the {@link http://akismet.com Akismet} anti-comment spam service in any PHP5 application.
 *
 * This service performs a number of checks on submitted data and returns whether or not the data is likely to be spam.
 *
 * Please note that in order to use this class, you must have a vaild {@link http://wordpress.com/api-keys/ WordPress API key}.  They are free for non/small-profit types and getting one will only take a couple of minutes.
 *
 * For commercial use, please {@link http://akismet.com/commercial/ visit the Akismet commercial licensing page}.
 *
 * Please be aware that this class is PHP5 only.  Attempts to run it under PHP4 will most likely fail.
 *
 * See the Akismet class documentation page linked to below for usage information.
 *
 * @author         Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @version        0.4
 * @copyright      Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @license        http://www.opensource.org/licenses/bsd-license.php BSD License
 */
namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 *    The Akismet PHP5 Class
 *
 *  This class takes the functionality from the Akismet WordPress plugin written by {@link http://photomatt.net/ Matt Mullenweg} and allows it to be integrated into any PHP5 application or website.
 *
 *  The original plugin is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 *  <b>Usage:</b>
 *  <code>
 *    $akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
 *    $akismet->setCommentAuthor($name);
 *    $akismet->setCommentAuthorEmail($email);
 *    $akismet->setCommentAuthorURL($url);
 *    $akismet->setCommentContent($comment);
 *    $akismet->setPermalink('http://www.example.com/blog/alex/someurl/');
 *    if ($akismet->isCommentSpam())
 *      // store the comment but mark it as spam (in case of a mis-diagnosis)
 *    else
 *      // store the comment normally
 *  </code>
 *
 *  Optionally you may wish to check if your WordPress API key is valid as in the example below.
 *
 * <code>
 *   $akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
 *
 *   if ($akismet->isKeyValid()) {
 *     // api key is okay
 *   } else {
 *     // api key is invalid
 *   }
 * </code>
 *
 * @package    Croogo.Croogo.Controller.Component
 * @name       Akismet
 * @version    0.4
 * @author     Alex Potsides
 * @link       http://www.achingbrain.net/
 */
class AkismetComponent extends Component
{
    private $version = '0.4';
    private $akismetAPIKey;
    private $blogURL;
    private $comment;
    private $apiPort;
    private $akismetServer;
    private $akismetVersion;

    // This prevents some potentially sensitive information from being sent accross the wire.
    private $ignore = ['HTTP_COOKIE',
                            'HTTP_X_FORWARDED_FOR',
                            'HTTP_X_FORWARDED_HOST',
                            'HTTP_MAX_FORWARDS',
                            'HTTP_X_FORWARDED_SERVER',
                            'REDIRECT_STATUS',
                            'SERVER_PORT',
                            'PATH',
                            'DOCUMENT_ROOT',
                            'SERVER_ADMIN',
                            'QUERY_STRING',
                            'PHP_SELF' ];

    /**
     * @param    string    $event            The URL of your blog.
     * @param    string    $wordPressAPIKey    WordPress API key.
     */
    public function startup(Event $event)
    {
        $this->blogURL = Configure::read('Service.akismet_url');
        $this->akismetAPIKey = Configure::read('Service.akismet_key');

        // Set some default values
        $this->apiPort = 80;
        $this->akismetServer = 'rest.akismet.com';
        $this->akismetVersion = '1.1';

        // Start to populate the comment data
        $this->comment['blog'] = $this->blogURL;
        $this->comment['user_agent'] = $this->request->header('user-agent') ?: 'Unknown';

        if ($this->request->referer()) {
            $this->comment['referrer'] = $this->request->referer();
        }

        $this->comment['user_ip'] = $this->request->clientIp();
    }

    /**
     * Makes a request to the Akismet service to see if the API key passed to the constructor is valid.
     *
     * Use this method if you suspect your API key is invalid.
     *
     * @return bool    True is if the key is valid, false if not.
     */
    public function isKeyValid()
    {
        // Check to see if the key is valid
        $response = $this->sendRequest('key=' . $this->akismetAPIKey . '&blog=' . $this->blogURL, $this->akismetServer, '/' . $this->akismetVersion . '/verify-key');
        return $response[1] == 'valid';
    }

    // makes a request to the Akismet service
    private function sendRequest($request, $host, $path)
    {
        $httpRequest  = "POST " . $path . " HTTP/1.0\r\n";
        $httpRequest .= "Host: " . $host . "\r\n";
        $httpRequest .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
        $httpRequest .= "Content-Length: " . strlen($request) . "\r\n";
        $httpRequest .= "User-Agent: Akismet PHP5 Class " . $this->version . " | Akismet/1.11\r\n";
        $httpRequest .= "\r\n";
        $httpRequest .= $request;

        $socketWriteRead = new SocketWriteRead($host, $this->apiPort, $httpRequest);
        $socketWriteRead->send();

        return explode("\r\n\r\n", $socketWriteRead->getResponse(), 2);
    }

    // Formats the data for transmission
    private function getQueryString()
    {
        foreach ($_SERVER as $key => $value) {
            if (!in_array($key, $this->ignore)) {
                if ($key == 'REMOTE_ADDR') {
                    $this->comment[$key] = $this->comment['user_ip'];
                } else {
                    $this->comment[$key] = $value;
                }
            }
        }

        $queryString = '';

        foreach ($this->comment as $key => $data) {
            if (!is_array($data)) {
                $queryString .= $key . '=' . urlencode(stripslashes($data)) . '&';
            }
        }

        return $queryString;
    }

    /**
     *    Tests for spam.
     *
     *    Uses the web service provided by {@link http://www.akismet.com Akismet} to see whether or not the submitted comment is spam.  Returns a boolean value.
     *
     * @return        bool    True if the comment is spam, false if not
     * @throws        Will throw an exception if the API key passed to the constructor is invalid.
     */
    public function isCommentSpam()
    {
        $response = $this->sendRequest($this->getQueryString(), $this->akismetAPIKey . '.rest.akismet.com', '/' . $this->akismetVersion . '/comment-check');

        if ($response[1] == 'invalid' && !$this->isKeyValid()) {
            throw new Exception('The API key passed to the Akismet constructor is invalid.  Please obtain a valid one from http://akismet.com/');
        }

        return ($response[1] == 'true');
    }

    /**
     *    Submit spam that is incorrectly tagged as ham.
     *
     *    Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
     */
    public function submitSpam()
    {
        $this->sendRequest($this->getQueryString(), $this->akismetAPIKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-spam');
    }

    /**
     *    Submit ham that is incorrectly tagged as spam.
     *
     *    Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
     */
    public function submitHam()
    {
        $this->sendRequest($this->getQueryString(), $this->akismetAPIKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-ham');
    }

    /**
     *    To override the user IP address when submitting spam/ham later on
     *
     * @param string $userip    An IP address.  Optional.
     */
    public function setUserIP($userip)
    {
        $this->comment['user_ip'] = $userip;
    }

    /**
     *    To override the referring page when submitting spam/ham later on
     *
     * @param string $referrer    The referring page.  Optional.
     */
    public function setReferrer($referrer)
    {
        $this->comment['referrer'] = $referrer;
    }

    /**
     *    A permanent URL referencing the blog post the comment was submitted to.
     *
     * @param string $permalink    The URL.  Optional.
     */
    public function setPermalink($permalink)
    {
        $this->comment['permalink'] = $permalink;
    }

    /**
     *    The type of comment being submitted.
     *
     *    May be blank, comment, trackback, pingback, or a made up value like "registration" or "wiki".
     */
    public function setCommentType($commentType)
    {
        $this->comment['comment_type'] = $commentType;
    }

    /**
     *    The name that the author submitted with the comment.
     */
    public function setCommentAuthor($commentAuthor)
    {
        $this->comment['comment_author'] = $commentAuthor;
    }

    /**
     *    The email address that the author submitted with the comment.
     *
     *    The address is assumed to be valid.
     */
    public function setCommentAuthorEmail($authorEmail)
    {
        $this->comment['comment_author_email'] = $authorEmail;
    }

    /**
     *    The URL that the author submitted with the comment.
     */
    public function setCommentAuthorURL($authorURL)
    {
        $this->comment['comment_author_url'] = $authorURL;
    }

    /**
     *    The comment's body text.
     */
    public function setCommentContent($commentBody)
    {
        $this->comment['comment_content'] = $commentBody;
    }

    /**
     *    Defaults to 80
     */
    public function setAPIPort($apiPort)
    {
        $this->apiPort = $apiPort;
    }

    /**
     *    Defaults to rest.akismet.com
     */
    public function setAkismetServer($akismetServer)
    {
        $this->akismetServer = $akismetServer;
    }

    /**
     *    Defaults to '1.1'
     */
    public function setAkismetVersion($akismetVersion)
    {
        $this->akismetVersion = $akismetVersion;
    }
}

/**
 *    Utility class used by Akismet
 *
 *  This class is used by Akismet to do the actual sending and receiving of data.  It opens a connection to a remote host, sends some data and the reads the response and makes it available to the calling program.
 *
 *  The code that makes up this class originates in the Akismet WordPress plugin, which is {@link http://akismet.com/download/ available on the Akismet website}.
 *
 *    N.B. It is not necessary to call this class directly to use the Akismet class.  This is included here mainly out of a sense of completeness.
 *
 * @package    Croogo.Croogo.Controller.Component
 * @name       SocketWriteRead
 * @version    0.1
 * @author     Alex Potsides
 * @link       http://www.achingbrain.net/
 */
class SocketWriteRead
{
    private $host;
    private $port;
    private $request;
    private $response;
    private $responseLength;
    private $errorNumber;
    private $errorString;

    /**
     * @param    string    $host            The host to send/receive data.
     * @param    int        $port            The port on the remote host.
     * @param    string    $request        The data to send.
     * @param    int        $responseLength    The amount of data to read.  Defaults to 1160 bytes.
     */
    public function __construct($host, $port, $request, $responseLength = 1160)
    {
        $this->host = $host;
        $this->port = $port;
        $this->request = $request;
        $this->responseLength = $responseLength;
        $this->errorNumber = 0;
        $this->errorString = '';
    }

    /**
     *  Sends the data to the remote host.
     *
     * @throws    An exception is thrown if a connection cannot be made to the remote host.
     */
    public function send()
    {
        $this->response = '';

        $fs = fsockopen($this->host, $this->port, $this->errorNumber, $this->errorString, 3);

        if ($this->errorNumber != 0) {
            throw new Exception('Error connecting to host: ' . $this->host . ' Error number: ' . $this->errorNumber . ' Error message: ' . $this->errorString);
        }

        if ($fs !== false) {
            @fwrite($fs, $this->request);

            while (!feof($fs)) {
                $this->response .= fgets($fs, $this->responseLength);
            }

            fclose($fs);
        }
    }

    /**
     *  Returns the server response text
     *
     * @return    string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *    Returns the error number
     *
     *    If there was no error, 0 will be returned.
     *
     * @return int
     */
    public function getErrorNumner()
    {
        return $this->errorNumber;
    }

    /**
     *    Returns the error string
     *
     *    If there was no error, an empty string will be returned.
     *
     * @return string
     */
    public function getErrorString()
    {
        return $this->errorString;
    }
}
