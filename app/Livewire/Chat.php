<?php

namespace App\Livewire;

use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Component;

class Chat extends Component
{
    public $prompt;
    public $output;
    public $messages = [];

    public function generateOutput()
    {
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->prompt,
        ];

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $this->messages,
        ]);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $result['choices'][0]['message']['content'],
        ];
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
