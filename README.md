# Email Template Builder

Visual email template builder with responsive design and cross-client compatibility.

## Features

- Drag-and-drop interface
- Responsive templates
- MJML support
- Automatic CSS inlining
- Preview across email clients
- Variable/placeholder system
- Export to HTML
- Test email sending
- Template library

## Usage

```php
// Create template
$template = EmailTemplate::create([
    'name' => 'Welcome Email',
    'subject' => 'Welcome {{name}}!',
    'content' => $mjmlContent
]);

// Render with variables
$html = $template->render([
    'name' => 'John',
    'company' => 'Acme Inc'
]);

// Send email
Mail::send($html, $data, function($message) {
    $message->to('user@example.com');
});
```

## CLI

```bash
# Build template
php artisan email:build templates/welcome.mjml

# Test send
php artisan email:test welcome --to=test@example.com

# Export
php artisan email:export welcome --format=html
```

## Template Syntax

Variables: `{{variable}}`
Conditionals: `{% if premium %}...{% endif %}`
Loops: `{% for item in items %}...{% endfor %}`

## Requirements

- PHP 7.2+
- Laravel 6.0
