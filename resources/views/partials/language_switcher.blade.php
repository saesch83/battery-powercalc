<div class="input-group input-group-static">
    <select class="form-control" onchange="if (this.value) window.location.href=this.value" id="exampleFormControlSelect1">
        @foreach($available_locales as $locale_name => $available_locale)
            @if($available_locale === $current_locale)
                <option selected value="language/{{ $available_locale }}">{{ $locale_name }}</option>
            @else
                <option value="language/{{ $available_locale }}">{{ $locale_name }}</option>
            @endif
        @endforeach        
      </select>
</div>