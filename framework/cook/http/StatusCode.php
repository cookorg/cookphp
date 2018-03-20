<?php

namespace cook\http;

/**
 * HTTP状态代码
 * @author cookphp <admin@cookphp.org>
 */
interface StatusCode {

    /**
     * @var int
     */
    const STATUS_CONTINUE = 100;

    /**
     * @var int
     */
    const STATUS_SWITCHING_PROTOCOLS = 101;

    /**
     * @var int
     */
    const STATUS_PROCESSING = 102;
    // Successful 2xx
    /**
     * @var int
     */
    const STATUS_OK = 200;

    /**
     * @var int
     */
    const STATUS_CREATED = 201;

    /**
     * @var int
     */
    const STATUS_ACCEPTED = 202;

    /**
     * @var int
     */
    const STATUS_NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * @var int
     */
    const STATUS_NO_CONTENT = 204;

    /**
     * @var int
     */
    const STATUS_RESET_CONTENT = 205;

    /**
     * @var int
     */
    const STATUS_PARTIAL_CONTENT = 206;

    /**
     * @var int
     */
    const STATUS_MULTI_STATUS = 207;

    /**
     * @var int
     */
    const STATUS_ALREADY_REPORTED = 208;

    /**
     * @var int
     */
    const STATUS_IM_USED = 226;
    // Redirection 3xx
    /**
     * @var int
     */
    const STATUS_MULTIPLE_CHOICES = 300;

    /**
     * @var int
     */
    const STATUS_MOVED_PERMANENTLY = 301;

    /**
     * @var int
     */
    const STATUS_FOUND = 302;

    /**
     * @var int
     */
    const STATUS_SEE_OTHER = 303;

    /**
     * @var int
     */
    const STATUS_NOT_MODIFIED = 304;

    /**
     * @var int
     */
    const STATUS_USE_PROXY = 305;

    /**
     * @var int
     */
    const STATUS_RESERVED = 306;

    /**
     * @var int
     */
    const STATUS_TEMPORARY_REDIRECT = 307;

    /**
     * @var int
     */
    const STATUS_PERMANENT_REDIRECT = 308;
    // Client Errors 4xx
    /**
     * @var int
     */
    const STATUS_BAD_REQUEST = 400;

    /**
     * @var int
     */
    const STATUS_UNAUTHORIZED = 401;

    /**
     * @var int
     */
    const STATUS_PAYMENT_REQUIRED = 402;

    /**
     * @var int
     */
    const STATUS_FORBIDDEN = 403;

    /**
     * @var int
     */
    const STATUS_NOT_FOUND = 404;

    /**
     * @var int
     */
    const STATUS_METHOD_NOT_ALLOWED = 405;

    /**
     * @var int
     */
    const STATUS_NOT_ACCEPTABLE = 406;

    /**
     * @var int
     */
    const STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * @var int
     */
    const STATUS_REQUEST_TIMEOUT = 408;

    /**
     * @var int
     */
    const STATUS_CONFLICT = 409;

    /**
     * @var int
     */
    const STATUS_GONE = 410;

    /**
     * @var int
     */
    const STATUS_LENGTH_REQUIRED = 411;

    /**
     * @var int
     */
    const STATUS_PRECONDITION_FAILED = 412;

    /**
     * @var int
     */
    const STATUS_PAYLOAD_TOO_LARGE = 413;

    /**
     * @var int
     */
    const STATUS_URI_TOO_LONG = 414;

    /**
     * @var int
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * @var int
     */
    const STATUS_RANGE_NOT_SATISFIABLE = 416;

    /**
     * @var int
     */
    const STATUS_EXPECTATION_FAILED = 417;

    /**
     * @var int
     */
    const STATUS_IM_A_TEAPOT = 418;

    /**
     * @var int
     */
    const STATUS_MISDIRECTED_REQUEST = 421;

    /**
     * @var int
     */
    const STATUS_UNPROCESSABLE_ENTITY = 422;

    /**
     * @var int
     */
    const STATUS_LOCKED = 423;

    /**
     * @var int
     */
    const STATUS_FAILED_DEPENDENCY = 424;

    /**
     * @var int
     */
    const STATUS_UPGRADE_REQUIRED = 426;

    /**
     * @var int
     */
    const STATUS_PRECONDITION_REQUIRED = 428;

    /**
     * @var int
     */
    const STATUS_TOO_MANY_REQUESTS = 429;

    /**
     * @var int
     */
    const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * @var int
     */
    const STATUS_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    // Server Errors 5xx
    /**
     * @var int
     */
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var int
     */
    const STATUS_NOT_IMPLEMENTED = 501;

    /**
     * @var int
     */
    const STATUS_BAD_GATEWAY = 502;

    /**
     * @var int
     */
    const STATUS_SERVICE_UNAVAILABLE = 503;

    /**
     * @var int
     */
    const STATUS_GATEWAY_TIMEOUT = 504;

    /**
     * @var int
     */
    const STATUS_VERSION_NOT_SUPPORTED = 505;

    /**
     * @var int
     */
    const STATUS_VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * @var int
     */
    const STATUS_INSUFFICIENT_STORAGE = 507;

    /**
     * @var int
     */
    const STATUS_LOOP_DETECTED = 508;

    /**
     * @var int
     */
    const STATUS_NOT_EXTENDED = 510;

    /**
     * @var int
     */
    const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @var array
     * @link https://en.wikipedia.org/wiki/List_of_HTTP_status_codes 参考文献
     */
    const HTTP_CODE_TEXT = [
        self::STATUS_CONTINUE => 'Continue',
        self::STATUS_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_OK => 'OK',
        self::STATUS_CREATED => 'Created',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_NON_AUTHORITATIVE_INFORMATION => 'Non-authoritative Information',
        self::STATUS_NO_CONTENT => 'No Content',
        self::STATUS_RESET_CONTENT => 'Reset Content',
        self::STATUS_PARTIAL_CONTENT => 'Partial Content',
        self::STATUS_MULTI_STATUS => 'Multi-Status',
        self::STATUS_ALREADY_REPORTED => 'Already Reported',
        self::STATUS_IM_USED => 'IM Used',
        self::STATUS_MULTIPLE_CHOICES => 'Multiple Choices',
        self::STATUS_MOVED_PERMANENTLY => 'Moved Permanently',
        self::STATUS_FOUND => 'Found',
        self::STATUS_SEE_OTHER => 'See Other',
        self::STATUS_NOT_MODIFIED => 'Not Modified',
        self::STATUS_USE_PROXY => 'Use Proxy',
        self::STATUS_RESERVED => 'Unused',
        self::STATUS_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::STATUS_PERMANENT_REDIRECT => 'Permanent Redirect',
        self::STATUS_BAD_REQUEST => 'Bad Request',
        self::STATUS_UNAUTHORIZED => 'Unauthorized',
        self::STATUS_PAYMENT_REQUIRED => 'Payment Required',
        self::STATUS_FORBIDDEN => 'Forbidden',
        self::STATUS_NOT_FOUND => 'Not Found',
        self::STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::STATUS_NOT_ACCEPTABLE => 'Not Acceptable',
        self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::STATUS_REQUEST_TIMEOUT => 'Request Timeout',
        self::STATUS_CONFLICT => 'Conflict',
        self::STATUS_GONE => 'Gone',
        self::STATUS_LENGTH_REQUIRED => 'Length Required',
        self::STATUS_PRECONDITION_FAILED => 'Precondition Failed',
        self::STATUS_PAYLOAD_TOO_LARGE => 'Request Entity Too Large',
        self::STATUS_URI_TOO_LONG => 'Request-url Too Long',
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::STATUS_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        self::STATUS_EXPECTATION_FAILED => 'Expectation Failed',
        self::STATUS_IM_A_TEAPOT => 'I\'m a teapot',
        self::STATUS_MISDIRECTED_REQUEST => 'Misdirected Request',
        self::STATUS_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::STATUS_LOCKED => 'Locked',
        self::STATUS_FAILED_DEPENDENCY => 'Failed Dependency',
        self::STATUS_UPGRADE_REQUIRED => 'Upgrade Required',
        self::STATUS_PRECONDITION_REQUIRED => 'Precondition Required',
        self::STATUS_TOO_MANY_REQUESTS => 'Too Many Requests',
        self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::STATUS_NOT_IMPLEMENTED => 'Not Implemented',
        self::STATUS_BAD_GATEWAY => 'Bad Gateway',
        self::STATUS_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::STATUS_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::STATUS_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        self::STATUS_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::STATUS_INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::STATUS_LOOP_DETECTED => 'Loop Detected',
        self::STATUS_NOT_EXTENDED => 'Not Extended',
        self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required'
    ];

}
