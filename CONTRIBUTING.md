# Contributing

Thanks for your interest in contributing to Filament Calendar.

## Development setup

This package lives inside a Laravel application. After cloning the repository:

```bash
composer install
```

To run the tests:

```bash
composer test     # Pest, PHP
composer test:js  # node, the Alpine calendar
```

To work on the documentation site:

```bash
cd docs
npm install
npm run dev
```

## Pull requests

1. Fork the repository and create a branch from `main`.
2. Make focused changes with clear commit messages.
3. Run `composer test` when your change affects PHP code, and `composer test:js` when it touches `resources/js`.
4. Open a pull request with a short summary of the change and why it is needed.

## Reporting issues

Please open a [GitHub issue](https://github.com/AsmitNepali/fila-calendar/issues) with:

- A clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Your PHP, Laravel, and Filament versions

## Security issues

Do not open public issues for security vulnerabilities. See [SECURITY.md](SECURITY.md).

## Code style

- Follow existing conventions in the package.
- Run `vendor/bin/pint` on changed PHP files before submitting.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE.md).
