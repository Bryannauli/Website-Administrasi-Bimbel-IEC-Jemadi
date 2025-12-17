<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB (KONSISTEN DENGAN INDEX LOG) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.log.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Activity Log</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Detail #{{ $activityLog->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. TITLE SECTION --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Log Activity Detail
                </h1>
                <p class="text-gray-500 text-sm mt-1">Viewing specific event data and attribute changes.</p>
            </div>

            {{-- 3. CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Detail Informasi Log (Kiri) --}}
                <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Log Metadata</h3>
                    
                    <div class="space-y-5">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Event Type</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold mt-1 border
                                @if($activityLog->event == 'created') bg-green-50 text-green-700 border-green-200
                                @elseif($activityLog->event == 'updated') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($activityLog->event == 'deleted') bg-red-50 text-red-700 border-red-200
                                @else bg-gray-50 text-gray-700 border-gray-200
                                @endif">
                                {{ strtoupper($activityLog->event) }}
                            </span>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actor</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                {{ $activityLog->actor ? $activityLog->actor->name : 'System/Guest' }}
                            </p>
                            <p class="text-[10px] text-gray-500 font-mono">{{ class_basename($activityLog->actor_type) }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Subject Reference</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                @if ($activityLog->subject)
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}</span>
                                @else
                                    <span class="text-gray-400 italic">N/A (Global Action)</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Recorded At</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                {{ $activityLog->created_at ? $activityLog->created_at->format('d M Y, H:i:s') : 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Network Info</p>
                            <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-xs font-mono text-gray-600 flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    IP: {{ $activityLog->ip_address ?? 'N/A' }}
                                </p>
                                <p class="text-[10px] text-gray-400 mt-2 break-words leading-relaxed" title="{{ $activityLog->user_agent }}">
                                    {{ $activityLog->user_agent ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Perubahan (Kanan) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Data Changes</h3>
                        <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded-md uppercase">Attributes Comparison</span>
                    </div>

                    @php
                        $properties = $activityLog->properties ?? [];
                        $oldAttributes = $properties['old'] ?? [];
                        $newAttributes = $properties['attributes'] ?? [];
                        $allKeys = array_unique(array_merge(array_keys($oldAttributes), array_keys($newAttributes)));
                    @endphp

                    <div class="p-0">
                        @if (empty($allKeys))
                            <div class="p-10 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                                <p class="text-gray-400 text-sm">No attribute changes were recorded for this event.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest w-1/4">Attribute</th>
                                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest w-1/3">Old Value</th>
                                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest w-1/3">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($allKeys as $key)
                                            @php
                                                $oldVal = $oldAttributes[$key] ?? 'N/A';
                                                $newVal = $newAttributes[$key] ?? 'N/A';
                                                $isChanged = ($oldVal !== $newVal);
                                            @endphp
                                            <tr class="hover:bg-gray-50/50 transition-colors {{ $isChanged ? 'bg-blue-50/30' : '' }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">{{ $key }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500 italic max-w-sm break-words {{ $isChanged ? 'line-through opacity-60' : '' }}">
                                                    {{ is_array($oldVal) ? json_encode($oldVal) : (is_bool($oldVal) ? ($oldVal ? 'true' : 'false') : $oldVal) }}
                                                </td>
                                                <td class="px-6 py-4 text-sm max-w-sm break-words {{ $isChanged ? 'font-bold text-blue-700' : 'text-gray-600' }}">
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
    </div>
</x-app-layout>