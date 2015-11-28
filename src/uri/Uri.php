<?php
/**
 * This file is part of SoloProyectos common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/uri/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/uri
 */
namespace soloproyectos\uri;
use soloproyectos\uri\exception\UriException;
use soloproyectos\arr\Arr;
use soloproyectos\text\Text;

/**
 * Class Uri.
 *
 * @package Uri
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos-php/uri/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/soloproyectos-php/uri
 */
class Uri
{
    /**
     * Original URI path.
     * @var string
     */
    private $_uri = "";
    
    /**
     * URI Scheme.
     * @var string
     */
    private $_scheme = "";
    
    /**
     * URI Host.
     * @var string
     */
    private $_host = "";
    
    /**
     * URI Port.
     * @var string
     */
    private $_port = "";
    
    /**
     * URI Username.
     * @var string
     */
    private $_username = "";
    
    /**
     * URI Password.
     * @var string
     */
    private $_password = "";
    
    /**
     * URI Path.
     * @var string
     */
    private $_path = "";
    
    /**
     * URI Query.
     * @var string
     */
    private $_query = "";
    
    /**
     * URI Fragment.
     * @var string
     */
    private $_fragment = "";
    
    /**
     * Constructor.
     * 
     * Example:
     * ```PHP
     * $uri = new Uri("http://user:pass@host:8080/path/to/url?aaa=1&bbb=2#tagname");
     * echo $uri->getScheme() . "\n";
     * echo $uri->getHost() . "\n";
     * echo $uri->getPort() . "\n";
     * echo $uri->getUsername() . "\n";
     * echo $uri->getPassword() . "\n";
     * echo $uri->getPath() . "\n";
     * echo $uri->getQuery() . "\n";
     * echo $uri->getFragment() . "\n";
     * ```
     * 
     * @param string $uri URI
     */
    public function __construct($uri)
    {
        $this->_uri = $uri;
        
        // parses the uri
        $urlInfo = parse_url($uri);
        if ($urlInfo === false) {
            throw new UriException("The given URI is not well formed");
        }
        
        // gets the uri info
        $this->_scheme = Arr::get($urlInfo, "scheme");
        $this->_host = Arr::get($urlInfo, "host");
        $this->_port = Arr::get($urlInfo, "port");
        $this->_username = Arr::get($urlInfo, "user");
        $this->_password = Arr::get($urlInfo, "pass");
        $this->_path = Arr::get($urlInfo, "path");
        $this->_query = Arr::get($urlInfo, "query");
        $this->_fragment = Arr::get($urlInfo, "fragment");
    }
    
    /**
     * Gets the URI scheme.
     * 
     * @return string
     */
    public function getScheme()
    {
        return $this->_scheme;
    }
    
    /**
     * Gets the URI host.
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }
    
    /**
     * Gets the URI port.
     * 
     * @return string
     */
    public function getPort()
    {
        return $this->_port;
    }
    
    /**
     * Gets the URI username.
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }
    
    /**
     * Gets the URI password.
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * Gets the URI path.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Gets the URI query.
     * 
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * Gets the URI fragment.
     * 
     * @return string
     */
    public function getFragment()
    {
        return $this->_fragment;
    }
    
    /**
     * Is a relative URI?
     * 
     * @return boolean
     */
    public function isRelative()
    {
        return Text::isEmpty($this->_host) && strpos($this->_path, "/") !== 0;
    }
    
    /**
     * Is the current URI under the given URI?
     * 
     * Example:
     * ```php
     * // the following code returns 'true'
     * $uri1 = new Uri("http://www.hostname.com");
     * $uri2 = new Uri("http://www.hostname.com/path/to/page");
     * echo $uri2->isUnder($uri1);
     * 
     * // the following code returns 'false'
     * $uri1 = new Uri("/home/john");
     * $uri2 = new Uri("/another/path");
     * $uri2->isUnder($uri1);
     * ```
     * 
     * @param Uri $absoluteUri Absolute URI
     * 
     * @return boolean
     */
    public function isUnder($absoluteUri)
    {
        return strpos($this->_getUriPath(), $absoluteUri->__toString()) === 0;
    }
    
    /**
     * Gets the absolute URI.
     * 
     * This method gets the absolute URI in relation to another absolute URI.
     * 
     * For example:
     * ```php
     * // prints '/home/path/to/folder'
     * $uri1 = new Uri("/home/john");
     * $uri2 = new Uri("path/to/folder");
     * echo $uri2->getAbsoluteUri($uri1);
     * 
     * // prints 'http://hostname.com/root/path/to/page'
     * $uri1 = new Uri("http://hostname.com/root");
     * $uri2 = new Uri("path/to/page");
     * echo $uri2->getAbsoluteUri($uri1);
     * 
     * // throws an error, as '/path/to/page' is not an relative URI
     * $uri1 = new Uri("http://hostname.com/root");
     * $uri2 = new Uri("/path/to/page");
     * echo $uri2->getAbsoluteUri($uri1);
     * 
     * // throws an error, as 'dirname/' is not an absolute URI
     * $uri1 = new Uri("dirname/");
     * $uri2 = new Uri("path/to/page");
     * echo $uri2->getAbsoluteUri($uri1);
     * ```
     * 
     * @param Uri $absoluteUri Absolute URI
     * 
     * @return Uri
     */
    public function getAbsoluteUri($absoluteUri)
    {
        // is the given URI an absolute URI?
        if ($absoluteUri->isRelative()) {
            throw new UriException("The given URI is not absolute");
        }
        
        // is the given URI a relative URI?
        if (!$this->isRelative()) {
            throw new UriException("The current URI is not relative");
        }
        
        return new Uri($absoluteUri->_getUriPath() . "/" . ltrim($this->__toString(), "/"));
    }
    
    /**
     * Gets the relative URI.
     * 
     * This method gets the relative URI in relation to another absolute URI.
     * 
     * Example:
     * ```php
     * // prints 'path/to/file'
     * $uri1 = new Uri("/home/john");
     * $uri2 = new Uri("/home/john/path/to/file");
     * echo $uri2->getRelativeUri($uri1);
     * 
     * // prints 'path/to/page'
     * $uri1 = new Uri("http://john:smith@hostname.com/docroot");
     * $uri2 = new Uri("http://john:smith@hostname.com/docroot/path/to/page");
     * echo $uri2->getRelativeUri($uri1);
     * 
     * // throws an error, as 'docroot' is not an absolute URI
     * $uri1 = new Uri("docroot");
     * $uri2 = new Uri("http://john:smith@hostname.com/docroot/path/to/page");
     * echo $uri2->getRelativeUri($uri1);
     * 
     * // throws an error, as '/home/betty/dir/subdir' is not under '/home/john'
     * $uri1 = new Uri("/home/john");
     * $uri2 = new Uri("/home/betty/dir/subdir");
     * echo $uri2->getRelativeUri($uri1);
     * ```
     * 
     * @param Uri $absoluteUri Absolute URI
     * 
     * @return Uri
     */
    public function getRelativeUri($absoluteUri)
    {
        // is the given URI an absolute URI?
        if ($absoluteUri->isRelative()) {
            throw new UriException("The given URI is not absolute");
        }
        
        // is the current URI an absolute URI?
        if ($this->isRelative()) {
            throw new UriException("The current URI is not absolute");
        }
        
        // is the current URI 'under' the given URI?
        if (!$this->isUnder($absoluteUri)) {
            throw new UriException("The current URI is not under the given URI");
        }
        
        return new Uri(substr($this->__toString(), strlen($absoluteUri->_getUriPath()) + 1));
    }
    
    /**
     * Gets the current URI path.
     * 
     * This method gets the current URI excluding the query and the fragment parts.
     * 
     * For example,
     * http://hostname.com/path/to/resource?param1=1&param2=2 --> http://hostname.com/path/to/resource
     * 
     * @return string
     */
    private function _getUriPath()
    {
        // user info part
        $userInfo = $this->_username;
        if (!Text::isEmpty($this->_password)) {
            $userInfo .= ":" . $this->_password;
        }
        if (!Text::isEmpty($userInfo)) {
            $userInfo = $userInfo . "@";
        }
        
        $scheme = Text::isEmpty($this->_scheme)? "" : $this->_scheme . ":";
        $port = Text::isEmpty($this->_port)? "": ":" . $this->_port;
        $authority = Text::isEmpty($this->_host)? "": "//" . $userInfo . $this->_host . $port;
        return $scheme . $authority . rtrim($this->_path, "/");
    }
    
    /**
     * Gets a string representation of the URI.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->_uri;
    }
}
