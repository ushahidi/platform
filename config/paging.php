<?php
/**
 * Paging Config
 */
return [
    'items_per_page' => env('DEFAULT_COUNT_ITEMS_PER_PAGE', 20),
    'default_order_by' => env('DEFAULT_ORDER_BY', "id"),
    'default_order' => env('DEFAULT_ORDER', "DESC")

];
