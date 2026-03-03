# Security Policy

## Supported versions

| Version | Supported |
|---------|-----------|
| 1.x     | ✅ Yes    |

Only the latest minor release of the current major version receives security fixes.

## Reporting a vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

If you discover a security vulnerability in Hephaestus, please disclose it responsibly by emailing:

**ariel@aurora-php.dev**

Include as much of the following as possible:

- A description of the vulnerability and its potential impact
- Steps to reproduce or a proof-of-concept
- Affected versions
- Any suggested fix (optional)

You will receive an acknowledgement within **48 hours** and a resolution timeline within **7 days**.

## Scope

Hephaestus is a CLI framework library. Vulnerabilities most relevant to this project include:

- Arbitrary code execution via crafted input passed to commands
- Path traversal in `CommandLoader` file discovery
- Unsafe deserialization in `CommandCache`
- Attribute parsing that allows injection of unintended behavior

Issues related to how applications built *on top of* Hephaestus handle user input are generally out of scope, but we appreciate the report anyway.

## Disclosure policy

Once a fix is released, the vulnerability will be disclosed publicly via a GitHub Security Advisory with full credit to the reporter (unless you prefer to remain anonymous).
