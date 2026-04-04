<?php

namespace App\Livewire\Pages\Attachments;

use App\Models\CrmAttachment;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('أرشيف المرفقات')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public string $search        = '';
    public string $filterType    = '';
    public string $filterUploader = '';
    public string $filterOpportunity = '';
    public string $created_from  = '';
    public string $created_to    = '';

    // Sort
    public string $sortField     = 'created_at';
    public string $sortDirection = 'desc';
    public int    $perPage       = 15;

    // Delete
    public ?int $attachmentToDelete = null;
    public bool $showDeleteModal    = false;

    protected $queryString = [
        'search'            => ['except' => ''],
        'filterType'        => ['except' => ''],
        'filterUploader'    => ['except' => ''],
        'filterOpportunity' => ['except' => ''],
        'created_from'      => ['except' => ''],
        'created_to'        => ['except' => ''],
        'sortField'         => ['except' => 'created_at'],
        'sortDirection'     => ['except' => 'desc'],
    ];

    // ==========================================
    // Computed Properties
    // ==========================================

    #[Computed]
    public function attachmentsList()
    {
        $validSortFields = ['file_name', 'file_type', 'file_size', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return CrmAttachment::query()
            ->with(['uploader', 'attachmentable', 'customer'])
            ->where('attachmentable_type', Opportunity::class)
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('file_name', 'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%')
                       ->orWhereHas('attachmentable', fn($oq) =>
                           $oq->where('title', 'like', '%' . $this->search . '%')
                              ->orWhere('opportunity_number', 'like', '%' . $this->search . '%')
                       )
                       ->orWhereHas('customer', fn($cq) =>
                           $cq->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('mobile', 'like', '%' . $this->search . '%')
                              ->orWhere('phone', 'like', '%' . $this->search . '%')
                       );
                });
            })
            ->when($this->filterType, fn($q) => $q->where('file_type', 'like', '%' . $this->filterType . '%'))
            ->when($this->filterUploader, fn($q) => $q->where('created_by', $this->filterUploader))
            ->when($this->filterOpportunity, fn($q) => $q->where('attachmentable_id', $this->filterOpportunity))
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('created_at', '<=', $this->created_to))
            ->orderBy($sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function uploaders()
    {
        return User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    }

    #[Computed]
    public function opportunities()
    {
        return Opportunity::orderBy('title')->get(['id', 'title', 'opportunity_number']);
    }

    #[Computed]
    public function fileTypes()
    {
        return [
            'pdf'   => 'PDF',
            'image' => 'صور',
            'word'  => 'Word',
            'excel' => 'Excel',
        ];
    }

    #[Computed]
    public function stats()
    {
        $base = CrmAttachment::where('attachmentable_type', Opportunity::class);
        return [
            'total'      => (clone $base)->count(),
            'total_size' => (clone $base)->sum('file_size'),
            'pdf_count'  => (clone $base)->where('file_type', 'like', '%pdf%')->count(),
            'img_count'  => (clone $base)->where('file_type', 'like', '%image%')->count(),
        ];
    }

    // ==========================================
    // Actions
    // ==========================================

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
        $this->dispatch('close-offcanvas');
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterType', 'filterUploader', 'filterOpportunity', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete(int $attachmentId): void
    {
        $this->attachmentToDelete = $attachmentId;
        $this->showDeleteModal    = true;
    }

    public function deleteAttachment(): void
    {
        if ($this->attachmentToDelete) {
            $attachment = CrmAttachment::find($this->attachmentToDelete);
            if ($attachment) {
                // حذف الملف من التخزين (public disk)
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
            $this->showDeleteModal    = false;
            $this->attachmentToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف المرفق بنجاح.');
            unset($this->attachmentsList);
            unset($this->stats);
        }
    }

    public function downloadAttachment(int $attachmentId)
    {
        $attachment = CrmAttachment::find($attachmentId);
        if (!$attachment) {
            $this->dispatch('notify', type: 'error', message: 'المرفق غير موجود.');
            return;
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            $this->dispatch('notify', type: 'error', message: 'الملف غير موجود على الخادم.');
            return;
        }

        $fullPath = storage_path('app/public/' . $attachment->file_path);
        return response()->download($fullPath, $attachment->file_name);
    }

    public function render()
    {
        return view('pages.attachments.⚡index.index');
    }
};
