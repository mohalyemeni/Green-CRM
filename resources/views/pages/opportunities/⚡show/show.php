<?php

namespace App\Livewire\Pages\Opportunities;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Opportunity;
use App\Models\CrmAttachment;
use App\Enums\CommentType;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Storage;

new #[Title('ملف الفرصة البيعية')] class extends Component
{
    use WithFileUploads;

    public Opportunity $opportunity;

    // التعليقات
    public string $commentBody = '';
    public int    $commentType;

    // المرفقات
    public $attachmentFile        = null;
    public string $attachmentDesc = '';

    public function mount(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
        $this->commentType = CommentType::NOTE->value;
    }

    // ==== التعليقات ====

    public function addComment()
    {
        $this->validate([
            'commentBody' => 'required|string|min:2|max:2000',
            'commentType' => 'required|integer',
        ], [
            'commentBody.required' => 'محتوى التعليق مطلوب.',
            'commentBody.min'      => 'التعليق يجب أن يكون حرفين على الأقل.',
        ]);

        $this->opportunity->comments()->create([
            'body'       => $this->commentBody,
            'type'       => $this->commentType,
            'user_id'    => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        $this->reset('commentBody');
        $this->commentType = CommentType::NOTE->value;
        unset($this->comments);

        $this->dispatch('notify', type: 'success', message: 'تم إضافة التعليق بنجاح.');
    }

    public function deleteComment($commentId)
    {
        $comment = $this->opportunity->comments()->find($commentId);
        if ($comment && $comment->type != CommentType::CLOSED->value && $comment->user_id === auth()->id()) {
            $comment->delete();
            unset($this->comments);
            $this->dispatch('notify', type: 'warning', message: 'تم حذف التعليق.');
        }
    }

    // ==== المرفقات ====

    public function uploadAttachment()
    {
        $this->validate([
            'attachmentFile' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
            'attachmentDesc' => 'nullable|string|max:500',
        ], [
            'attachmentFile.required' => 'يجب اختيار ملف للرفع.',
            'attachmentFile.max'      => 'الحد الأقصى لحجم الملف 10 ميگابايت.',
        ]);

        $file     = $this->attachmentFile;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('crm-attachments', $fileName, 'public');

        CrmAttachment::create([
            'attachmentable_type' => Opportunity::class,
            'attachmentable_id'   => $this->opportunity->id,
            'file_name'           => $file->getClientOriginalName(),
            'file_path'           => $filePath,
            'file_type'           => $file->getMimeType(),
            'file_size'           => $file->getSize(),
            'description'         => $this->attachmentDesc ?: null,
            'created_by'          => auth()->id(),
        ]);

        $this->reset(['attachmentFile', 'attachmentDesc']);
        unset($this->attachments);

        $this->dispatch('notify', type: 'success', message: 'تم رفع المرفق بنجاح.');
    }

    public function deleteAttachment($attachmentId)
    {
        $attachment = CrmAttachment::find($attachmentId);
        if ($attachment && $attachment->created_by === auth()->id()) {
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
            unset($this->attachments);
            $this->dispatch('notify', type: 'warning', message: 'تم حذف المرفق.');
        }
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = CrmAttachment::find($attachmentId);
        if ($attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
            } else {
                $this->dispatch('notify', type: 'error', message: 'عذراً، الملف غير موجود.');
            }
        }
    }

    // ==== Computed Properties ====

    #[Computed]
    public function comments()
    {
        return $this->opportunity->comments()->with('user')->latest()->get();
    }

    #[Computed]
    public function attachments()
    {
        return $this->opportunity->attachments()->with('uploader')->latest()->get();
    }

    #[Computed]
    public function activities()
    {
        return $this->opportunity->activities()->with('user')->latest('created_at')->take(20)->get();
    }

    #[Computed]
    public function types()
    {
        return CommentType::cases();
    }

    public function render()
    {
        return view('pages.opportunities.⚡show.show');
    }
};
