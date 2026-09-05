<?php

return [
    /* Default row cap for the "top pages" table. */
    'top_pages_limit' => (int) env('NOVA_ANALYTICS_TOP_PAGES_LIMIT', 20),

    /* Cap on each filter-bar dropdown's distinct-value list, to bound the scan on
     * high-cardinality columns like utm_campaign. */
    'filter_options_limit' => (int) env('NOVA_ANALYTICS_FILTER_OPTIONS_LIMIT', 200),
];
