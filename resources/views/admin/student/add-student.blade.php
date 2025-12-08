<x-app-layout>

    <x-slot name="header"></x-slot>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-600 ml-6 mt-6">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Home</a>

        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd" />
        </svg>

        <a href="{{ route('admin.student.index') }}" class="hover:text-gray-900">Student</a>

        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd" />
        </svg>

        <span class="text-gray-900 font-medium">Add New Student</span>
    </div>

    <div class="p-6">

        {{-- FORM --}}
        <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Add New Student</h1>

        <form action="{{ route('admin.student.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="font-semibold">Student Number</label>
                    <input type="text" name="student_number"
                        class="w-full border rounded p-2"
                        value="{{ old('student_number') }}">
                    @error('student_number') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-semibold">Full Name</label>
                    <input type="text" name="name"
                        class="w-full border rounded p-2"
                        value="{{ old('name') }}">
                    @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-semibold">Gender</label>
                    <select name="gender" class="w-full border rounded p-2">
                        <option value="">-- Choose --</option>
                        <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-semibold">Phone</label>
                    <input type="text" name="phone"
                        class="w-full border rounded p-2"
                        value="{{ old('phone') }}">
                </div>

                <div class="col-span-2">
                    <label class="font-semibold">Address</label>
                    <textarea name="address" class="w-full border rounded p-2">{{ old('address') }}</textarea>
                </div>

                <div class="col-span-2">
                    <label class="font-semibold">Class</label>
                    <select name="class_id" class="w-full border rounded p-2">
                        <option value="">-- Without Class --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->category }} | {{ $class->name }} | {{ $class->classroom }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded mt-5">
                Save
            </button>
        </form>
    </div>

</x-app-layout>