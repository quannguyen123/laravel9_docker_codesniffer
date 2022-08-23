@if ($errors->has($field))
    <span id="exampleInputEmail1-error" class="text-danger">{{ $errors->first($field) }}</span>
@endif