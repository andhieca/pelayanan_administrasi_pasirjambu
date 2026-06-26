<aside
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 group"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium">
            <!-- Common Links -->
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 text-gray-900 rounded-lg {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'bg-bedas-50 text-bedas-600' : 'hover:bg-gray-100 group' }}">
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-bedas-600" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-.066h.002Z" />
                        <path
                            d="M12.5 0c-.157 0-.311.01-.462.03a.999.999 0 0 0-.812 1.192V8h6.778a.999.999 0 0 0 1.192-.812A10.118 10.118 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->role === 'petugas')
                <li class="pt-4 pb-2">
                    <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen</span>
                </li>
                <li>
                    <a href="{{ route('petugas.articles.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg {{ request()->routeIs('petugas.articles.*') ? 'bg-bedas-50 text-bedas-600' : 'hover:bg-gray-100 group' }}">
                        <svg class="w-5 h-5 transition duration-75 group-hover:text-bedas-600" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M19 4h-1V3a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1ZM3 4h14v1H3V4Zm16 14H1V6h18v12Z" />
                        </svg>
                        <span class="ms-3">Kelola Berita</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('petugas.users.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg {{ request()->routeIs('petugas.users.*') ? 'bg-bedas-50 text-bedas-600' : 'hover:bg-gray-100 group' }}">
                        <svg class="w-5 h-5 transition duration-75 group-hover:text-bedas-600" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path
                                d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.988 2.988 0 0 1-.505 1.697H19a1 1 0 0 0 1-1v-5.122a3.447 3.447 0 0 0-3.445-3.445H15Z" />
                            <path d="M7 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0 2a7 7 0 0 0-7 7v1h14v-1a7 7 0 0 0-7-7H7Z" />
                        </svg>
                        <span class="ms-3">Kelola Pengguna</span>
                    </a>
                </li>
            @endif

            <li class="pt-4 pb-2">
                <span class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Akun</span>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center p-2 text-gray-900 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-bedas-50 text-bedas-600' : 'hover:bg-gray-100 group' }}">
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-bedas-600" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path
                            d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.988 2.988 0 0 1-.505 1.697H19a1 1 0 0 0 1-1v-5.122a3.447 3.447 0 0 0-3.445-3.445H15Z" />
                        <path d="M7 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0 2a7 7 0 0 0-7 7v1h14v-1a7 7 0 0 0-7-7H7Z" />
                    </svg>
                    <span class="ms-3">Profil</span>
                </a>
            </li>
        </ul>
    </div>
</aside>