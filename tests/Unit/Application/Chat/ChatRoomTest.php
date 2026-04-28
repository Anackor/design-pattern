<?php

namespace App\Tests\Unit\Application\Chat;

use App\Application\Chat\ChatRoom;
use App\Application\Chat\User;
use PHPUnit\Framework\TestCase;

class ChatRoomTest extends TestCase
{
    public function testUsersCanSendMessagesThroughChatRoom(): void
    {
        $this->expectOutputRegex('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] Alice: Hello Bob!/');
        $this->expectOutputRegex('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] Bob: Hi Alice!/');

        $chatRoom = new ChatRoom();

        $alice = new User('Alice', $chatRoom);
        $bob = new User('Bob', $chatRoom);

        $alice->sendMessage('Hello Bob!');
        $bob->sendMessage('Hi Alice!');
    }
}
