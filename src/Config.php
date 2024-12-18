<?php

namespace ivoglent\yii2\apm;

use Elastic\Apm\PhpAgent\Interfaces\ConfigInterface;
use Elastic\Apm\PhpAgent\Model\Agent as AgentConfig;
use Elastic\Apm\PhpAgent\Model\Framework;
use Elastic\Apm\PhpAgent\Model\User;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class Config implements ConfigInterface
{
    public const AGENT_NAME = 'APM PHP Agent';
    public const AGENT_VERSION = '1.0.0';
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $secret_token;

    /**
     * @var Agent
     */
    private $agent;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $server_url;

    /**
     * @var Framework
     */
    private $framework;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var array
     */
    private $metadata = [];

    /**
     * Config constructor.
     * @param string $name
     * @param string $version
     * @param string $server_url
     * @param string $secret_token
     * @param array|null $metadata
     * @param Framework $framework
     * @param User $user
     * @param ClientInterface $client
     */
    public function __construct(string $name, string $version, string $server_url, ?string $secret_token = null, ?array $metadata = [], ?Framework $framework = null, ?User $user = null, ?ClientInterface $client = null)
    {
        $this->name = $name;
        $this->version = $version;
        if (null !== $secret_token) {
            $this->secret_token = $secret_token;
        }
        if (null !== $client) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
        if (null !== $server_url) {
            $this->server_url = $server_url;
        }
        if (null !== $framework) {
            $this->framework = $framework;
        }

        if (null !== $user) {
            $this->user = $user;
        }

        if (!empty($metadata)) {
            $this->metadata = $metadata;
        }

        $this->agent = new AgentConfig([
            'name' => self::AGENT_NAME,
            'version' => self::AGENT_VERSION,
        ]);
    }

    /**
     * Get application name
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->name;
    }

    /**
     * Get application current version
     *
     * @return string
     */
    public function getAppVersion(): string
    {
        return $this->version;
    }

    /**
     * Get access token to connect to the server
     *
     * @return string|null
     */
    public function getSecretToken(): ?string
    {
        return $this->secret_token;
    }

    /**
     * Get base url of APM server
     *
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->server_url;
    }

    /**
     * Get client which will send the request to APM server
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Set client to config
     *
     * @param ClientInterface $client
     * @return mixed
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get framework information
     *
     * @return Framework
     */
    public function getFramework(): ?Framework
    {
        return $this->framework;
    }

    /**
     * Set using framework to config
     *
     * @param Framework $framework
     * @return mixed
     */
    public function setFramework(Framework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Get authenticated user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set authenticated user to config
     *
     * @param User $user
     * @return mixed
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return AgentConfig
     */
    public function getAgentConfig(): AgentConfig
    {
        return $this->agent;
    }
    /**
     * Get registered metadata for this agent
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set meta data for the agent
     *
     * @param array $data
     * @return mixed
     */
    public function setMetadata(array $data)
    {
        $this->metadata = $data;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

}
