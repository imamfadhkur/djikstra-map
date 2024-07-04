<nav id="sidebar">
    <div class="p-4 pt-5">
        @isset($profil->icon)
            <a href="#" class="img logo" style="background-image: url({{ asset($profil->icon) }});"></a>
        @endisset
      <p class="mb-2 text-center">welcome, 
        {{ Auth::user()->name }}!</p>
        @can('admin')
            <ul class="list-unstyled components mb-5">
                <li class="{{ Request::is('*dashboard') ? 'active' : '' }}">
                    <a href="/dashboard">Dashboard</a>
                </li>
                <li class="{{ Request::is('kurir*') ? 'active' : '' }}">
                    <a href="/kurir">Kurir</a>
                </li>
                <li class="{{ Request::is('alamat-penerima*') ? 'active' : '' }}">
                    <a href="/alamat-penerima">Alamat Penerima</a>
                </li>
            </ul>
        @endcan
        @can('kurir')
            <ul class="list-unstyled components mb-5">
                <li class="{{ Request::is('*dashboard') ? 'active' : '' }}">
                    <a href="/dashboard">Dashboard</a>
                </li>
                <li class="{{ Request::is('alamat-pengiriman*') ? 'active' : '' }}">
                    <a href="/alamat-pengiriman">Alamat Pengiriman</a>
                </li>
            </ul>
        @endcan

<div class="footer">
    @isset($profil->footer)
        <p>{{ $profil->footer }}</p>
    @endisset
</div>

</div>
</nav>