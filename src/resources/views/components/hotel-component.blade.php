<div class="card">
    <a href="{{ route('hotel.showOne', $hotel->id) }}" class="card__link">
        <img 
            alt="Image of {{ $hotel->name }}" 
            height="200" 
            src="{{ $hotel->image ?: asset('images/200x100.svg') }}" 
            width="300" 
            class="card__image" 
        />
        <div class="card__content">
            <div class="card__title">
                {{ $hotel->name }}
            </div>
            <div class="card__country">
                {{ $hotel->country }}
            </div>
            <div class="card__description">
                {{ $hotel->description }}
            </div>
        </div>
    </a>
</div>
