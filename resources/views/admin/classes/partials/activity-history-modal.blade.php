<div x-show="showHistoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
                <div><h3 class="text-lg leading-6 font-bold text-gray-900">Teaching Logs</h3><p class="text-sm text-gray-500 mt-1">Activity history & attendance.</p></div>
                <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                @if($teachingLogs->isEmpty())
                    <div class="text-center py-10 text-gray-500">No logs found.</div>
                @else
                    <div class="space-y-4">
                        @foreach($teachingLogs as $log)
                            @php $teacher = $log->teacher; @endphp 
                            
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 text-blue-700 font-bold px-3 py-1.5 rounded-lg text-center"><span class="block text-xs uppercase">{{ \Carbon\Carbon::parse($log->date)->format('M') }}</span><span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($log->date)->format('d') }}</span></div>
                                        <div>
                                            {{-- PERBAIKAN: Mengakses langsung $teacher --}}
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $teacher->name ?? 'Unknown' }}</h4> 
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->date)->format('l') }}</span>
                                        </div>
                                    </div>
                                    @php
                                        $p = $log->records->where('status', 'present')->count();
                                        $t = $log->records->count();
                                        $pct = $t > 0 ? round(($p/$t)*100) : 0;
                                    @endphp
                                    <div class="text-right"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $pct }}% Present</span></div>
                                </div>
                                <div class="mt-3 pl-[72px]"><p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg italic border border-gray-100">"{{ $log->comment ?? '-' }}"</p></div> 
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100"><button @click="showHistoryModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Close</button></div>
        </div>
    </div>
</div>