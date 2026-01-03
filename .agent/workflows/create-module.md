---
description: How to create a new module (Model, Repository, Service, Controller)
---

# Create a New Module Workflow

Follow these steps to ensure the new module follows the architecture:

1. **Create the Model**
   - File: `app/Models/[Name].php`
   - Extend `Model` class.
   - Define `protected $table`.

2. **Create the Repository**
   - File: `app/Repositories/[Name]Repository.php`
   - Handle all PDO queries here.
   - Return model instances using `mapToModel()`.

3. **Create the Service**
   - File: `app/Services/[Name]Service.php`
   - Implement business logic.

4. **Create the Controller**
   - File: `app/Controllers/[Name]Controller.php`
   - Handle routing and user input.

5. **Create the Views**
   - Folder: `views/[folder]/`
   - Use partials for headers/footers.
