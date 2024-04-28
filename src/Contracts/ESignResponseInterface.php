<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/29
 * Time: 04:59
 */

namespace MaxSky\ESign\Contracts;

use Psr\Http\Message\StreamInterface;

interface ESignResponseInterface {

    /**
     * @param int             $status
     * @param StreamInterface $body
     */
    public function __construct(int $status, StreamInterface $body);

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface;

    /**
     * @return array
     */
    public function getJson(): array;
}
