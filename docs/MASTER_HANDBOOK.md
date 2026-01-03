# 📜 ERP v2: Master Developer Handbook & Workbook
**The Complete Guide for Junior Developers & New Joiners**

Welcome to the **ERP v2** project. This document is your single source of truth for the project's architecture, standards, and workflow. It is designed to allow any developer to understand the base structure without external help.

---

## 🍽️ 1. The Core Philosophy: The Restaurant Analogy

Imagine building a feature is like running a **High-End Restaurant**. We separate roles so the Chef isn't also scrubbing the floors.

1.  **The Ingredient (Model)** - `app/Models/`  
    *Job:* Object representation of DB tables.  
    "I am a piece of data. I know my own properties and internal rules like `isActive()`."
    
2.  **The Librarian (Repository)** - `app/Repositories/`  
    *Job:* **Data Access Layer.** The ONLY one allowed to touch the Database.  
    "I know how to find ingredients in the dark pantry (SQL). I fetch them and return them as Model Objects."

3.  **The Master Chef (Service)** - `app/Services/`  
    *Job:* **Business Logic Layer.** Where calculations, validation, and complex rules live.  
    "I take the ingredients, check if they are fresh, cook them, and plate them."

4.  **The Waiter (Controller)** - `app/Controllers/`  
    *Job:* Handles HTTP requests. Should contain **zero** business logic.  
    "The customer ordered something! I'll tell the Chef to prepare it and then I'll take it to the table."

5.  **The Table (View)** - `views/`  
    *Job:* The UI templates. What the user actually sees.

---

## 🏗️ 2. The Request Lifecycle

When a user clicks a link, the app follows this exact path:

1.  **Browser** hits `public/index.php`.
2.  **Router** (`routes/web.php`) finds the matching path and handler (e.g., `EmployeeController@index`).
3.  **Container** (`app/Core/Container.php`) automatically resolves the Controller and injects Dependencies.
4.  **Controller** calls the **Service Layer**.
5.  **Service** calls the **Repository Layer** for data.
6.  **Repository** runs **SQL** (PDO) and returns a **Model Object**.
7.  **Controller** uses the `view()` helper to render the HTML.

---

## 📂 3. Directory Navigation Guide

| Directory | Responsibility |
| :--- | :--- |
| `app/Core` | Framework engine: Database connection, Base Model, DI Container. **DO NOT EDIT.** |
| `app/Controllers` | One file per module. Handles user input/session. Keep it "thin". |
| `app/Services` | The brain. If you are doing math or checking rules, it goes here. |
| `app/Repositories` | **SQL lives here only.** Never write a `SELECT` query anywhere else. |
| `app/Models` | Table definitions and entity logic. Use `$this->column_name`. |
| `views/` | HTML/PHP. Use layouts in `views/layouts/` for headers/footers. |
| `routes/` | `web.php` maps URLs to your code. |

---

## 📋 4. The Master 25-Point Checklist

*Follow these steps in order for every new feature. Tick them off as you go!*

### Phase A: Setup & Model
1.  [ ] **DB Schema**: Ensure your table name is plural and snake_case (e.g., `estimates`).
2.  [ ] **Model Creation**: Create `app/Models/MyEntity.php`.
3.  [ ] **Extends**: Does it extend the `Model` class?
4.  [ ] **Table Variable**: Is `protected $table = 'name';` defined correctly?
5.  [ ] **Entity Logic**: Did you add methods like `isApproved()` or `getShortName()` inside the Model?

### Phase B: Repository (The SQL Hunter)
6.  [ ] **Repository Creation**: Create `app/Repositories/MyEntityRepository.php`.
7.  [ ] **PDO**: Does it use the Database connection injected in the constructor?
8.  [ ] **Prepared Statements**: Does every query use `?` or named placeholders? (NO raw variables in queries!)
9.  [ ] **Mapping**: Does your `find()` method return `new MyEntity($row)`? (Returning arrays is forbidden).
10. [ ] **Clean SQL**: Did you avoid `SELECT *` if you only need certain columns?

### Phase C: Service (The Logic Brain)
11. [ ] **Service Creation**: Create `app/Services/MyEntityService.php`.
12. [ ] **Dependency Injection**: Did you avoid using `new UserRepository()`? (Let the Container inject it).
13. [ ] **Validation**: Are you checking if data is valid before saving?
14. [ ] **Business Rules**: Is all your "if/else" logic for this feature inside the Service?

### Phase D: Controller & Routing
15. [ ] **Controller Creation**: Create `app/Controllers/MyEntityController.php`.
16. [ ] **Thin Methods**: Are your controller methods shorter than 15 lines?
17. [ ] **View Helper**: Are you using `view('module.file', $data)`?
18. [ ] **Redirects**: Are you using `redirectWithFlash()` after saving data?
19. [ ] **Route Entry**: Is the GET/POST route added to `routes/web.php`?

### Phase E: View & UI
20. [ ] **Correct Folder**: Is the view in `views/module/filename.php`?
21. [ ] **Layout System**: Does it use the `$layout` variable in the `view()` call?
22. [ ] **Escaping**: Are you using `htmlspecialchars()` for everything you echo?
23. [ ] **Conditionals**: Are you using `isset()` or `??` for data passed to the view?

### Phase F: Final Polish
24. [ ] **Checklist Completion**: Did you remove all `var_dump()` and `die()`?
25. [ ] **Naming Rules**: Classes are `PascalCase`, methods are `camelCase`.

---

## 🚫 5. The "Wall of Shame" (Common Pitfalls)

| Pitfall | Why it's bad | Solution |
| :--- | :--- | :--- |
| **SQL in Model** | Violates Repository Pattern. | Move to Repository. |
| **`new` for Services** | Breaks Dependency Injection. | Use Constructor injection. |
| **Array Access** | `$user['email']` is slow and error-prone. | Use properties: `$user->email`. |
| **Raw $_POST in Repo** | Repository shouldn't know about HTTP. | Pass values as method arguments. |
| **Hardcoded URLs** | Fails on different servers. | Use `BASE_PATH` constant. |

---

## 🎓 6. Example Workflow: "I need to add a Discount column"

1.  **Database**: Add `discount` column to `invoices` table.
2.  **Model (`Invoice.php`)**: Add `public function getDiscountedTotal() { return $this->total - $this->discount; }`.
3.  **Repository (`InvoiceRepository.php`)**: Ensure `SELECT` queries include the `discount` column.
4.  **View**: Change `<?= $invoice->total ?>` to `<?= $invoice->getDiscountedTotal() ?>`.

---
*The ERP v2 Handbook - Building Quality Software Together*
