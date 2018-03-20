<?php

namespace cook\http;

/**
 * 传输​​协议
 * @author cookphp <admin@cookphp.org>
 */
interface RequestMethod {

    /**
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    const METHOD_PURGE = 'PURGE';

    /**
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    /**
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';

}
