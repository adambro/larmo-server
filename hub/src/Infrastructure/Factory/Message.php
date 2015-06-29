<?php

namespace FP\Larmo\Infrastructure\Factory;

use FP\Larmo\Domain\Entity;
use FP\Larmo\Domain\ValueObject\Author;
use FP\Larmo\Domain\ValueObject\UniqueId;
use FP\Larmo\Domain\Service\UniqueIdGenerator;

class Message
{
    private $uniqueId;

    public function __construct(UniqueId $uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public function fromArray($message)
    {
        $type = isset($message['type']) ? $message['type'] : '';
        $timestamp = isset($message['type']) ? $message['type'] : '';
        $body = isset($message['body']) ? $message['body'] : '';
        $extras = isset($message['extras']) ? $message['extras'] : '';

        if (isset($message['author'])) {
            $authorFullName = isset($message['author']['name']) ? $message['author']['name'] : '';
            $authorNickName = isset($message['author']['login']) ? $message['author']['login'] : '';
            $authorEmail = isset($message['author']['email']) ? $message['author']['email'] : '';

            $author = new Author($authorFullName, $authorNickName, $authorEmail);
        } else {
            $author = new Author();
        }

        return new Entity\Message($type, $timestamp, $author, $this->uniqueId, $body, $extras);
    }
}