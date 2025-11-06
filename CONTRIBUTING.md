# Contributing to SIMPUS

Thank you for considering contributing to SIMPUS! This document provides guidelines for contributing to the project.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Commit Message Guidelines](#commit-message-guidelines)

---

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

### Our Pledge

- Be respectful and inclusive
- Welcome newcomers and help them get started
- Accept constructive criticism gracefully
- Focus on what is best for the community

---

## How Can I Contribute?

### 🐛 Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**When reporting bugs, include:**
- Clear and descriptive title
- Steps to reproduce the behavior
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Environment information (PHP version, Laravel version, OS, etc.)

**Example:**
```
### Bug Description
Patient search tidak bekerja ketika menggunakan karakter spesial

### Steps to Reproduce
1. Buka halaman daftar pasien
2. Ketik "John's" di search box
3. Tidak ada hasil yang muncul

### Expected Behavior
Seharusnya menampilkan pasien dengan nama "John's"

### Environment
- Laravel: 12.0
- PHP: 8.2.24
- MySQL: 8.0.30
- Browser: Chrome 120
```

### ✨ Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues.

**When suggesting enhancements, include:**
- Clear and descriptive title
- Detailed description of the suggested enhancement
- Why this enhancement would be useful
- Examples of how it would work

### 💻 Code Contributions

1. Fork the repository
2. Create your feature branch
3. Make your changes
4. Test your changes
5. Submit a Pull Request

---

## Development Setup

### Prerequisites

- PHP >= 8.2.24
- Composer >= 2.x
- Node.js >= 18.x
- MySQL >= 8.0 or MariaDB >= 10.6
- Git

### Setup Steps

1. **Fork and clone:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/Simpus.git
   cd Simpus
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** in `.env`:
   ```env
   DB_DATABASE=simpus
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations & seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start development server:**
   ```bash
   composer run dev
   ```

### Running Tests

Before submitting PR, make sure all tests pass:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PatientTest.php

# Run with coverage
php artisan test --coverage
```

---

## Coding Standards

### PHP Code Style

We use **Laravel Pint** for code formatting. Always format code before committing:

```bash
# Format all files
vendor/bin/pint

# Format only changed files
vendor/bin/pint --dirty

# Test without formatting
vendor/bin/pint --test
```

### Code Style Guidelines

#### PHP
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use type hints for parameters and return types
- Write DocBlocks for all methods
- Use descriptive variable and method names
- Keep methods small and focused (max 20 lines preferred)

**Example:**
```php
/**
 * Create a new patient record.
 *
 * @param array $data Patient data
 * @return Patient
 * @throws ValidationException
 */
public function createPatient(array $data): Patient
{
    $validated = $this->validator->validate($data);

    return Patient::create($validated);
}
```

#### JavaScript
- Use ES6+ syntax
- Use `const` and `let`, avoid `var`
- Use meaningful variable names
- Add comments for complex logic

**Example:**
```javascript
// Good
const fetchPatients = async (query) => {
    try {
        const response = await fetch(`/api/patients?q=${query}`);
        return await response.json();
    } catch (error) {
        console.error('Failed to fetch patients:', error);
        throw error;
    }
};

// Bad
var a = async function(q) {
    var r = await fetch('/api/patients?q=' + q);
    return await r.json();
};
```

#### Blade Templates
- Keep logic minimal in templates
- Use Blade components for reusable UI
- Extract complex logic to controllers or view composers

### Database

- **Migrations:** Always use migrations for schema changes
- **Seeders:** Keep seeders idempotent (safe to run multiple times)
- **Naming:** Use snake_case for table and column names
- **Foreign Keys:** Always add foreign key constraints
- **Indexes:** Add indexes for frequently queried columns

### Testing

- Write tests for new features
- Follow **AAA pattern** (Arrange, Act, Assert)
- Use descriptive test method names
- Test both happy path and edge cases

**Example:**
```php
public function test_can_create_patient_with_valid_data(): void
{
    // Arrange
    $data = [
        'name' => 'John Doe',
        'nik' => '1234567890123456',
        'date_of_birth' => '1990-01-15',
    ];

    // Act
    $response = $this->post('/patients', $data);

    // Assert
    $response->assertStatus(201);
    $this->assertDatabaseHas('patients', ['name' => 'John Doe']);
}
```

---

## Pull Request Process

### 1. Create Feature Branch

Create a branch from `main`:

```bash
git checkout -b feature/amazing-feature
# or
git checkout -b fix/bug-fix
# or
git checkout -b docs/update-readme
```

**Branch naming:**
- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring
- `test/` - Adding tests
- `chore/` - Maintenance tasks

### 2. Make Changes

- Write clean, readable code
- Follow coding standards
- Add tests for new features
- Update documentation if needed

### 3. Commit Changes

Follow commit message guidelines (see below).

```bash
git add .
git commit -m "Add: patient search functionality"
```

### 4. Push to Fork

```bash
git push origin feature/amazing-feature
```

### 5. Create Pull Request

1. Go to your fork on GitHub
2. Click "New Pull Request"
3. Select base: `main` and compare: `feature/amazing-feature`
4. Fill in PR template:

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Tests added/updated
- [ ] Tests pass locally
- [ ] Documentation updated
- [ ] No new warnings

## Screenshots (if applicable)
Add screenshots here

## Related Issues
Closes #123
```

### 6. Code Review

- Address review comments
- Push new commits to the same branch
- PR will auto-update

### 7. Merge

After approval, maintainers will merge your PR.

---

## Commit Message Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Format

```
<type>: <subject>

[optional body]

[optional footer]
```

### Types

- `Add:` - New feature or functionality
- `Fix:` - Bug fix
- `Update:` - Update existing feature
- `Remove:` - Remove feature or code
- `Refactor:` - Code refactoring
- `Docs:` - Documentation only
- `Test:` - Adding or updating tests
- `Chore:` - Maintenance tasks
- `Style:` - Code style changes (formatting, etc.)
- `Perf:` - Performance improvements

### Examples

```bash
# Good commits
git commit -m "Add: patient search with autocomplete"
git commit -m "Fix: null pointer exception in lab module"
git commit -m "Update: BPJS integration to use database-driven status"
git commit -m "Docs: add BPJS integration guide"
git commit -m "Refactor: extract patient validation logic"

# Bad commits (avoid these)
git commit -m "update"
git commit -m "fix bug"
git commit -m "changes"
git commit -m "asdf"
```

### Commit Body (Optional)

For complex changes, add a body:

```
Add: patient search with autocomplete

- Implement TypeScript search component
- Add debounce to prevent excessive API calls
- Cache results for 5 minutes
- Add keyboard navigation support

Closes #45
```

---

## Project Structure

```
Simpus/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic
│   ├── Jobs/                # Queue jobs
│   └── Helpers/             # Helper functions
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── resources/
│   ├── views/               # Blade templates
│   ├── js/                  # JavaScript files
│   └── css/                 # CSS files
├── routes/
│   ├── web.php              # Web routes
│   ├── api.php              # API routes
│   └── console.php          # Console commands
├── tests/
│   ├── Feature/             # Feature tests
│   └── Unit/                # Unit tests
├── docs/                    # Documentation
├── public/                  # Public assets
└── storage/                 # Storage files
```

---

## Need Help?

- 📖 Read the [Documentation](docs/)
- 💬 Ask questions in [GitHub Discussions](https://github.com/Haluluya/Simpus/discussions)
- 🐛 Report bugs in [GitHub Issues](https://github.com/Haluluya/Simpus/issues)
- 📧 Contact maintainers

---

## License

By contributing to SIMPUS, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to SIMPUS! 🎉**
