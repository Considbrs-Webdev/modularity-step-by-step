@if (!$hideTitle && $postTitle)
    @typography(['element' => 'h4', 'variant' => 'h2', 'classList' => ['module-title']])
        {{ $postTitle }}
    @endtypography
@endif

@if($has_steps)
    <div class="c-step-by-step-timeline" data-total-steps="{{ $total_steps }}">
        <div class="c-step-by-step-timeline__line"></div>
        <div class="c-step-by-step-timeline__content">
            @accordion([
                'spacing' => true,
                'border' => true,
            ])
                @foreach($list as $section)
                    @accordion__item([
                        'heading' => $section['heading'],
                        'classList' => ['c-step-by-step-timeline__section'],
                        'attributeList' => $section['attributeList'],
                    ])
                        {!! $section['content'] !!}
                    @endaccordion__item
                @endforeach
            @endaccordion
        </div>
    </div>
@else
    <p class="mod-step-by-step__empty">{{ __('No steps have been added yet.', 'modularity-step-by-step') }}</p>
@endif
