<?php

namespace MaxSky\ESign\Common;

use MaxSky\ESign\Contracts\ESignResponseInterface;
use MaxSky\ESign\Exceptions\ESignResponseException;
use Psr\Http\Message\StreamInterface;

/**
 * ESign 响应类
 *
 * @author   澄泓
 * @date     2022/08/18 9:51
 *
 * @modifier Max Sky
 * @date     2024/04/25 5:07
 */
class ESignResponse implements ESignResponseInterface {

    private $status;
    private $body;
    private $json = [];
    private $data = null;
    private $code;
    private $message;

    /**
     * @param int             $status
     * @param StreamInterface $body
     *
     * @throws ESignResponseException
     */
    public function __construct(int $status, StreamInterface $body) {
        $this->status = $status;
        $this->body = $body;

        $this->setJsonData($body);
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getJson(): array {
        return $this->json;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getCode(): int {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @param StreamInterface $body
     *
     * @return void
     * @throws ESignResponseException
     */
    private function setJsonData(StreamInterface $body) {
        $json = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->json = $json;
            $this->code = $json['code'] ?? -1;
            $this->message = $json['message'] ?? '未知错误';

            if ($this->code) { // success: 0
                throw new ESignResponseException(
                    "错误码：{$this->code}，{$this->message}。如需帮助请前往：https://open.esign.cn/tools/error-code 进行查询。", $this->code
                );
            } else {
                $this->data = $json['data'];
            }
        }
    }
}
