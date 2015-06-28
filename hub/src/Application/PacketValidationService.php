<?php

namespace FP\Larmo\Application;

use FP\Larmo\Application\Contract\JsonSchemaValidation;
use FP\Larmo\Domain\Service\AuthInfoInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

final class PacketValidationService
{
    private $schema;
    private $packet;
    private $validator;
    private $authinfo;
    private $plugins;
    private $errors = [];

    public function __construct(
        JsonSchemaValidation $schemaValidation,
        AuthInfoInterface $authinfo,
        PluginService $plugins
    ) {
        $this->validator = $schemaValidation;
        $this->authinfo = $authinfo;
        $this->plugins = $plugins;
    }

    public function setSchemaFromFile($schemaFilePath)
    {
        if (!file_exists($schemaFilePath) || !is_readable($schemaFilePath)) {
            throw new Exception('Can\'t read the specified file: ' . $schemaFilePath);
        }

        $this->schema = json_decode(file_get_contents($schemaFilePath));

        return $this;
    }

    public function setPacket($packet)
    {
        $this->packet = $packet;

        return $this;
    }

    public function isValid()
    {
        $this->errors = [];
        $this->validator->check($this->packet, $this->schema);

        if (!$this->validator->isValid()) {
            $this->errors = $this->validator->getErrors();

            return false;
        }

        if (!$this->authinfo->validate(get_object_vars($this->packet->metadata->authinfo))) {
            $this->errors[] = [
                'property' => 'metadata.authinfo',
                'message' => 'the authinfo is invalid'
            ];

            return false;
        }

        if (!$this->plugins->checkPluginIsRegistered($this->packet->metadata->source)) {
            $this->errors[] = [
                'property' => 'metadata.source',
                'message' => 'the source is invalid'
            ];

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}