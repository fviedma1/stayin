<div class="logo">
    <img class="logo__image" alt="Logo placeholder" src="{{ asset('images/2.svg') }}" />
</div>
<nav class="nav">
    @foreach ($menu as $item)
        <div class="nav__item {{ $item['active'] ? 'nav__item--active' : '' }}">
            <a class="nav__link" href="{{ $item['url'] }}">
                <i class="nav__icon {{ $item['icon'] }}"></i> {{ $item['label'] }}
            </a>
        </div>
    @endforeach
</nav>
<div class="copyright">
    <p class="copyright__text">&copy; {{ date('Y') }}-Grup07</p>
</div>
