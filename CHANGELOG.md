# Changelog

All notable changes to `fidum/laravel-blueprint-pestphp-addon` will be documented in this file

## 2.2.4 - 2023-02-10

### What's Changed

- Fixing missing model import in generated tests for show-only controllers by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/37

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.2.3...2.2.4

## 2.2.3 - 2022-02-26

## What's Changed

- Replace assertDeleted with assertModelMissing by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/35

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.2.2...2.2.3

## 2.2.2 - 2022-02-18

## What's Changed

- Fix job test when property name different to context by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/34

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.2.1...2.2.2

## 2.2.1 - 2022-02-14

## What's Changed

- Add PHP 8 property types by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/32

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.2.0...2.2.1

## 2.2.0 - 2022-02-13

## What's Changed

- Laravel 9 Compatibility by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/31

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.1.0...2.2.0

## 2.1.0 - 2022-02-09

## Whats Changed?

- Support laravel-shift/blueprint 2.2.0 by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/394c2e4f82c6f57be7eb2f19b53fd35033abaa7a

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.0.1...2.1.0

## 2.0.1 - 2022-01-04

**Added**

- Run actions on PHP 8.1 by @dmason30 in https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/29

**Full Changelog**: https://github.com/fidum/laravel-blueprint-pestphp-addon/compare/2.0.0...2.0.1

## 2.0.0 - 2021-08-17

**Added**

- Only support latest stable Laravel version ([#28](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/28))

This release only supports Blueprint >= v2.0.0

Version 2.x is a superficial major release to reflect Blueprint's new [Support Policy](https://github.com/laravel-shift/blueprint#support-policy).

## 1.1.0 - 2021-03-20

**Added**

- Add nested models support ([#26](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/26))

This release only supports Blueprint >= v1.23.0.

## 1.0.0 - 2021-01-07

Stable release to coincide with Pest version.

**Added**

- Add PHP 8 support ([#25](https://github.com/fidum/laravel-blueprint-pestphp-addon/pull/25))

## 0.6.2 - 2020-10-23

**Changed**

- Use assertSoftDeletes when needed and parse timestamps ([2b732ad](https://github.com/fidum/laravel-blueprint-pestphp-addon/commit/2b732adbd71455b9bb2d023cfa03f7b303bff4f3))

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
