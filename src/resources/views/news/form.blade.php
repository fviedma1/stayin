<div class="form-group">
    <label for="title">Títol</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $newsItem->title ?? '') }}" required>
</div>
<div class="form-group">
    <label for="short_description">Descripció Curta</label>
    <textarea name="short_description" class="form-control" required>{{ old('short_description', $newsItem->short_description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="long_description">Descripció Llarga</label>
    <textarea name="long_description" class="form-control" required>{{ old('long_description', $newsItem->long_description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="images">Imatges</label>
    <input type="file" name="images[]" class="form-control" multiple>
</div>
<div class="form-group">
    <label for="published">Publicada</label>
    <input type="checkbox" name="published" {{ old('published', $newsItem->published ?? false) ? 'checked' : '' }}>
</div>
<div class="form-group">
    <label for="hotels">Hotels</label>
    <select name="hotels[]" class="form-control" multiple required>
        @foreach($hotels as $hotel)
            <option value="{{ $hotel->id }}" {{ in_array($hotel->id, old('hotels', $newsItem->hotels ?? [])) ? 'selected' : '' }}>
                {{ $hotel->name }}
            </option>
        @endforeach
    </select>
</div>