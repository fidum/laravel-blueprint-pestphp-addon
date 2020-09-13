# Changelog

All notable changes to `fidum/laravel-blueprint-pestphp-addon` will be documented in this file

## 0.6.1 - 2020-09-13

**Changed**
- Generate tests using class factories when on Laravel 8 ([#24](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/24))

## 0.6.0 - 2020-09-09

**Added**
- Cleaned up dependencies to support Laravel 8 ([#23](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/23))

**Changed**
- Changed `assertCount` to use the new `expect()->toHaveCount()` pest expectation API.
- Changed `assertSame` to use the new `expect()->toBe()` pest expectation API.

## 0.5.1 - 2020-08-28

**Changed**
- Update to support Blueprint v1.17.0 ([`6830547`](https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/68305474e482c787b6ada3bc6e7d1489e39bf486))
- Delete tests now `assertNoContent` ([`cc5a2e7`](https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/cc5a2e79f42eb872d5166783f60c486bd34093d2))

This release only supports Blueprint >= v1.17.0.

## 0.5.0 - 2020-07-23

**Changed**
- Update to support Blueprint v1.15.3 ([#22](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/22))

This release only supports Blueprint >= v1.15.3.

## 0.4.6 - 2020-07-23

**Changed**
- Requires specifically 1.15.2 of Blueprint as the next release has breaking changes. I will create a new release soon with support for 1.15.3.

## 0.4.5 - 2020-07-16

**Changed**
- Use assertCreated for new Resource responses

## 0.4.4 - 2020-07-10

**Added**
- Generate assertions for resource and update statements ([#20](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/20))

## 0.4.3 - 2020-07-05

**Fixed**
- Use factory for referenced relationships in validation statements ([#18](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/18))

## 0.4.2 - 2020-07-04

**Fixed**
- Fix test setup for validation shorthand ([#16](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/16))

## 0.4.1 - 2020-06-30

**Added**
- Add types method to support next blueprint release ([#14](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/14))

## 0.4.0 - 2020-06-28

**Added**
- Add Notifications testing support ([#13](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/13))

## 0.3.0 - 2020-06-24

**Changed**
- generated `assertActionUsesFormRequest` tests now imports controller and request classes ([1a70ee9](https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/1a70ee94dead15d2a7cef2c70b66c0cf77b6c68f))

**Fixed**
- generate related models using factories ([1a70ee9](https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/1a70ee94dead15d2a7cef2c70b66c0cf77b6c68f))

## 0.2.0 - 2020-06-08

**Changed**
- refactored statements into separate builder classes ([#12](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/12))

## 0.1.0 - 2020-05-30

**Fixed**
- use configured model namespace in tests ([#11](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/11))

## 0.0.2 - 2020-05-30

**Changed**
- use global assertion methods where available

## 0.0.1 - 2020-05-28

- initial release
