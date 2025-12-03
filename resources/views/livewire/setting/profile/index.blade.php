<div>
    <x-slot name="title">Pengaturan Profil</x-slot>

    <x-slot name="pageTitle">Pengaturan Profil</x-slot>

    <x-slot name="pagePretitle">Pengaturan</x-slot>

    <x-alert />

    <div class="row row-cards">
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body p-4 text-center">
                    <span class="avatar avatar-xl mb-3 avatar-rounded"
                        style="background-image: url({{ auth()->user()->avatarUrl() ? auth()->user()->avatarUrl() : asset('storage/' . auth()->user()->avatar) }})"></span>

                    <h3 class="m-0 mb-1">
                        @if (auth()->user()->role == 'siswa' && auth()->user()->student())
                            <div>{{ strtolower(auth()->user()->student->full_name) }}</div>
                        @else
                            <div>{{ strtolower(auth()->user()->username) }}</div>
                        @endif
                    </h3>

                    <div class="text-muted">{{ auth()->user()->email ?? '-' }}</div>

                    <div class="mt-3">
                        <span class="badge bg-green-lt">{{ auth()->user()->role ?? '-' }}</span>
                    </div>
                </div>

                <div class="btn-group p-3" role="group" aria-label="Basic example">
                    <a href="{{ route('setting.profile.index') }}" class="btn btn-card py-3 btn-square">Profil</a>

                    <a href="{{ route('setting.account.index') }}" class="btn btn-card py-3 btn-square">Akun</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-8">
            @if (auth()->user()->role == 'guru' && auth()->user()->teacher)
                <livewire:setting.profile.teacher-profile />
            @elseif (auth()->user()->role == 'siswa' && auth()->user()->student)
                <livewire:setting.profile.student-profile />
            @elseif (auth()->user()->role == 'wali siswa' && auth()->user()->student_guardian)
                <livewire:setting.profile.student-guardian-profile />
            @else
                <div class="card">
                    <div class="card-body py-5 text-center">
                        <i class="las la-praying-hands" style="font-size: 40px"></i>
                        <h2 class="m-0 mb-1 pt-3 font-weight-bold">Maaf</h2>
                        <div class="text-muted pb-3">Sunting profil tidak tersedia untuk akun Anda.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
