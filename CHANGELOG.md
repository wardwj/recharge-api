# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Initial Release (v1.0.0)

#### Core Features
- Multi-version support for Recharge API (2021-01, 2021-11)
- Automatic cursor-based pagination
- Type-safe DTOs with PHP 8.2+ enums
- Simple, intuitive API design

#### Resources
- Subscriptions (list, get, create, update, delete, cancel, activate)
- Customers (list, get, create, update, delete)
- Addresses (list, get, create, update, delete)
- Charges (list, get, skip, refund, process)
- Orders (list, get, update, delete, clone)
- Products (list, get, create, update, delete)
- Store (get)

#### Quality
- PSR-12 code style
- PHPStan Level 7 analysis
- 103 tests with 314 assertions
- Comprehensive error handling
- Git hooks for code quality

#### Developer Experience
- Automatic pagination handling
- Named parameter API (no complex builders)
- Clear exception messages
- PSR-3 logging support
- Composer scripts for quality checks

---

## Semantic Versioning

This project follows [Semantic Versioning](https://semver.org/):

- **MAJOR** - Incompatible API changes
- **MINOR** - New features (backward compatible)
- **PATCH** - Bug fixes (backward compatible)
