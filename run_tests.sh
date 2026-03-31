#!/bin/bash
export PAGER=cat
export LESS=
cd /Users/anil/Desktop/sita/hotel-management-system
php artisan test tests/Unit/GuestSegmentationEngineTest.php --no-ansi --no-interaction --verbose > /tmp/test_output_full.log 2>&1
echo "Test completed"
echo "Exit code: $?"
