<?php

declare (strict_types=1);
namespace RectorPrefix20211123\Symplify\PackageBuilder\ValueObject;

/**
 * @api
 */
final class MethodName
{
    /**
     * @var string
     */
    public const CONSTRUCTOR = '__construct';
    /**
     * @var string
     */
    public const SET_UP = 'setUp';
    /**
     * @var string
     */
    public const INVOKE = '__invoke';
}