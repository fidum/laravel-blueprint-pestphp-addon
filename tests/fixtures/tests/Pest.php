<?php

use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);
uses(AdditionalAssertions::class);
uses(RefreshDatabase::class);
uses(WithFaker::class);
