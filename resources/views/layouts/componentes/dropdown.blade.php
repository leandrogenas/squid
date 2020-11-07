<div class="{{$class ?? "dropdown"}}">
    <div>
        <button type="button" class="btn btn-primary dropdown-toggle"
                data-toggle="dropdown"
                aria-expanded="false">{{$button_name ?? "Funções"}}
            <span class="fa fa-caret-down"></span></button>
        <ul class="dropdown-menu">
            {{$slot}}
        </ul>
    </div>
</div>
