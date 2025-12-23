@if (!$hideTitle && $postTitle)
    @typography(['element' => 'h4', 'variant' => 'h2', 'classList' => ['module-title']])
        {{ $postTitle }}
    @endtypography
@endif

@if($has_steps)
    <div class="c-step-by-step-timeline" data-module-id="{{ $id }}" data-total-steps="{{ $total_steps }}">
        <div class="c-step-by-step-timeline__line"></div>
        <div class="c-step-by-step-timeline__content">
            <{{$componentElement}} class="{{ $class }}" {!! $attribute !!}>
                @foreach($list as $section)
                    <{{$sectionElement}} class="{{$baseClass}}__section c-step-by-step-timeline__section @if($section['open_by_default']) c-step-by-step-timeline__section--open @endif">
                        <{{$sectionHeadingElement}} 
                            class="{{$baseClass}}__button @if($section['open_by_default']) {{$baseClass}}__button--expanded @endif" 
                            type="button"
                            role="button" 
                            aria-label="{{$section['heading']}}" 
                            aria-controls="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" 
                            aria-expanded="{{ $section['open_by_default'] ? 'true' : 'false' }}">
                            <span class="{{$baseClass}}__button-wrapper" tabindex="-1">
                                {!!$beforeHeading!!}
                                {!! $section['heading'] !!}
                                {!!$afterHeading!!}
                                @icon(['icon' => 'add', 'size' => 'md', 'classList' => [$baseClass . '__icon', $baseClass . '__icon--add']])
                                @endicon
                                @icon(['icon' => 'remove', 'size' => 'md', 'classList' => [$baseClass . '__icon', $baseClass . '__icon--remove']])
                                @endicon
                            </span>
                        </{{$sectionHeadingElement}}>
                        <{{$sectionContentElement}} 
                            class="{{$baseClass}}__content" 
                            id="{{ $baseClass }}__aria-{{ $id }}-{{ $loop->index }}" 
                            aria-hidden="{{ $section['open_by_default'] ? 'false' : 'true' }}">
                            <div class="{{$baseClass}}__content-inner">
                                {!!$beforeContent!!}
                                {!! $section['content'] !!}
                                {!!$afterContent!!}
                            </div>
                        </{{$sectionContentElement}}>
                    </{{$sectionElement}}>
                @endforeach
            </{{$componentElement}}>
        </div>
    </div>
@else
    <p class="mod-step-by-step__empty">{{ __('No steps have been added yet.', 'modularity-step-by-step') }}</p>
@endif

