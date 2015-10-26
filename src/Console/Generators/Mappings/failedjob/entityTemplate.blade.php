namespace {{$namespace}};

class FailedJob
{
    protected $id;
    protected $connection;
    protected $queue;
    protected $payload;
    protected $failed_at;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function getFailedAt()
    {
        return $this->failed_at;
    }

}