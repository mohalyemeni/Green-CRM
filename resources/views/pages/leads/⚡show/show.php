<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Enums\CommentType;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('ملف العميل المحتمل')] class extends Component
{
    public Lead $lead;
    public string $commentBody = '';
    public int $commentType = CommentType::NOTE->value;

    public function mount(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function addComment()
    {
        $this->validate([
            'commentBody' => 'required|string|min:2|max:2000',
            'commentType' => 'required|integer',
        ]);

        $this->lead->comments()->create([
            'body'       => $this->commentBody,
            'type'       => $this->commentType,
            'user_id'    => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        $this->reset('commentBody');
        $this->commentType = CommentType::NOTE->value;

        // refresh computed
        unset($this->comments);

        $this->dispatch('notify', type: 'success', message: 'تم إضافة التعليق بنجاح.');
    }

    public function deleteComment($commentId)
    {
        $comment = $this->lead->comments()->find($commentId);
        if ($comment && $comment->type != CommentType::CLOSED->value) {
            $comment->delete();
            unset($this->comments);
            $this->dispatch('notify', type: 'warning', message: 'تم حذف التعليق.');
        }
    }

    #[Computed]
    public function comments()
    {
        return $this->lead->comments()->with('user')->latest()->get();
    }

    #[Computed]
    public function activities()
    {
        return $this->lead->activities()->with('user')->latest('created_at')->take(20)->get();
    }

    #[Computed]
    public function types()
    {
        return CommentType::cases();
    }

    public function render()
    {
        return view('pages.leads.⚡show.show');
    }
};
