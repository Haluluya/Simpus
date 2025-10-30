<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-700">
                    {{ __('Edit Patient') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">{{ $patient->name }} · {{ $patient->medical_record_number }}</p>
            </div>
            <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring focus:ring-slate-200 focus:ring-offset-2">
                {{ __('Back to detail') }}
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl border border-white/60 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-8">
            @csrf
            @method('PUT')
            @include('patients.partials.form')

            <div class="flex justify-end gap-3">
                <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring focus:ring-slate-200 focus:ring-offset-2">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring focus:ring-sky-400 focus:ring-offset-2">
                    {{ __('Update Patient') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
