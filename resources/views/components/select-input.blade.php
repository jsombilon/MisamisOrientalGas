@props([
    'disabled' => false,
    'options' => [],   // accepts array of options
    'selected' => null // optional default selected value
])

<select 
    @disabled($disabled) 
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
>
    <option value="" disabled {{ $selected === null ? 'selected' : '' }}>-- Select --</option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
