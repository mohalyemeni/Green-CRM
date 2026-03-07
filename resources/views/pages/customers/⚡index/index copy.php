<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new #[Title('Customers')] class extends Component
{
    use WithPagination, WithFileUploads;

     // Filters
    public $search = '';
    public $department = '';
    public $status = '';
    public $sortField = 'first_name';
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected employees for bulk actions
    public $selected = [];
    public $selectAll = false;

    // Query string for URL persistence
    protected $queryString = [
        'search' => ['except' => ''],
        'department' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'first_name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // Computed property for filtered employees
    public function getEmployeesProperty()
    {
        return Employee::query()
            ->when($this->search, function ($query) {
                $query->whereFullText(['first_name', 'last_name', 'email', 'phone'], $this->search);
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    // Computed property for selected employees
    public function getSelectedEmployeesProperty()
    {
        return Employee::whereIn('id', $this->selected)->get();
    }

    // Computed property for department options
    public function getDepartmentOptionsProperty()
    {
        return Department::all();
    }

    // Computed property for status options
    public function getStatusOptionsProperty()
    {
        return [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
            ['value' => 'on_leave', 'label' => 'On Leave'],
        ];
    }

    // Computed property for sort icons
    public function getSortIconProperty($field)
    {
        if ($this->sortField === $field) {
            return $this->sortDirection === 'asc' ? '↑' : '↓';
        }
        return '';
    }

    // Computed property for pagination links
    public function getPaginationLinksProperty()
    {
        return $this->employees->links();
    }

    // Computed property for total employees
    public function getTotalEmployeesProperty()
    {
        return Employee::count();
    }

    // Computed property for active employees
    public function getActiveEmployeesProperty()
    {
        return Employee::where('status', 'active')->count();
    }

    // Computed property for inactive employees
    public function getInactiveEmployeesProperty()
    {
        return Employee::where('status', 'inactive')->count();
    }

    // Computed property for employees on leave
    public function getOnLeaveEmployeesProperty()
    {
        return Employee::where('status', 'on_leave')->count();
    }

    // Computed property for employees by department
    public function getEmployeesByDepartmentProperty()
    {
        return Department::withCount('employees')->get();
    }

    // Computed property for employees by status
    public function getEmployeesByStatusProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    }

    // Computed property for employees by department with pagination
    public function getEmployeesByDepartmentWithPaginationProperty()
    {
        return Department::withCount('employees')->paginate(5);
    }

    // Computed property for employees by status with pagination
    public function getEmployeesByStatusWithPaginationProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->paginate(5);
    }

    // Computed property for employees by department with pagination and search
    public function getEmployeesByDepartmentWithPaginationAndSearchProperty()
    {
        return Department::withCount('employees')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->paginate(5);
    }

    // Computed property for employees by status with pagination and search
    public function getEmployeesByStatusWithPaginationAndSearchProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->paginate(5);
    }

    // Computed property for employees by department with pagination and search and sort
    public function getEmployeesByDepartmentWithPaginationAndSearchAndSortProperty()
    {
        return Department::withCount('employees')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Computed property for employees by status with pagination and search and sort
    public function getEmployeesByStatusWithPaginationAndSearchAndSortProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Computed property for employees by department with pagination and search and sort and filters
    public function getEmployeesByDepartmentWithPaginationAndSearchAndSortAndFiltersProperty()
    {
        return Department::withCount('employees')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Computed property for employees by status with pagination and search and sort and filters
    public function getEmployeesByStatusWithPaginationAndSearchAndSortAndFiltersProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Computed property for employees by department with pagination and search and sort and filters and bulk actions
    public function getEmployeesByDepartmentWithPaginationAndSearchAndSortAndFiltersAndBulkActionsProperty()
    {
        return Department::withCount('employees')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }

    // Computed property for employees by status with pagination and search and sort and filters and bulk actions
    public function getEmployeesByStatusWithPaginationAndSearchAndSortAndFiltersAndBulkActionsProperty()
    {
        return Employee::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->when($this->search, function ($query) {
                $query->whereFullText(['name', 'email', 'phone'], $this->search);
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);
    }
}

