# PHP MVC

This is a simple PHP MVC framework. It is not meant to be used in production, but rather as a learning tool for those who are interested in learning how to build a simple MVC framework. It builds on the concepts of the [Laravel framework](https://laravel.com/). It uses the [Twig template engine](https://twig.symfony.com/) for rendering views. It uses [Composer](https://getcomposer.org/) for autoloading classes and [PHPUnit](https://phpunit.de/) for testing. As front-end it uses [Vite](https://vitejs.dev/) and [Tailwind CSS](https://tailwindcss.com/).

## Installation

1. Use the repository as a template to create a new repository.
2. Clone the repository.
3. Run `composer install` to install php dependencies.
4. Run `npm install` to install node dependencies.
5. Run `npm run dev` to build the front-end assets.
6. Run `npm run serve` to start the development server. Note that you run this command in a separate terminal window.
7. Open `http://localhost:4000` in your browser.

## Usage

### Models

Models are located in the `app/Models` directory. They use the `App\Libraries\Core\Database` class to interact with the database. Models are responsible for fetching data from the database.

#### Example model

```php
// app/Models/User.php
<?php

namespace App\Models;

use App\Libraries\Core\Database;

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all()
    {
        $this->db->query('SELECT * FROM users');
        return $this->db->many();
    }
}
```

or

```php
// app/Models/User.php
<?php

namespace App\Models;

use App\Libraries\Core\Database;

class User
{
    public static function all()
    {
        $db = Database::getInstance();
        $db->query('SELECT * FROM users');
        return $db->many();
    }
}
```

### Views

Views are located in the `resources/views` directory. They use the [Twig template engine](https://twig.symfony.com/) for rendering. Views are responsible for displaying data to the user.

#### Example view

```twig
{# resources/views/home/index.twig #}
{% include "layouts/base.twig" %}

{% block body %}
<main class="flex flex-col items-center p-3">
    <section class="max-w-6xl w-full p-2">
        <h2 class="text-3xl font-bold text-center">Welcome to the Users page</h2>
        <p class="text-center">This is the users page.</p>
    </section>
    <section class="max-w-6xl w-full p-2">
        <h3 class="text-3xl font-bold text-center">Users</h3>
        <ul class="flex flex-col items-center">
            {% for user in users %}
            <li class="flex flex-col items-center p-2 m-2 border border-gray-300 rounded-md">
                <p class="text-center">{{ user.name }}</p>
                <p class="text-center">{{ user.email }}</p>
            </li>
            {% endfor %}
        </ul>
    </section>
</main>
{% endblock %}
```

### Controllers

Controllers are located in the `app/Controllers` directory. They extend the `App\Libraries\Core\Controller` class. Controllers are responsible for handling requests and returning responses. Controllers are instantiated by the `App\Libraries\Core\Core` class. Note that the name of the
controller class must match the name of the file. For example, the `Homepages` class is located in the `app/Controllers/Homepages.php` file. The `Homepages` class is instantiated by the `App\Libraries\Core\Core` class when the user visits the `/` route.

#### Example controller

```php
// app/Controllers/Users.php
<?php

namespace App\Controllers;

use App\Libraries\Core\Controller;

class Users
{
    public function index()
    {
        // this method is called when the user visits the /users route
    }

    public function show($id)
    {
        // this method is called when the user visits the /users/{id} route
    }
}
```
