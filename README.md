# **Laravel Code Formatter**  

A Laravel package for formatting Blade, Controller, and Model files using a custom JSON-based formatting configuration.  

---

## **📌 Installation**  

You can install the package via Composer:  

```sh
composer require umangunagar/laravel-code-formatter
```

### **Install Dependencies**  
After installing the package, run the following command to install the required dependencies:  

```sh
php artisan install:configuration
```

This package includes a default formatting configuration file (`formatter.json`). If you want to customize it, publish the configuration manually:  

```sh
php artisan vendor:publish --tag=formatter-config
```

This will create a `formatter.json` file in your project's root directory, allowing you to define custom formatting rules.

---

## **⚡ Usage**  

This package provides Artisan commands to format specific Laravel files efficiently.  

### **📂 Format Controllers**  

To format all controller files inside `app/Http/Controllers`:  

```sh
php artisan format:controllers
```

To format a specific controller:  

```sh
php artisan format:controllers ExampleController.php
```

To format all controllers inside a subdirectory:  

```sh
php artisan format:controllers Example
```

---

### **📂 Format Models**  

To format all model files inside `app/Models`:  

```sh
php artisan format:models
```

To format a specific model:  

```sh
php artisan format:models Example.php
```

To format all models inside a subdirectory:  

```sh
php artisan format:models Example
```

---

### **📂 Format Blade Files**  

To format all Blade files inside `resources/views`:  

```sh
php artisan format:blades
```

To format a specific Blade file:  

```sh
php artisan format:blades example.blade.php
```

To format all Blade files inside a subdirectory:  

```sh
php artisan format:blades Example
```

---

## **💡 Why Use This Package?**  

✔ **Custom Formatting Rules:** This package allows you to define your own formatting rules in `formatter.json`.  
✔ **Targeted Formatting:** Easily format specific directories, files, or subdirectories without manually specifying paths.  
✔ **Laravel-Friendly:** Provides structured Artisan commands for seamless integration into Laravel projects.  

---

## **🛠 Troubleshooting**  

If you encounter any issues, try the following:  

- Ensure the package is installed correctly:  

  ```sh
  composer require umangunagar/laravel-code-formatter
  ```

- If formatting commands are not recognized, clear the autoload cache:  

  ```sh
  composer dump-autoload
  ```

- Ensure you have published the configuration if needed:  

  ```sh
  php artisan vendor:publish --tag=formatter-config
  ```

---

## **📜 License**  

This package is open-source and available under the MIT License.