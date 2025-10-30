@php
    $isEdit = isset($patient);
@endphp

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nama Lengkap" />
        <x-text-input id="name" name="name" type="text" class="mt-2 block w-full" required value="{{ old('name', $patient->name ?? '') }}" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="nik" value="NIK" />
        <x-text-input id="nik" name="nik" type="text" class="mt-2 block w-full" required maxlength="20" value="{{ old('nik', $patient->nik ?? '') }}" />
        <x-input-error :messages="$errors->get('nik')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="bpjs_card_no" value="Nomor Kartu BPJS" />
        <x-text-input id="bpjs_card_no" name="bpjs_card_no" type="text" class="mt-2 block w-full" maxlength="30" value="{{ old('bpjs_card_no', $patient->bpjs_card_no ?? '') }}" />
        <x-input-error :messages="$errors->get('bpjs_card_no')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="medical_record_number" value="Nomor Rekam Medis" />
        <x-text-input id="medical_record_number" name="medical_record_number" type="text" class="mt-2 block w-full" maxlength="30" value="{{ old('medical_record_number', $patient->medical_record_number ?? '') }}" />
        <x-input-error :messages="$errors->get('medical_record_number')" class="mt-1" />
        <p class="mt-1 text-xs text-slate-500">Biarkan kosong untuk generate otomatis.</p>
    </div>

    <div>
        <x-input-label for="date_of_birth" value="Tanggal Lahir" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-2 block w-full" required value="{{ old('date_of_birth', isset($patient) ? optional($patient->date_of_birth)->format('Y-m-d') : '') }}" />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="gender" value="Jenis Kelamin" />
        <select id="gender" name="gender" class="mt-2 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
            <option value="">Pilih jenis kelamin</option>
            <option value="male" @selected(old('gender', $patient->gender ?? '') === 'male')>Laki-laki</option>
            <option value="female" @selected(old('gender', $patient->gender ?? '') === 'female')>Perempuan</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="blood_type" value="Golongan Darah" />
        <x-text-input id="blood_type" name="blood_type" type="text" class="mt-2 block w-full uppercase" maxlength="3" value="{{ old('blood_type', $patient->blood_type ?? '') }}" />
        <x-input-error :messages="$errors->get('blood_type')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="phone" value="Nomor Telepon" />
        <x-text-input id="phone" name="phone" type="text" class="mt-2 block w-full" maxlength="30" value="{{ old('phone', $patient->phone ?? '') }}" />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" value="{{ old('email', $patient->email ?? '') }}" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="address" value="Alamat Domisili" />
        <textarea id="address" name="address" rows="2" class="mt-2 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('address', $patient->address ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="village" value="Kelurahan / Desa" />
        <x-text-input id="village" name="village" type="text" class="mt-2 block w-full" value="{{ old('village', $patient->village ?? '') }}" />
        <x-input-error :messages="$errors->get('village')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="district" value="Kecamatan" />
        <x-text-input id="district" name="district" type="text" class="mt-2 block w-full" value="{{ old('district', $patient->district ?? '') }}" />
        <x-input-error :messages="$errors->get('district')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="city" value="Kab/Kota" />
        <x-text-input id="city" name="city" type="text" class="mt-2 block w-full" value="{{ old('city', $patient->city ?? '') }}" />
        <x-input-error :messages="$errors->get('city')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="province" value="Provinsi" />
        <x-text-input id="province" name="province" type="text" class="mt-2 block w-full" value="{{ old('province', $patient->province ?? '') }}" />
        <x-input-error :messages="$errors->get('province')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="postal_code" value="Kode Pos" />
        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-2 block w-full" maxlength="10" value="{{ old('postal_code', $patient->postal_code ?? '') }}" />
        <x-input-error :messages="$errors->get('postal_code')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="occupation" value="Pekerjaan" />
        <x-text-input id="occupation" name="occupation" type="text" class="mt-2 block w-full" value="{{ old('occupation', $patient->occupation ?? '') }}" />
        <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="allergies" value="Riwayat Alergi" />
        <textarea id="allergies" name="allergies" rows="2" class="mt-2 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('allergies')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="emergency_contact_name" value="Nama Kontak Darurat" />
        <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-2 block w-full" value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}" />
        <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="emergency_contact_relation" value="Hubungan Kontak Darurat" />
        <x-text-input id="emergency_contact_relation" name="emergency_contact_relation" type="text" class="mt-2 block w-full" value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation ?? '') }}" />
        <x-input-error :messages="$errors->get('emergency_contact_relation')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="emergency_contact_phone" value="Nomor Kontak Darurat" />
        <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-2 block w-full" maxlength="30" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}" />
        <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-1" />
    </div>
</div>
