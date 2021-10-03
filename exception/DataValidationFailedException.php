<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\exception;

use yii\web\HttpException;

/**
 * BadRequestHttpException represents a "Bad Request" HTTP exception with status code 400.
 *
 * Use this exception to represent a generic client error. In many cases, there
 * may be an HTTP exception that more precisely describes the error. In that
 * case, consider using the more precise exception to provide the user with
 * additional information.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.5.1
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DataValidationFailedException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(422, $message, $code, $previous);
    }
}
