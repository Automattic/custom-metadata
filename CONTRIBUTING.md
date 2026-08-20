# Contributing to Custom Metadata Manager

Thank you for your interest in improving Custom Metadata Manager. Contributions of all kinds are welcome, whether that is reporting a bug, suggesting an enhancement, improving the documentation, or submitting a code change. This guide explains how to set up a local environment and how we work so that your contribution can be reviewed and merged smoothly.

## Code of Conduct

We want this to be a welcoming and respectful project for everyone. Please be considerate and constructive in all interactions, whether in issues, pull requests or discussions.

## Getting Started

You will need Git, [Composer](https://getcomposer.org/), and [Node.js](https://nodejs.org/) (which provides npm) installed.

1. Fork and clone the repository:

   ```bash
   git clone https://github.com/Automattic/custom-metadata.git
   cd custom-metadata
   ```

2. Install the PHP dependencies:

   ```bash
   composer install
   ```

3. Install [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) if you do not already have it, then start a local WordPress environment:

   ```bash
   npm -g install @wordpress/env
   wp-env start
   ```

   Inside the wp-env container, the plugin is mounted at `wp-content/plugins/custom-metadata` and is activated for you. Once the environment is running you can visit the local site in your browser to try the plugin out.

## Development Workflow

We use a branching model where day-to-day work happens against `develop`, and `main` holds the stable, released code.

1. Create a branch from `develop`:

   ```bash
   git checkout develop
   git pull
   git checkout -b my-change
   ```

2. Make your changes.
3. Add or update tests to cover the behaviour you have changed.
4. Run the linting and tests locally (see below) and make sure they pass.
5. Open a pull request against `develop`.

## Code Standards

The plugin is written in procedural PHP. We follow the [WordPress VIP Coding Standards](https://github.com/Automattic/VIP-Coding-Standards), enforced through PHP_CodeSniffer.

Check your changes against the standards:

```bash
composer cs
```

Many issues can be fixed automatically:

```bash
composer cs-fix
```

Please make sure `composer cs` reports no errors before opening a pull request.

## Testing

Integration tests run through wp-env, so make sure your environment is started (`wp-env start`) first.

Run the single-site suite:

```bash
composer test:integration
```

Run the multisite suite:

```bash
composer test:integration-ms
```

Integration tests live in `tests/Integration/` and extend `Yoast\WPTestUtils\WPIntegration\TestCase`. When adding tests, please follow the Arrange-Act-Assert pattern and give each test a descriptive name that explains the behaviour being verified.

## Pull Request Guidelines

- Keep each pull request focused on a single change; smaller PRs are easier to review and merge.
- Write a clear description explaining what the change does and why.
- Reference any related issues, for example `Fixes #123`, so they are linked and closed automatically.
- Make sure all continuous integration checks pass.
- Do not commit dependencies (`vendor/`) or other generated files.

## Releases

Releasing is handled by the maintainers. In short, tagging a release on `main` triggers the GitHub release and WordPress.org deploy workflows, which build and publish the plugin. Contributors do not need to do anything for a release beyond getting their changes merged into `develop`.
