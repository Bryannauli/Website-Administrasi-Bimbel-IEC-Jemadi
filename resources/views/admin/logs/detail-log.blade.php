<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.activity-log.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Activity Log</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail #{{ $activityLog->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Detail Informasi Log --}}
                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Log Metadata</h3>
                    
                    <div class="space-y-4">
                        <div class="border-b pb-2">
                            <p class="text-xs font-medium text-gray-500 uppercase">Event</p>
                            <span class="text-sm font-semibold 
                                @if($activityLog->event == 'created') text-green-600
                                @elseif($activityLog->event == 'updated') text-blue-600
                                @elseif($activityLog->event == 'deleted') text-red-600
                                @else text-gray-600
                                @endif">
                                {{ ucfirst($activityLog->event) }}
                            </span>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs font-medium text-gray-500 uppercase">Actor</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $activityLog->actor ? $activityLog->actor->name . ' (' . class_basename($activityLog->actor_type) . ')' : 'System/Guest' }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs font-medium text-gray-500 uppercase">Subject</p>
                            <p class="text-sm font-semibold text-gray-900">
                                @if ($activityLog->subject)
                                    {{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs font-medium text-gray-500 uppercase">Timestamp</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $activityLog->created_at->format('d F Y, H:i:s') }}</p>
                        </div>
                        <div class="border-b pb-2">
                            <p class="text-xs font-medium text-gray-500 uppercase">IP Address / User Agent</p>
                            <p class="text-sm text-gray-700">{{ $activityLog->ip_address ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 truncate mt-1" title="{{ $activityLog->user_agent }}">{{ $activityLog->user_agent ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Detail Perubahan (Properties) --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Data Changes (Properties)</h3>

                    @php
                        $properties = $activityLog->properties ?? [];
                        $oldAttributes = $properties['old'] ?? [];
                        $newAttributes = $properties['attributes'] ?? [];

                        // Gabungkan semua kunci untuk membandingkan
                        $allKeys = array_unique(array_merge(array_keys($oldAttributes), array_keys($newAttributes)));
                    @endphp

                    @if (empty($allKeys))
                        <p class="text-gray-500">No attribute changes were recorded (e.g., event is 'created' or 'deleted' without specific data).</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Attribute</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Old Value</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">New Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($allKeys as $key)
                                        @php
                                            $oldVal = $oldAttributes[$key] ?? 'N/A';
                                            $newVal = $newAttributes[$key] ?? 'N/A';
                                            $isChanged = ($oldVal !== $newVal);
                                        @endphp
                                        <tr @if($isChanged) class="bg-blue-50/50" @endif>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $key }}</td>
                                            <td class="px-6 py-3 text-sm text-gray-700 max-w-sm break-words 
                                                @if($isChanged) line-through text-gray-500 @endif">
                                                {{ is_array($oldVal) ? json_encode($oldVal) : (is_bool($oldVal) ? ($oldVal ? 'true' : 'false') : $oldVal) }}
                                            </td>
                                            <td class="px-6 py-3 text-sm max-w-sm break-words 
                                                @if($isChanged) font-bold text-blue-700 @else text-gray-700 @endif">
                                                {{ is_array($newVal) ? json_encode($newVal) : (is_bool($newVal) ? ($newVal ? 'true' : 'false') : $newVal) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>