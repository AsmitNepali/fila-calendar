<?php

namespace Asmit\FilaCalendar\Support;

enum CalendarMode: string
{
    case Single = 'single';

    case Multiple = 'multiple';

    case Range = 'range';

    case MultiRange = 'multi-range';
}
