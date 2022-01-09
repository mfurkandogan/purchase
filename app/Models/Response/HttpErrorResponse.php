<?php

namespace App\Models\Response;

class HttpErrorResponse
{

    protected int $status;

    protected int $code;

    private array $message = [];

    /**
     * @return array
     */
    public function getMessage(): array
    {
        return $this->message;
    }

    /**
     * @param array $message
     * @return $this
     */
    public function setMessage(array $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return call_user_func('get_object_vars', $this);
    }
}
