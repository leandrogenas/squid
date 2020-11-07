<div class="form-group">
    <div class="{{$class ??"icheck-primary"}} d-inline">
        <input  value="{{$value ?? ""}}"  type="checkbox"  {{empty($function ) ? "":"checked=checked"}} name="{{$name ?? ""}}" id="{{$id ?? "checkboxPrimary1"}}" >
        <label for="{{$id ?? "checkboxPrimary1"}}">
            {{$slot}}
        </label>
    </div>
</div>

