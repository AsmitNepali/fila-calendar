<?php

return [
    'today' => 'Today',
    'previous_month' => 'Previous month',
    'next_month' => 'Next month',
    'clear_range_hint' => 'Shift-click to clear the whole range',
    'reserved' => 'Reserved',

    'announcements' => [
        'selected' => ':date selected.',
        'deselected' => ':date deselected.',
        'range_started' => ':date selected as the range start. Hold shift with the arrow keys to extend, then press Enter.',
        'range_selected' => ':start to :end selected.',
        'cleared' => 'Selection cleared.',
    ],

    'validation' => [
        'min_nights' => '{1} The :attribute must cover at least 1 night.|[2,*] The :attribute must cover at least :min nights.',
        'max_nights' => '{1} The :attribute may not cover more than 1 night.|[2,*] The :attribute may not cover more than :max nights.',
        'min_dates' => '{1} The :attribute must have at least 1 date selected.|[2,*] The :attribute must have at least :min dates selected.',
        'max_dates' => '{1} The :attribute may not have more than 1 date selected.|[2,*] The :attribute may not have more than :max dates selected.',
        'incomplete_range' => 'The :attribute must have both a start and an end date.',
        'invalid' => 'The :attribute contains an invalid date.',
    ],
];
