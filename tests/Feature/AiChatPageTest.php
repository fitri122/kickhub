<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiChatPageTest extends TestCase
{
    public function test_ai_chat_preview_is_public_and_shows_chat_prompts(): void
    {
        $this->get(route('ai-chat'))
            ->assertOk()
            ->assertSee('KickHub AI')
            ->assertSee('Google Gemini AI')
            ->assertSee('Open to everyone · No sign-in required')
            ->assertSee('Boleh terangkan peraturan offside dengan ringkas?')
            ->assertSee('no messages are sent to an AI service');
    }
}
