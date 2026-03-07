<div>
    {{-- Table --}}
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <input type="checkbox" wire:model.live="selectAll"
                                        class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sm:left-6">
                                </th>

                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    number
                                </th>

                                {{-- Sortable Name Column --}}
                                <th wire:click="sortBy('name')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        Name
                                        @if($sortField === 'name')
                                        <span>
                                            @if($sortDirection === 'asc')
                                            ↑
                                            @else
                                            ↓
                                            @endif
                                        </span>
                                        @endif
                                    </div>
                                </th>

                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Email
                                </th>

                                {{-- Sortable country --}}
                                <th wire:click="sortBy('department')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        country
                                        @if($sortField === 'department')
                                        <span>
                                            @if($sortDirection === 'asc')
                                            ↑
                                            @else
                                            ↓
                                            @endif
                                        </span>
                                        @endif
                                    </div>
                                </th>

                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>

                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($this->customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}" class="hover:bg-gray-50">
                                <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}"
                                        class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sm:left-6">
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $customer->customer_number }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ $customer->name }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $customer->email }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $customer->country }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span
                                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                                        {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $customer->status->label() }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('customers.edit', $customer) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        <button wire:click="confirmDelete({{ $customer->id }})"
                                            class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                    No customers found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->customers->links() }}
    </div>
</div>