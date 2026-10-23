<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    |
    | Pesan ini ditampilkan langsung oleh frontend di bawah input form.
    | Aturan yang tidak ada di sini memakai fallback bahasa Inggris.
    |
    */

    'accepted' => ':Attribute harus disetujui.',
    'alpha_dash' => ':Attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'array' => ':Attribute harus berupa daftar.',
    'boolean' => ':Attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password saat ini salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'date_format' => ':Attribute harus berformat :format.',
    'different' => ':Attribute harus berbeda dari :other.',
    'digits' => ':Attribute harus berupa :digits digit angka.',
    'distinct' => ':Attribute tidak boleh duplikat.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'enum' => ':Attribute yang dipilih tidak valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa file.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'list' => ':Attribute harus berupa daftar.',
    'max' => [
        'array' => ':Attribute maksimal berisi :max item.',
        'file' => ':Attribute maksimal :max kilobyte.',
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa file bertipe: :values.',
    'min' => [
        'array' => ':Attribute minimal berisi :min item.',
        'file' => ':Attribute minimal :min kilobyte.',
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus mengandung minimal satu huruf.',
        'mixed' => ':Attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => ':Attribute harus mengandung minimal satu angka.',
        'symbols' => ':Attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':Attribute ini pernah bocor di internet. Pilih :attribute lain.',
    ],
    'prohibited' => ':Attribute tidak boleh diisi.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi jika :other adalah :value.',
    'same' => ':Attribute dan :other harus sama.',
    'size' => [
        'array' => ':Attribute harus berisi :size item.',
        'file' => ':Attribute harus berukuran :size kilobyte.',
        'numeric' => ':Attribute harus bernilai :size.',
        'string' => ':Attribute harus :size karakter.',
    ],
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah dipakai.',
    'uploaded' => ':Attribute gagal diunggah.',
    'url' => ':Attribute harus berupa URL yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'custom' => [],

    'attributes' => [
        // Umum
        'title' => 'judul',
        'titleJp' => 'judul Jepang',
        'kanji' => 'kanji',
        'tone' => 'warna cover',
        'coverUrl' => 'cover',
        'sortOrder' => 'urutan',
        'slug' => 'slug',
        'category' => 'kategori',
        'year' => 'tahun',
        'role' => 'peran',
        'status' => 'status',
        'description' => 'deskripsi',

        // Proyek
        'duration' => 'durasi',
        'summary' => 'ringkasan',
        'overview' => 'gambaran umum',
        'challenge' => 'tantangan',
        'approach' => 'pendekatan',
        'approach.*' => 'langkah pendekatan',
        'results' => 'hasil',
        'results.*.value' => 'nilai hasil',
        'results.*.label' => 'label hasil',
        'stack' => 'stack',
        'stack.*' => 'item stack',
        'links' => 'link',
        'links.demo' => 'link demo',
        'links.source' => 'link source code',

        // Sertifikasi
        'issuer' => 'penerbit',
        'score' => 'skor',
        'note' => 'catatan',
        'credentialUrl' => 'link kredensial',

        // Kegiatan
        'organizer' => 'penyelenggara',
        'photos' => 'foto',
        'photos.*' => 'foto',
        'focus' => 'fokus foto',

        // Artikel
        'date' => 'tanggal',
        'readTime' => 'waktu baca',
        'excerpt' => 'ringkasan',
        'content' => 'konten',
        'content.*' => 'blok konten',

        // Pesan
        'name' => 'nama',
        'email' => 'email',
        'subject' => 'subjek',
        'message' => 'pesan',

        // Auth
        'password' => 'password',
        'currentPassword' => 'password saat ini',
        'passwordConfirmation' => 'konfirmasi password',
        'remember' => 'ingat saya',

        // Pengaturan
        'brand' => 'brand',
        'nameJp' => 'nama Jepang',
        'location' => 'lokasi',
        'photoUrl' => 'foto profil',
        'cvUrl' => 'CV',
        'availability' => 'ketersediaan',
        'availability.open' => 'status ketersediaan',
        'availability.label' => 'label ketersediaan',
        'availability.detail' => 'detail ketersediaan',
        'socials' => 'media sosial',
        'socials.*.label' => 'nama media sosial',
        'socials.*.href' => 'link media sosial',
        'features' => 'fitur',
        'features.chatbot' => 'fitur chatbot',
        'features.preloader' => 'fitur preloader',

        // Upload & lainnya
        'file' => 'file',
        'folder' => 'folder',
        'ids' => 'daftar id',
        'ids.*' => 'id',
        'path' => 'path',
    ],

];
