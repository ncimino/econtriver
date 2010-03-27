<?php
class HTTPErrorDecoder {
	private $errorCode;
	private $errorTitle;
	private $errorType;
	private $errorDesc;

	function __construct() { }

	function setErrorCode($error) { $this->errorCode = $error; }
	function setErrorTitle($error) { $this->errorTitle = $error; }
	function setErrorType($error) { $this->errorType = $error; }
	function setErrorDesc($error) { $this->errorDesc = $error; }

	function getErrorCode() { return $this->errorCode; }
	function setErrorTitle() { return $this->errorTitle; }
	function setErrorType() { return $this->errorType; }
	function setErrorDesc() { return $this->errorDesc; }

	function decodeError($error=NULL) {
		if (!empty($error)) { $this->setErrorCode($error); }

		if ($this->getErrorCode() >= 100 and $this->getErrorCode() < 200 ) {
			$this->setErrorType("HTTP_INFO");
		} elseif ($this->getErrorCode() >= 200 and $this->getErrorCode() < 300 ) {
			$this->setErrorType("HTTP_SUCCESS");
		} elseif ($this->getErrorCode() >= 300 and $this->getErrorCode() < 400 ) {
			$this->setErrorType("HTTP_REDIRECT");
		} elseif ($this->getErrorCode() >=400 and $this->getErrorCode() < 500 ) {
			$this->setErrorType("HTTP_CLIENT_ERROR");
		} elseif ($this->getErrorCode() >= 500 and $this->getErrorCode() < 600 ) {
			$this->setErrorType("HTTP_SERVER_ERROR");
		}

		switch ($this->getErrorCode()) {
			case 100:
				$this->setErrorTitle("Continue");
				$this->setErrorDesc("This means that the server has received the request headers, and that the client should proceed to send the request body (in case of a request which needs to be sent; for example, a POST request). If the request body is large, sending it to a server when a request has already been rejected based upon inappropriate headers is inefficient. To have a server check if the request could be accepted based on the requests headers alone, a client must send Expect: 100-continue as a header in its initial request (see RFC 2616 14.20 Expect header) and check if a 100 Continue status code is received in response before continuing (or receive 417 Expectation Failed and not continue).");
				break;
			case 101:
				$this->setErrorTitle("Switching Protocols");
				$this->setErrorDesc("This means the requester has asked the server to switch protocols and the server is acknowledging that it will do so.[3]");
				break;
			case 102:
				$this->setErrorTitle("Processing");
				$this->setErrorDesc("(WebDAV) – (RFC 2518 )");
				break;
			case 200:
				$this->setErrorTitle("OK");
				$this->setErrorDesc("Standard response for successful HTTP requests. The actual response will depend on the request method used. In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request the response will contain an entity describing or containing the result of the action.");
				break;
			case 201:
				$this->setErrorTitle("Created");
				$this->setErrorDesc("The request has been fulfilled and resulted in a new resource being created.");
				break;
			case 202:
				$this->setErrorTitle("Accepted");
				$this->setErrorDesc("The request has been accepted for processing, but the processing has not been completed. The request might or might not eventually be acted upon, as it might be disallowed when processing actually takes place.");
				break;
			case 203:
				$this->setErrorTitle("Non-Authoritative Information");
				$this->setErrorDesc("The server successfully processed the request, but is returning information that may be from another source.");
				break;
			case 204:
				$this->setErrorTitle("No Content");
				$this->setErrorDesc("The server successfully processed the request, but is not returning any content.");
				break;
			case 205:
				$this->setErrorTitle("Reset Content");
				$this->setErrorDesc("The server successfully processed the request, but is not returning any content. Unlike a 204 response, this response requires that the requester reset the document view.");
				break;
			case 206:
				$this->setErrorTitle("Partial Content");
				$this->setErrorDesc("The server is delivering only part of the resource due to a range header sent by the client. This is used by tools like wget to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams.");
				break;
			case 207:
				$this->setErrorTitle("Multi-Status");
				$this->setErrorDesc("(WebDAV) – The message body that follows is an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.");
				break;
			case 226:
				$this->setErrorTitle("IM Used");
				$this->setErrorDesc("The server has fulfilled a GET request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance. The actual current instance might not be available except by combining this response with other previous or future responses, as appropriate for the specific instance-manipulation(s).");
				break;
			case 300:
				$this->setErrorTitle("Multiple Choices");
				$this->setErrorDesc("Indicates multiple options for the resource that the client may follow. It, for instance, could be used to present different format options for video, list files with different extensions, or word sense disambiguation.");
				break;
			case 301:
				$this->setErrorTitle("Moved Permanently");
				$this->setErrorDesc("This and all future requests should be directed to the given URI.");
				break;
			case 302:
				$this->setErrorTitle("Found");
				$this->setErrorDesc("This is the most popular redirect code[citation needed], but also an example of industrial practice contradicting the standard. HTTP/1.0 specification (RFC 1945 ) required the client to perform a temporary redirect (the original describing phrase was “Moved Temporarily”), but popular browsers implemented it as a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to disambiguate between the two behaviours. However, the majority of Web applications and frameworks still use the 302 status code as if it were the 303.");
				break;
			case 303:
				$this->setErrorTitle("See Other");
				$this->setErrorDesc("The response to the request can be found under another URI using a GET method. When received in response to a PUT, it should be assumed that the server has received the data and the redirect should be issued with a separate GET message.");
				break;
			case 304:
				$this->setErrorTitle("Not Modified");
				$this->setErrorDesc("Indicates the resource has not been modified since last requested. Typically, the HTTP client provides a header like the If-Modified-Since header to provide a time against which to compare. Utilizing this saves bandwidth and reprocessing on both the server and client, as only the header data must be sent and received in comparison to the entirety of the page being re-processed by the server, then resent using more bandwidth of the server and client.");
				break;
			case 305:
				$this->setErrorTitle("Use Proxy");
				$this->setErrorDesc("Many HTTP clients (such as Mozilla[4] and Internet Explorer) do not correctly handle responses with this status code, primarily for security reasons.");
				break;
			case 306:
				$this->setErrorTitle("Switch Proxy");
				$this->setErrorDesc("No longer used.");
				break;
			case 307:
				$this->setErrorTitle("Temporary Redirect");
				$this->setErrorDesc("In this occasion, the request should be repeated with another URI, but future requests can still use the original URI. In contrast to 303, the request method should not be changed when reissuing the original request. For instance, a POST request must be repeated using another POST request.");
				break;
			case 400:
				$this->setErrorTitle("Bad Request");
				$this->setErrorDesc("The request contains bad syntax or cannot be fulfilled.");
				break;
			case 401:
				$this->setErrorTitle("Unauthorized");
				$this->setErrorDesc("Similar to 403 Forbidden, but specifically for use when authentication is possible but has failed or not yet been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. See Basic access authentication and Digest access authentication.");
				break;
			case 402:
				$this->setErrorTitle("Payment Required");
				$this->setErrorDesc("The original intention was that this code might be used as part of some form of digital cash or micropayment scheme, but that has not happened, and this code has never been used.");
				break;
			case 403:
				$this->setErrorTitle("Forbidden");
				$this->setErrorDesc("The request was a legal request, but the server is refusing to respond to it. Unlike a 401 Unauthorized response, authenticating will make no difference.");
				break;
			case 404:
				$this->setErrorTitle("Not Found");
				$this->setErrorDesc("The requested resource could not be found but may be available again in the future. Subsequent requests by the client are permissible.");
				break;
			case 405:
				$this->setErrorTitle("Method Not Allowed");
				$this->setErrorDesc("A request was made of a resource using a request method not supported by that resource; for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.");
				break;
			case 406:
				$this->setErrorTitle("Not Acceptable");
				$this->setErrorDesc("The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.");
				break;
			case 407:
				$this->setErrorTitle("Proxy Authentication Required");
				$this->setErrorDesc("Required");
				break;
			case 408:
				$this->setErrorTitle("Request Timeout");
				$this->setErrorDesc("The server timed out waiting for the request.");
				break;
			case 409:
				$this->setErrorTitle("Conflict");
				$this->setErrorDesc("Indicates that the request could not be processed because of conflict in the request, such as an edit conflict.");
				break;
			case 410:
				$this->setErrorTitle("Gone");
				$this->setErrorDesc("Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed; however, it is not necessary to return this code and a 404 Not Found can be issued instead. Upon receiving a 410 status code, the client should not request the resource again in the future. Clients such as search engines should remove the resource from their indexes.");
				break;
			case 411:
				$this->setErrorTitle("Length Required");
				$this->setErrorDesc("The request did not specify the length of its content, which is required by the requested resource.");
				break;
			case 412:
				$this->setErrorTitle("Precondition Failed");
				$this->setErrorDesc("The server does not meet one of the preconditions that the requester put on the request.");
				break;
			case 413:
				$this->setErrorTitle("Request Entity Too Large");
				$this->setErrorDesc("The request is larger than the server is willing or able to process.");
				break;
			case 414:
				$this->setErrorTitle("Request-URI Too Long");
				$this->setErrorDesc("The URI provided was too long for the server to process.");
				break;
			case 415:
				$this->setErrorTitle("Unsupported Media Type");
				$this->setErrorDesc("The request did not specify any media types that the server or resource supports. For example the client specified that an image resource should be served as image/svg+xml, but the server cannot find a matching version of the image.");
				break;
			case 416:
				$this->setErrorTitle("Requested Range Not Satisfiable");
				$this->setErrorDesc("The client has asked for a portion of the file, but the server cannot supply that portion (for example, if the client asked for a part of the file that lies beyond the end of the file).");
				break;
			case 417:
				$this->setErrorTitle("Expectation Failed");
				$this->setErrorDesc("The server cannot meet the requirements of the Expect request-header field.");
				break;
			case 418:
				$this->setErrorTitle("I’m a teapot");
				$this->setErrorDesc("The HTCPCP server is a teapot. The responding entity MAY be short and stout. Defined by the April Fools specification RFC 2324. See Hyper Text Coffee Pot Control Protocol for more information.");
				break;
			case 422:
				$this->setErrorTitle("Unprocessable Entity");
				$this->setErrorDesc("(WebDAV) (RFC 4918 ) – The request was well-formed but was unable to be followed due to semantic errors.");
				break;
			case 423:
				$this->setErrorTitle("Locked");
				$this->setErrorDesc("(WebDAV) (RFC 4918 ) – The resource that is being accessed is locked");
				break;
			case 424:
				$this->setErrorTitle("Failed Dependency");
				$this->setErrorDesc("(WebDAV) (RFC 4918 ) – The request failed due to failure of a previous request (e.g. a PROPPATCH).");
				break;
			case 425:
				$this->setErrorTitle("Unordered Collection");
				$this->setErrorDesc("Defined in drafts of WebDav Advanced Collections, but not present in “Web Distributed Authoring and Versioning (WebDAV) Ordered Collections Protocol” (RFC 3648).");
				break;
			case 426:
				$this->setErrorTitle("Upgrade Required");
				$this->setErrorDesc("(RFC 2817 ) – The client should switch to TLS/1.0.");
				break;
			case 449:
				$this->setErrorTitle("Retry With");
				$this->setErrorDesc("A Microsoft extension. The request should be retried after doing the appropriate action.");
				break;
			case 500:
				$this->setErrorTitle("Internal Server Error");
				$this->setErrorDesc("A generic error message, given when no more specific message is suitable.");
				break;
			case 501:
				$this->setErrorTitle("Not Implemented");
				$this->setErrorDesc("The server either does not recognise the request method, or it lacks the ability to fulfil the request.");
				break;
			case 502:
				$this->setErrorTitle("Bad Gateway");
				$this->setErrorDesc("The server was acting as a gateway or proxy and received an invalid response from the upstream server.");
				break;
			case 503:
				$this->setErrorTitle("Service Unavailable");
				$this->setErrorDesc("The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.");
				break;
			case 504:
				$this->setErrorTitle("Gateway Timeout");
				$this->setErrorDesc("The server was acting as a gateway or proxy and did not receive a timely request from the upstream server.");
				break;
			case 505:
				$this->setErrorTitle("HTTP Version Not Supported");
				$this->setErrorDesc("The server does not support the HTTP protocol version used in the request.");
				break;
			case 506:
				$this->setErrorTitle("Variant Also Negotiates");
				$this->setErrorDesc("(RFC 2295 ) – Transparent content negotiation for the request, results in a circular reference.");
				break;
			case 507:
				$this->setErrorTitle("Insufficient Storage");
				$this->setErrorDesc("(WebDAV) (RFC 4918 )");
				break;
			case 509:
				$this->setErrorTitle("Bandwidth Limit Exceeded");
				$this->setErrorDesc("(Apache bw/limited extension) – This status code, while used by many servers, is not specified in any RFCs.");
				break;
			case 510:
				$this->setErrorTitle("Not Extended");
				$this->setErrorDesc("(RFC 2774 ) – Further extensions to the request are required for the server to fulfil it.");
				break;
		}
	}
}
?>