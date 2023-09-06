<?php

namespace App\Classes;

class OrderStatus
{
    const PENDING = 'pending';
    const FAILED = 'failed';
    const COMPLETED = 'completed';
    const PROCESSING = 'processing';
    const PACKING = 'packing';
    const SENDING = 'sending';
}