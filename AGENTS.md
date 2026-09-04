# AGENTS.md

## 1. Project Overview

This repository contains **CRUDBerita**, a simple news portal built with
Laravel.

The application has two main areas:

-   **Public Portal** --- visitors can read published news.
-   **Admin Dashboard** --- authenticated admins can manage categories
    and news articles.

The primary goal is to produce a clean, understandable Laravel CRUD
application that can be explained during project validation.

------------------------------------------------------------------------

## 2. Core Development Rules

### MUST

-   Use Laravel conventions whenever possible.
-   Keep the implementation simple and readable.
-   Build features incrementally.
-   Validate all user-controlled input on the server.
-   Protect admin routes with authentication middleware.
-   Use Eloquent for database operations.
-   Use Blade for server-rendered pages.
-   Use Tailwind CSS for styling.
-   Use environment variables for secrets and environment-specific
    configuration.
-   Run relevant tests/checks after making changes.
-   Keep migrations, models, controllers, routes, and views logically
    separated.
-   Write code that the developer can understand and explain.

### MUST NOT

-   Do not use source code copied from another repository or project.
-   Do not use WordPress or another CMS.
-   Do not introduce unnecessary frameworks or dependencies.
-   Do not hardcode passwords, API keys, tokens, or database
    credentials.
-   Do not commit `.env`.
-   Do not over-engineer the application.
-   Do not add features that are outside the PRD unless explicitly
    requested.
-   Do not silently change existing behavior unrelated to the current
    task.

------------------------------------------------------------------------

## 3. Technology Stack

Use:

-   PHP
-   Laravel
-   Blade
-   Tailwind CSS
-   Vite
-   Eloquent ORM
-   SQLite for simple development unless the project configuration
    specifies another database

Do not replace Laravel with another backend framework.

------------------------------------------------------------------------

## 4. Application Architecture

Follow the normal Laravel request flow:

``` text
Browser
   ↓
Route
   ↓
Middleware
   ↓
Controller
   ↓
Validation
   ↓
Model / Eloquent
   ↓
Database
   ↓
Controller
   ↓
Blade View
   ↓
Browser
```

Avoid putting large amounts of business logic directly inside Blade
templates.

Avoid putting unrelated responsibilities inside controllers.

------------------------------------------------------------------------

## 5. Database Rules

Main entities:

``` text
User
Category
Article
```

Relationship:

``` text
Category 1 ──────── * Article
```

Laravel relationship:

``` php
Category -> hasMany(Article::class)
Article  -> belongsTo(Category::class)
```

### Categories

Required fields:

``` text
id
name
slug
created_at
updated_at
```

Rules:

-   `name` is required.
-   `slug` is required.
-   `slug` must be unique.

### Articles

Required fields:

``` text
id
category_id
title
slug
excerpt
content
thumbnail
status
published_at
created_at
updated_at
```

Rules:

-   `category_id` must reference an existing category.
-   `title` is required.
-   `slug` must be unique.
-   `excerpt` is required.
-   `content` is required.
-   `thumbnail` may be null.
-   `status` must be `draft` or `published`.
-   New articles default to `draft`.
-   `published_at` may be null.

------------------------------------------------------------------------

## 6. CRUD Requirements

### Category CRUD

Implement:

-   Create
-   Read
-   Update
-   Delete

### Article CRUD

Implement:

-   Create
-   Read
-   Update
-   Delete

Use RESTful route conventions where practical.

Prefer Laravel resource controllers/routes when they make the
implementation clearer.

------------------------------------------------------------------------

## 7. Public Portal Rules

Public users can:

-   View homepage.
-   View published article list.
-   View article detail.
-   Search published articles.
-   Filter published articles by category.

Important:

**Draft articles must never appear in public listings, search results,
category pages, or public detail pages.**

When querying public articles, always make the publication condition
explicit and easy to understand.

------------------------------------------------------------------------

## 8. Admin Rules

Admin pages must require authentication.

Unauthenticated users must not be able to access:

``` text
/admin
/admin/articles/*
/admin/categories/*
```

Admin features:

-   Dashboard.
-   Article CRUD.
-   Category CRUD.

Dashboard statistics may include:

-   Total articles.
-   Published articles.
-   Draft articles.
-   Total categories.

Do not create a complicated analytics system.

------------------------------------------------------------------------

## 9. Validation Rules

Validate incoming data using Laravel validation.

Example article validation:

``` php
[
    'category_id' => ['required', 'exists:categories,id'],
    'title' => ['required', 'string', 'max:255'],
    'slug' => ['required', 'string', 'max:255'],
    'excerpt' => ['required', 'string'],
    'content' => ['required', 'string'],
    'thumbnail' => ['nullable', 'image'],
    'status' => ['required', 'in:draft,published'],
]
```

Adjust exact rules when necessary, but never remove important validation
merely to make a request succeed.

For updates, ensure unique fields correctly ignore the current record.

------------------------------------------------------------------------

## 10. Security Rules

Always consider security when modifying the application.

### Authentication

Use Laravel's authentication mechanism.

### Authorization

Admin functionality must not be publicly accessible.

### CSRF

Use Laravel's normal CSRF protection for state-changing forms.

### Mass Assignment

Configure model `$fillable` or `$guarded` appropriately.

Do not blindly accept arbitrary request fields.

### XSS

Do not render untrusted HTML as raw HTML unless it has been
intentionally sanitized and the behavior is required.

Prefer normal Blade escaping.

### SQL Injection

Use Eloquent/query builder parameterization.

Do not build SQL queries by concatenating user input.

### File Upload

For thumbnails:

-   Validate file type.
-   Validate file size.
-   Store files through Laravel Storage.
-   Store the resulting path in the database.
-   Do not trust the original filename or extension.

### Secrets

Never put secrets in:

``` text
.php
.blade.php
.js
.css
README.md
```

Use `.env` and `.env.example`.

------------------------------------------------------------------------

## 11. File Structure

Prefer standard Laravel structure:

``` text
app/
├── Http/
│   └── Controllers/
├── Models/

database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── layouts/
    ├── articles/
    ├── categories/
    └── admin/

routes/
└── web.php
```

Do not create unnecessary architectural layers unless the project
actually needs them.

For this project, simple controllers + models + Blade views are
preferred.

------------------------------------------------------------------------

## 12. UI Guidelines

Use Tailwind CSS.

### Public UI

Style:

-   Clean
-   Modern
-   Editorial/news oriented
-   Responsive
-   Easy to scan

### Admin UI

Style:

-   Simple dashboard
-   Clear navigation
-   Tables for records
-   Forms for create/update
-   Clear success/error messages

Do not spend excessive development time on visual effects.

Functionality and clarity have priority.

------------------------------------------------------------------------

## 13. Naming Conventions

Follow Laravel/PHP conventions.

Examples:

``` text
Article
Category
ArticleController
CategoryController
DashboardController
```

Methods:

``` text
index
create
store
show
edit
update
destroy
```

Variables should communicate meaning.

Prefer:

``` php
$article
$category
$publishedArticles
```

Avoid:

``` php
$x
$data1
$tmp
$foo
```

unless there is a clear local reason.

------------------------------------------------------------------------

## 14. AI Coding Workflow

When asked to implement a feature:

### Step 1 --- Inspect

Understand the existing relevant files before changing them.

### Step 2 --- Plan

Identify:

-   Routes needed.
-   Controller changes.
-   Model changes.
-   Migration changes.
-   Views required.
-   Validation required.
-   Tests/checks required.

### Step 3 --- Implement

Make the smallest reasonable set of changes.

### Step 4 --- Verify

Run appropriate checks such as:

``` bash
php artisan test
php artisan migrate
php artisan route:list
```

and frontend/build checks when applicable.

Do not claim a feature works without actually checking it when
verification is available.

### Step 5 --- Explain

After completing a task, summarize:

1.  What changed.
2.  Which files changed.
3.  Why the change was needed.
4.  How it works.
5.  How it was verified.

Keep explanations understandable to a beginner.

------------------------------------------------------------------------

## 15. Migration Rules

When database structure changes:

-   Create a migration instead of manually modifying the database.
-   Make migrations deterministic.
-   Keep migrations focused.
-   Do not delete existing migration history merely to hide errors.
-   Consider existing data before changing or removing columns.

For a new development database, running:

``` bash
php artisan migrate:fresh --seed
```

is acceptable when explicitly requested.

Do not run destructive database commands against a production database.

------------------------------------------------------------------------

## 16. Seeder Rules

Provide development seed data:

-   One admin user.
-   Several categories.
-   Several dummy articles.

Use fictional/sample content.

Do not scrape or copy real news articles.

Seeders must be safe to run repeatedly when practical.

------------------------------------------------------------------------

## 17. Testing

At minimum verify:

### Authentication

-   Guest cannot access admin.
-   Authenticated admin can access admin.

### Category

-   Create works.
-   Read works.
-   Update works.
-   Delete works.
-   Validation works.

### Article

-   Create works.
-   Read works.
-   Update works.
-   Delete works.
-   Validation works.
-   Category relationship works.
-   Thumbnail validation works.

### Public

-   Published articles appear.
-   Draft articles do not appear.
-   Search works.
-   Category filtering works.
-   Article detail works.

------------------------------------------------------------------------

## 18. Git Rules

Use clear conventional commit messages.

Examples:

``` text
chore: initialize laravel project
feat: add category migration and model
feat: implement category crud
feat: implement article crud
feat: add admin authentication
feat: add public article pages
feat: add article search
fix: prevent draft articles from public pages
docs: add project documentation
```

Do not commit:

``` text
.env
vendor/
node_modules/
storage/logs/*
```

unless explicitly required by the project.

------------------------------------------------------------------------

## 19. README Requirements

README must include:

-   Project description.
-   Features.
-   Tech stack.
-   Requirements.
-   Installation.
-   Environment configuration.
-   Database configuration.
-   Migration instructions.
-   Seeder instructions.
-   Development server instructions.
-   Admin development account information.
-   Basic project structure.

Never put real secrets into README.

------------------------------------------------------------------------

## 20. Scope Control

The following are **not required** unless explicitly requested:

-   Comments.
-   Likes.
-   Bookmarks.
-   Notifications.
-   Email newsletters.
-   Real-time features.
-   Advanced analytics.
-   Recommendation algorithms.
-   Microservices.
-   REST API.
-   GraphQL.
-   WebSockets.
-   Complex role/permission systems.
-   Rich text editor.
-   Payment integration.

If a requested feature is not necessary for the PRD, ask before adding
significant complexity.

------------------------------------------------------------------------

## 21. Definition of Done

A task is complete only when:

-   Code is implemented.
-   Relevant validation exists.
-   Relevant security concerns are handled.
-   Existing functionality is not unnecessarily broken.
-   The application passes relevant checks/tests.
-   The implementation follows Laravel conventions.
-   The code is understandable.
-   The change can be explained by the developer.

------------------------------------------------------------------------

## 22. Important Instruction for Validation

This project will be reviewed through a validation process where the
developer may be asked to explain the code.

Therefore:

**Do not optimize only for "it works." Optimize for "it works and the
developer understands why."**

Prefer:

``` text
Simple + clear + secure
```

over:

``` text
Complex + clever + difficult to explain
```

When there are multiple valid implementations, choose the one that is
easiest for a Laravel beginner to understand while still following good
engineering practices.
