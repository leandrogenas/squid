<div class="form-group {{$divClass ?? ""}}">
    <div class="card {{$class ?? "card-primary"}} ">
        <a title="{{__("modeladminlang::default.click_to_expand")}}" data-toggle="collapse" class="{{empty($close) ? "collapsed":""}}"
           {{empty($close) ? "":"aria-expanded='true'"}} href="#{{$id??"collapse1"}}">
            <div class="card-header">
                <h4 class="card-title {{$class_title ?? ""}}"><i class="{{$icon ?? ""}}"></i> {{$title ?? ""}}
                </h4>
                {!!empty($header) ? "":$header  !!}
            </div>
        </a>
        <div id="{{$id ?? "collapse1"}}"
             class="collapse {{empty($close) ? "":"show"}}">
            <div class="card-body">
                {{$slot}}
            </div>
        </div>
    </div>
</div>
