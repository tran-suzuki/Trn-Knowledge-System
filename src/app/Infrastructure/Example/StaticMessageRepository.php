<?php

namespace App\Infrastructure\Example;

use App\Domain\Example\Message;

class StaticMessageRepository
{
    public function get(): Message
    {
        return new Message("Hello from DDD layer!");
    }
}
