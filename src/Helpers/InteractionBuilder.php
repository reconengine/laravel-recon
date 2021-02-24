<?php


namespace LaravelMl\Helpers;


use Carbon\Carbon;
use LaravelMl\Api\ApiFacade;

class InteractionBuilder
{
    /**
     * @var string The current session id (usually session()->getId()). This is used to bridge interactions for users before they are signed in.
     */
    protected $sessionId;

    /**
     * @var int The timestamp the interaction occurred at.
     */
    // efff, this is only public because it is hard to assert it's validity.
    public $timestamp;

    /**
     * @var null|string The type of interaction. Ex: 'purchase', 'click', 'view', 'review'.
     */
    protected $action = null;

    /**
     * @var null|string An optional value associated with this interaction.
     */
    protected $value = null;

    /**
     * @var null|string|int The item id being interacted with.
     */
    protected $itemId = null;

    /**
     * @var null|string|int The user performing the interaction.
     */
    protected $userId = null;

    /**
     * @var null|array An optional array of item ID's that the user viewed at the same time as the one they interacted with. Ex: [5, 3, 1]
     */
    protected $impressions = null;

    /**
     * @var null|array JSON array matching with the properties in the InteractionSchemaBuilder.
     */
    protected $metadata = null;

    /**
     * @var null|string The recommendation ID (returned from a recommendation request) that this interaction was taken on.
     */
    protected $recommendationId = null;


    /**
     * @param $action string The type of interaction. Ex: 'purchase', 'click', 'view', 'review'.
     * @param $sessionId string Session id of this interaction (defaults to session()->getId()). This is used to bridge interactions for users before they are signed in.
     * @param $occurredAt string|DateTimeInterface|null Time the event occurred at (default is the current time)
     */
    public function __construct($action, $sessionId = null, $occurredAt = null)
    {
        $this->action = $action;
        $this->sessionId = $sessionId ?? session()->getId();
        $this->occurredAt = $occurredAt ? Carbon::parse($occurredAt)->timestamp : now()->timestamp;
    }

    /**
     * @param $action string The type of interaction. Ex: 'purchase', 'click', 'view', 'review'.
     * @param $sessionId string Session id of this interaction.
     * @param $occurredAt string|DateTimeInterface|null Time the event occurred at (default is the current time)
     * @return static
     */
    public static function make($action, $sessionId = null, $occurredAt = null)
    {
        return new static($action, $sessionId, $occurredAt);
    }

    /**
     * @return array
     */
    public function toJson()
    {
        return [
            [ // yes, this needs to be an array of array.
                'session_id' => $this->sessionId,
                'type' => $this->action,
                'timestamp' => $this->timestamp,
                'value' => $this->value,
                'iid' => $this->itemId,
                'uid' => $this->userId,
                'impressions' => $this->impressions,
                'metadata' => $this->metadata,
                'recommendation_id' => $this->recommendationId,
            ],
        ];
    }

    /**
     * @param mixed $sessionId
     * @return InteractionBuilder
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @param mixed $timestamp
     * @return InteractionBuilder
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @param mixed $action
     * @return InteractionBuilder
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param null $value
     * @return InteractionBuilder
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param null $itemId
     * @return InteractionBuilder
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * @param null $userId
     * @return InteractionBuilder
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param null $impressions
     * @return InteractionBuilder
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @param null $metadata
     * @return InteractionBuilder
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @param null $recommendationId
     * @return InteractionBuilder
     */
    public function setRecommendationId($recommendationId)
    {
        $this->recommendationId = $recommendationId;
        return $this;
    }

    public function send()
    {
        return ApiFacade::putInteractions($this);
    }
}
