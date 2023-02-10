<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

uses(
    TestCase::class,
    AdditionalAssertions::class,
    RefreshDatabase::class,
    WithFaker::class
)->in('Feature');
