@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            @if($settings['backup_last_file'] ?? null)
                <p class="text-xs mt-1">
                    📁 Saved to: <strong>storage/app/backups/{{ $settings['backup_last_file'] }}</strong>
                </p>
            @endif
        </div>
    @endif
    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-lg p-6 max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Organization Settings</h1>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Organization Info --}}
                <div class="mb-4">
                    <label for="organization_name" class="block text-gray-700 text-sm font-bold mb-2">Organization Name</label>
                    <input type="text" id="organization_name" name="organization_name"
                           value="{{ $settings['organization_name'] ?? '' }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <input type="text" id="organization_address" name="organization_address"
                           value="{{ $settings['organization_address'] ?? '' }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_address')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="text" id="organization_phone" name="organization_phone"
                           value="{{ $settings['organization_phone'] ?? '' }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_phone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="organization_email" name="organization_email"
                           value="{{ $settings['organization_email'] ?? '' }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_logo" class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                    <input type="file" id="organization_logo" name="organization_logo"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_logo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Current Logo:</p>
                            <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}"
                                 alt="Current Organization Logo" class="mt-2 h-20">
                        </div>
                    @endif
                </div>

                {{-- Backup Settings --}}
                <div class="mb-6 border-t pt-6">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Database Backup</h2>

                    {{-- Last Backup Status --}}
                    {{-- Last Backup Status --}}
                    <div id="last_backup_box" class="mb-4 p-4 rounded-lg {{ ($settings['backup_last_run'] ?? null) ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                        <p class="text-sm font-bold {{ ($settings['backup_last_run'] ?? null) ? 'text-green-700' : 'text-yellow-700' }}">
                            Last Backup:
                            <span id="last_backup_display" class="font-normal">
            {{ ($settings['backup_last_run'] ?? null)
                ? \Carbon\Carbon::parse($settings['backup_last_run'])->diffForHumans() . ' (' . $settings['backup_last_run'] . ')'
                : 'No backup has been run yet.' }}
        </span>
                        </p>

                        @if(($settings['backup_enabled'] ?? '0') == '1' && ($settings['backup_last_run'] ?? null))
                            @php
                                $lastRun   = \Carbon\Carbon::parse($settings['backup_last_run']);
                                $frequency = $settings['backup_frequency'] ?? 'daily';
                                $nextRun   = match($frequency) {
                                    'daily'   => $lastRun->copy()->addDay(),
                                    'weekly'  => $lastRun->copy()->addWeek(),
                                    'monthly' => $lastRun->copy()->addMonth(),
                                    default   => $lastRun->copy()->addDay(),
                                };
                            @endphp
                            <p class="text-sm text-green-600 mt-1">
                                Next backup: <strong>{{ $nextRun->diffForHumans() }} ({{ $nextRun->format('Y-m-d H:i') }})</strong>
                            </p>
                        @endif
                    </div>
                    {{-- Enable Backup --}}
                    <div class="mb-4 flex items-center gap-3">
                        <input type="checkbox" id="backup_enabled" name="backup_enabled" value="1"
                               {{ ($settings['backup_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                               class="w-5 h-5 cursor-pointer">
                        <label for="backup_enabled" class="text-gray-700 text-sm font-bold">
                            Enable Automated Backup
                        </label>
                    </div>

                    {{-- Frequency --}}
                    <div class="mb-4">
                        <label for="backup_frequency" class="block text-gray-700 text-sm font-bold mb-2">Backup Frequency</label>
                        <select id="backup_frequency" name="backup_frequency"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                            <option value="daily"   {{ ($settings['backup_frequency'] ?? 'daily') == 'daily'   ? 'selected' : '' }}>Daily</option>
                            <option value="weekly"  {{ ($settings['backup_frequency'] ?? '') == 'weekly'  ? 'selected' : '' }}>Weekly (Every Monday)</option>
                            <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly (1st of month)</option>
                        </select>
                    </div>

                    {{-- Time --}}
                    <div class="mb-4">
                        <label for="backup_time" class="block text-gray-700 text-sm font-bold mb-2">Backup Time</label>
                        <input type="time" id="backup_time" name="backup_time"
                               value="{{ $settings['backup_time'] ?? '00:00' }}"
                               class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>

                    {{-- Retention --}}
                    <div class="mb-4">
                        <label for="backup_retention" class="block text-gray-700 text-sm font-bold mb-2">
                            Keep Backups For (days)
                        </label>
                        <input type="number" id="backup_retention" name="backup_retention" min="1" max="365"
                               value="{{ $settings['backup_retention'] ?? 7 }}"
                               class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                    </div>

                    {{-- Save Location --}}
                    {{-- Save Location --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Backup Save Location
                        </label>
                        <div class="flex gap-2 items-center">
                            <input type="text" id="backup_save_path" name="backup_save_path" readonly
                                   value="{{ $settings['backup_save_path'] ?? '' }}"
                                   placeholder="No folder selected..."
                                   class="shadow border rounded w-full py-2 px-3 text-gray-500 bg-gray-50 focus:outline-none cursor-not-allowed">
                            <button type="button" onclick="selectFolder()"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                                📁 Browse
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Click Browse to choose where backups will be saved on your computer.
                        </p>
                    </div>

                    {{-- Backup Action Buttons --}}
{{--                    <div class="flex gap-3 mt-2 mb-4">--}}
{{--                        <button type="button" id="runBackupBtn" onclick="runBackupNow()"--}}
{{--                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">--}}
{{--                            🗄 Run Backup Now--}}
{{--                        </button>--}}
{{--                        <button type="button" onclick="downloadToFolder()"--}}
{{--                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">--}}
{{--                            ⬇ Download Latest Backup--}}
{{--                        </button>--}}
{{--                    </div>--}}

                    {{-- Browser tip --}}
                    <div class="bg-blue-50 border border-blue-200 rounded p-3" id="browser-tip" style="display:none;">
                        <p class="text-xs text-blue-700 font-bold mb-1">⚠ Your browser does not support folder picker</p>
                        <p class="text-xs text-blue-600">
                            Please use <strong>Chrome or Edge</strong> for folder selection.
                            Or enable <strong>"Ask me where to save each file"</strong> in your browser download settings.
                        </p>
                    </div>

                    {{-- Backup Action Buttons --}}
                    <div class="flex gap-3 mt-2 mb-4">
                        <a href="{{ route('backup.run') }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            🗄 Run Backup Now
                        </a>
                        <a href="{{ route('backup.download') }}"
                           onclick="return confirmDownload()"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            ⬇ Download Latest Backup
                        </a>
                    </div>

                    {{-- Browser tip --}}
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-700 font-bold mb-1">💡 Tip: Enable Save Dialog in your browser</p>
                        <p class="text-xs text-blue-600">
                            Chrome/Edge: Go to <strong>Settings → Downloads</strong> and turn on
                            <strong>"Ask me where to save each file"</strong> so you can choose your backup folder every time.
                        </p>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex items-center justify-end border-t pt-4">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                        Save Settings
                    </button>
                </div>

            </form>
        </div>
    </div>
    <script>
        let selectedDirectoryHandle = null;

        // Restore folder name from sessionStorage on page load
        window.addEventListener('load', async () => {
            const savedFolder = sessionStorage.getItem('backup_folder_name');
            if (savedFolder) {
                document.getElementById('backup_save_path').value = savedFolder;
            }
        });

        async function selectFolder() {
            try {
                if (!window.showDirectoryPicker) {
                    document.getElementById('browser-tip').style.display = 'block';
                    return;
                }

                selectedDirectoryHandle = await window.showDirectoryPicker({ mode: 'readwrite' });

                const folderName = selectedDirectoryHandle.name;
                document.getElementById('backup_save_path').value = folderName;

                // Persist folder name in sessionStorage
                sessionStorage.setItem('backup_folder_name', folderName);

                // Save to hidden input for form submit
                let hiddenInput = document.getElementById('backup_save_path_hidden');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'backup_save_path';
                    hiddenInput.id   = 'backup_save_path_hidden';
                    document.querySelector('form').appendChild(hiddenInput);
                }
                hiddenInput.value = folderName;

                alert('✅ Folder selected: ' + folderName + '\nClick Save Settings to save your preferences.');

            } catch (err) {
                if (err.name !== 'AbortError') {
                    alert('Could not open folder picker: ' + err.message);
                }
            }
        }

        async function runBackupNow() {
            if (!selectedDirectoryHandle) {
                const confirm1 = confirm('⚠ No folder selected.\n\nThe backup file will be downloaded to your default Downloads folder.\n\nClick OK to continue, or Cancel to select a folder first.');
                if (!confirm1) return;
            }

            const btn = document.getElementById('runBackupBtn');
            btn.textContent = '⏳ Running backup...';
            btn.disabled = true;

            try {
                const runResponse = await fetch('{{ route('backup.run.ajax') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const result = await runResponse.json();

                if (!result.success) {
                    alert('Backup failed: ' + result.message);
                    btn.textContent = '🗄 Run Backup Now';
                    btn.disabled = false;
                    return;
                }

                // Download to selected folder or default downloads
                await saveToFolder(result.fileName);

                btn.textContent = '✅ Backup Done!';

                // Update last backup time on page without full reload
                document.getElementById('last_backup_display').textContent =
                    'Just now (' + result.lastRun + ')';
                document.getElementById('last_backup_box').className =
                    'mb-4 p-4 rounded-lg bg-green-50 border border-green-200';

                setTimeout(() => {
                    btn.textContent = '🗄 Run Backup Now';
                    btn.disabled = false;
                }, 3000);

            } catch (err) {
                alert('Error: ' + err.message);
                btn.textContent = '🗄 Run Backup Now';
                btn.disabled = false;
            }
        }

        async function downloadToFolder() {
            await saveToFolder(null);
        }

        async function saveToFolder(fileName) {
            try {
                const response = await fetch('{{ route('backup.download') }}');

                if (!response.ok) {
                    alert('Failed to get backup file from server.');
                    return;
                }

                const blob        = await response.blob();
                const disposition = response.headers.get('Content-Disposition');
                const name        = fileName
                    ?? (disposition
                        ? disposition.split('filename=')[1].replace(/"/g, '').trim()
                        : 'backup-' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.sqlite');

                if (selectedDirectoryHandle) {
                    try {
                        const fileHandle = await selectedDirectoryHandle.getFileHandle(name, { create: true });
                        const writable   = await fileHandle.createWritable();
                        await writable.write(blob);
                        await writable.close();
                        alert('✅ Backup saved to folder: ' + selectedDirectoryHandle.name + '\\\n' + name);
                        return;
                    } catch (err) {
                        console.warn('Folder write failed, falling back to download.', err);
                    }
                }

                // Fallback normal download
                const url  = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href  = url;
                link.download = name;
                link.click();
                URL.revokeObjectURL(url);

            } catch (err) {
                alert('Download failed: ' + err.message);
            }
        }
    </script>

@endsection
