
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management API</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px #00000015;
        }

        h1 {
            text-align: center;
            color: #243b64;
        }

        form {
            display: grid;
            gap: 12px;
            margin-bottom: 20px;
        }

        input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            padding: 9px 13px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 2px;
        }

        .primary {
            background: #2563eb;
            color: white;
        }

        .edit {
            background: #f59e0b;
            color: white;
        }

        .delete {
            background: #dc2626;
            color: white;
        }

        .cancel {
            background: #64748b;
            color: white;
        }

        #status {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            background: #eff6ff;
        }

        #status:empty {
            display: none;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 11px;
            text-align: left;
        }

        th {
            background: #243b64;
            color: white;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Student Management System</h1>

    <form id="studentForm">
        <input type="text" id="name"
               placeholder="Student Name" maxlength="100" required>

        <input type="email" id="email"
               placeholder="Email Address" maxlength="150" required>

        <input type="text" id="course"
               placeholder="Course" maxlength="100" required>

        <button type="submit" class="primary" id="saveBtn">
            Add Student
        </button>

        <button type="button" class="cancel"
                id="cancelBtn" hidden>
            Cancel Edit
        </button>
    </form>

    <div id="status" role="status" aria-live="polite"></div>

    <h2>Student Records</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="studentList"></tbody>
        </table>
    </div>
</div>

<script>
const API = "api_13.php";

const form = document.getElementById("studentForm");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const courseInput = document.getElementById("course");
const studentList = document.getElementById("studentList");
const statusBox = document.getElementById("status");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

let editingId = null;
let busy = false;

function setStatus(message) {
    statusBox.textContent = message;
}

function setBusy(value) {
    busy = value;
    saveBtn.disabled = value;
    cancelBtn.disabled = value;
}

async function requestAPI(action, data = null) {
    const options = data === null
        ? { method: "GET" }
        : {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ action, ...data })
        };

    const response = await fetch(API, options);

    let result;

    try {
        result = await response.json();
    } catch {
        throw new Error("Server returned an invalid JSON response");
    }

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed");
    }

    return result;
}

// Display all students
async function loadStudents() {
    if (busy) return;

    setBusy(true);
    setStatus("Loading students...");

    try {
        const result = await requestAPI("list");
        renderStudents(result.data || []);
        setStatus(result.message);
    } catch (error) {
        setStatus("Error: " + error.message);
    } finally {
        setBusy(false);
    }
}

// Build the table safely using textContent
function renderStudents(students) {
    studentList.replaceChildren();

    if (students.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");

        cell.colSpan = 5;
        cell.textContent = "No students found.";
        row.appendChild(cell);
        studentList.appendChild(row);
        return;
    }

    students.forEach(student => {
        const row = document.createElement("tr");

        [student.id, student.name, student.email, student.course]
            .forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value;
                row.appendChild(cell);
            });

        const actions = document.createElement("td");

        const editBtn = document.createElement("button");
editBtn.textContent = "Edit";
editBtn.type = "button";
editBtn.addEventListener("click", () => {
    editingId = Number(student.id);

    nameInput.value = student.name;
    emailInput.value = student.email;
    courseInput.value = student.course;

    saveBtn.textContent = "Update Student";
    cancelBtn.hidden = false;
});

const deleteBtn = document.createElement("button");
deleteBtn.textContent = "Delete";
deleteBtn.type = "button";
deleteBtn.addEventListener("click", () => {
    deleteStudent(Number(student.id));
});

        actions.append(editBtn, deleteBtn);
        row.appendChild(actions);
        studentList.appendChild(row);
    });
}

// Add or update a student
form.addEventListener("submit", async event => {
    event.preventDefault();

    if (busy) return;

    if (!form.reportValidity()) return;

    const data = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value.trim()
    };

    const action = editingId === null ? "add" : "update";

    if (editingId !== null) {
        data.id = editingId;
    }

    setBusy(true);
    setStatus(action === "add"
        ? "Adding student..."
        : "Updating student...");

    try {
        const result = await requestAPI(action, data);

        resetForm();
        await refreshStudents(result.message);
    } catch (error) {
        setStatus("Error: " + error.message);
    } finally {
        setBusy(false);
    }
});

// Delete a student
async function deleteStudent(id) {
    if (busy || !confirm("Delete student ID " + id + "?")) return;

    setBusy(true);
    setStatus("Deleting student...");

    try {
        const result = await requestAPI("delete", { id });
        await refreshStudents(result.message);
    } catch (error) {
        setStatus("Error: " + error.message);
    } finally {
        setBusy(false);
    }
}

async function refreshStudents(message) {
    const result = await requestAPI("list");
    renderStudents(result.data || []);
    setStatus(message);
}

function resetForm() {
    form.reset();
    editingId = null;
    saveBtn.textContent = "Add Student";
    cancelBtn.hidden = true;
}

cancelBtn.addEventListener("click", resetForm);

// Load records when the page first opens
loadStudents();
</script>
</body>
</html>
