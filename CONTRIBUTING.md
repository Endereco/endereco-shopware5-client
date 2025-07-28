# Contributor Guidelines

## General Requirements

- **Repository Structure**: This project is a Shopware 5 plugin that supports multiple Shopware 5 versions:
    - Compatible with Shopware 5.3.0 and higher
    - Single `master` branch for all Shopware 5 versions
    - PHP 7.4+ and PHP 8.0+ compatible

- **Contribution Process**: All changes must be submitted via pull request after forking the repository

- **Branch Strategy**: Create feature branches from the `master` branch

## Development Environment Setup

For detailed development environment setup instructions, please see the [Development Setup section in README.md](README.md#development-setup).

## Code Style and Formatting

The project follows **PSR-12** coding standards. All code must pass the quality checks defined in `composer.json`:

- **PHP_CodeSniffer** - Enforces PSR-12 coding standards
- **PHPStan** - Static analysis across multiple Shopware versions
- **PHPMD** - Mess detection for code quality
- **PHP Compatibility** - Ensures compatibility with PHP 7.4+

Run quality checks using:
```bash
composer qa        # Run all checks
composer phpcs     # Code style check only
composer phpcbf    # Auto-fix code style issues
```

## Code Quality Standards

Every commit must meet these requirements:

1. **Stable State**: Each commit should leave the codebase in a working, stable state
2. **Quality Assurance**: Run `composer run qa` - it must pass without errors
3. **Installation Tests**: The plugin must be installable and uninstallable with every commit
4. **Shopware Version Compatibility**: Test your changes with multiple Shopware 5 versions (5.3.0, 5.3.7, 5.4.6, 5.5.10, 5.6.10, 5.7.19) using the provided test environments
5. **Don't break**: The existing functionality should not break 

## Commit Message Guidelines

Follow our commit message format:

### Structure
```
Title: Brief description in imperative mood (under 70 characters)

Body:
Explain to the reviewer WHY you did the changes the way you did it (4-5 sentences max).
Provide him with context he might not have.
Reference relevant issues, meetings, or discussions.
Keep lines under 70 characters for readability.
You are writing this text for a reviewer. Don't make his life hard.

For implementation details see [ISSUE-NUMBER].
```

### Requirements

**Title:**
- Use English and imperative language ("Add feature" not "Added feature")
- Answer "WHAT?" - summarize what the commit does
- Keep under 70 characters
- No issue numbers or prefixes e.g. "fix: " in the title

**Body:**
- Explain "WHY?" - provide context for the changes
- Reference issues, emails, or meetings with specific identifiers
- Use professional, neutral language
- Break lines at ~70 characters
- Include 4-5 sentences maximum. If you need, then perhaps the commit should be broken down a few commits

**Example Good Commit:**

```
Add keyboard navigation support for autocomplete dropdowns

Enable users to navigate autocomplete suggestions using keyboard inputs
to improve accessibility compliance and user experience. Users can now
use Tab, Enter, and arrow keys to interact with address suggestions
without requiring mouse input.

This change addresses accessibility requirements outlined in WCAG 2.1
guidelines and moves the solution towards better EAA compatibility.

For implementation details see DEV-456. Related accessibility audit
findings documented in DEV-789.
```

### What to Avoid

- Vague titles like "fixed stuff" or "updates"
- Multiple unrelated changes in one commit
- Missing context about why changes were made
- Unprofessional language or jokes
- Lines exceeding 70 characters
- Mixing different types of changes (bug fixes + new features + refactoring)
- Adding fixes for previous commits. Just amend them yourself. Please.
- Too much text
- Technical details of the implementation, unless they are not understandable from reading the code

## Version-Specific Considerations

**For PHP Compatibility:**
- Ensure compatibility with PHP 7.4 and PHP 8.0+
- Test with minimum PHP version requirements (PHP 7.4)
- Use modern PHP features while maintaining backward compatibility

**For Shopware 5 Compatibility:**
- Test with multiple Shopware 5 versions (5.3.0 - 5.7.19)
- Use the provided Docker environments in the `shops/` directory
- Ensure plugin works across all supported Shopware versions

## Pull Request Requirements

Before submitting your PR:

1. ✅ All commits follow the message guidelines above
2. ✅ `composer run qa` passes without errors
3. ✅ Plugin installs and uninstalls successfully
4. ✅ Feature branch created from `master` branch
5. ✅ Shopware 5 version compatibility tested across supported versions
6. ✅ PHP version compatibility verified (PHP 7.4 and 8.0+)

## Quality Checklist

Use this checklist for each commit:

- [ ] Commit has clear, imperative title under 70 characters
- [ ] Body explains business reason/context for changes
- [ ] Professional language used throughout
- [ ] Lines broken at ~70 characters for readability
- [ ] References to relevant issues/meetings included
- [ ] Code passes `composer run qa`
- [ ] Plugin installation/uninstallation works
- [ ] Changes are logically grouped (not mixing unrelated modifications)
- [ ] There are no fixes for previous commits in new commits
- [ ] Shopware 5 compatibility considered and tested across supported versions

## Getting Help

If you're unsure about any of these requirements or need clarification on the commit message format, please ask in the issue comments before starting work. We're happy to provide guidance to ensure your contribution meets our standards.

---

*Note: These guidelines ensure code quality, maintainability, and a clear project history. Following them helps reviewers understand your changes and makes the codebase easier to maintain long-term.*

---