<?php
/**
 1xx Info / Informational
 HTTP_INFO – Request received, continuing process. Indicates a provisional response, consisting only of the Status-Line and optional headers, and is terminated by an empty line.

 100 Continue – HTTP_CONTINUE
 101 Switching Protocols – HTTP_SWITCHING_PROTOCOLS
 102 Processing – HTTP_PROCESSING


 2xx Success / OK
 HTTP_SUCCESS – The action was successfully received, understood, and accepted. Indicates that the client’s request was successfully received, understood, and accepted.

 200 OK – HTTP_OK
 201 Created – HTTP_CREATED
 202 Accepted – HTTP_ACCEPTED
 203 Non-Authoritative Information – HTTP_NON_AUTHORITATIVE
 204 No Content – HTTP_NO_CONTENT
 205 Reset Content – HTTP_RESET_CONTENT
 206 Partial Content – HTTP_PARTIAL_CONTENT
 207 Multi-Status – HTTP_MULTI_STATUS


 3xx Redirect
 HTTP_REDIRECT – The client must take additional action to complete the request. Indicates that further action needs to be taken by the user-agent in order to fulfill the request. The action required may be carried out by the user agent without interaction with the user if and only if the method used in the second request is GET or HEAD. A user agent should not automatically redirect a request more than 5 times, since such redirections usually indicate an infinite loop.

 300 Multiple Choices – HTTP_MULTIPLE_CHOICES
 301 Moved Permanently – HTTP_MOVED_PERMANENTLY
 302 Found – HTTP_MOVED_TEMPORARILY
 303 See Other – HTTP_SEE_OTHER
 304 Not Modified – HTTP_NOT_MODIFIED
 305 Use Proxy – HTTP_USE_PROXY
 306 unused – UNUSED
 307 Temporary Redirect – HTTP_TEMPORARY_REDIRECT


 4xx Client Error
 HTTP_CLIENT_ERROR – The request contains bad syntax or cannot be fulfilled. Indicates case where client seems to have erred. Except when responding to a HEAD request, the server should include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition.

 400 Bad Request – HTTP_BAD_REQUEST
 401 Authorization Required – HTTP_UNAUTHORIZED
 402 Payment Required – HTTP_PAYMENT_REQUIRED
 403 Forbidden – HTTP_FORBIDDEN
 404 Not Found – HTTP_NOT_FOUND
 405 Method Not Allowed – HTTP_METHOD_NOT_ALLOWED
 406 Not Acceptable – HTTP_NOT_ACCEPTABLE
 407 Proxy Authentication Required – HTTP_PROXY_AUTHENTICATION_REQUIRED
 408 Request Time-out – HTTP_REQUEST_TIME_OUT
 409 Conflict – HTTP_CONFLICT
 410 Gone – HTTP_GONE
 411 Length Required – HTTP_LENGTH_REQUIRED
 412 Precondition Failed – HTTP_PRECONDITION_FAILED
 413 Request Entity Too Large – HTTP_REQUEST_ENTITY_TOO_LARGE
 414 Request-URI Too Large – HTTP_REQUEST_URI_TOO_LARGE
 415 Unsupported Media Type – HTTP_UNSUPPORTED_MEDIA_TYPE
 416 Requested Range Not Satisfiable – HTTP_RANGE_NOT_SATISFIABLE
 417 Expectation Failed – HTTP_EXPECTATION_FAILED
 418 I’m a teapot – UNUSED
 419 unused – UNUSED
 420 unused – UNUSED
 421 unused – UNUSED
 422 Unprocessable Entity – HTTP_UNPROCESSABLE_ENTITY
 423 Locked – HTTP_LOCKED
 424 Failed Dependency – HTTP_FAILED_DEPENDENCY
 425 No code – HTTP_NO_CODE
 426 Upgrade Required – HTTP_UPGRADE_REQUIRED


 5xx Server Error
 HTTP_SERVER_ERROR – The server failed to fulfill an apparently valid request. Indicate cases in which the server is aware that it has erred or is incapable of performing the request. Except when responding to a HEAD request, the server should include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition. User agents should display any included entity to the user. These response codes are applicable to any request method.

 500 Internal Server Error – HTTP_INTERNAL_SERVER_ERROR
 501 Method Not Implemented – HTTP_NOT_IMPLEMENTED
 502 Bad Gateway – HTTP_BAD_GATEWAY
 503 Service Temporarily Unavailable – HTTP_SERVICE_UNAVAILABLE
 504 Gateway Time-out – HTTP_GATEWAY_TIME_OUT
 505 HTTP Version Not Supported – HTTP_VERSION_NOT_SUPPORTED
 506 Variant Also Negotiates – HTTP_VARIANT_ALSO_VARIES
 507 Insufficient Storage – HTTP_INSUFFICIENT_STORAGE
 508 unused – UNUSED
 509 unused – UNUSED
 510 Not Extended – HTTP_NOT_EXTENDED
 /*===================================*/

//include_once( './functions_err.inc.php' );
//$error_code = ( !isset($_SERVER['REDIRECT_STATUS']) ? 'Undefined' : intval($_SERVER['REDIRECT_STATUS']) );
//send_error_email( $error_code );
?>
<html>
<head>
<title>HTTP ERROR: <?php echo $error_code; ?></title>
</head>
<body>
ERROR
</body>
</html>
