let events = [];
let currentPage = 1;
const itemsPerPage = 3;

fetch("../JS/eventlist.json")
    .then(response => {

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    })

    .then(data => {

        events = data;

        displayEvents();

    })

    .catch(error => {

        console.error("Error loading JSON:", error);

        const eventlist = document.getElementById("eventlist");

        if (eventlist) {
            eventlist.innerHTML =
                "<p>Error loading event list.</p>";
        }

    });

function displayEvents() {

    const eventlist = document.getElementById("eventlist");

    if (!eventlist) return;

    const searchText =
        document.getElementById("search").value
        .toLowerCase()
        .trim();

    const category =
        document.getElementById("filter").value;

    const sortValue =
        document.getElementById("sort").value;


    let filteredEvents = events.filter(event =>
        event.name.toLowerCase().includes(searchText)
    );
    if (category !== "all") {

        filteredEvents =
            filteredEvents.filter(
                event => event.category === category
            );
    }

    if (sortValue === "name") {

        filteredEvents.sort((a, b) =>
            a.name.localeCompare(b.name)
        );

    } else if (sortValue === "date") {

        filteredEvents.sort((a, b) =>
            new Date(a.date) - new Date(b.date)
        );

    } else if (sortValue === "id") {

        filteredEvents.sort((a, b) =>
            Number(a.id) - Number(b.id)
        );
    }

    const start =
        (currentPage - 1) * itemsPerPage;

    const end =
        start + itemsPerPage;

    const pageEvents =
        filteredEvents.slice(start, end);


    if (pageEvents.length === 0) {

        eventlist.innerHTML =
            "<h3>No events found</h3>";

        createPagination(0);

        return;
    }

    eventlist.innerHTML = pageEvents
        .map(event => `

            <div class="event">

                <h2>${escapeHtml(event.name)}</h2>

                <p>
                    <b>ID:</b>
                    ${escapeHtml(event.id)}
                </p>

                <p>
                    <b>Category:</b>
                    ${escapeHtml(event.category)}
                </p>

                <p>
                    <b>Date:</b>
                    ${escapeHtml(event.date)}
                </p>

                <p>
                    <b>Venue:</b>
                    ${escapeHtml(event.venue)}
                </p>

            </div>

        `)
        .join("");


    createPagination(filteredEvents.length);
}

function createPagination(totalItems) {

    const pagination =
        document.getElementById("pagination");

    if (!pagination) return;

    pagination.innerHTML = "";


    const totalPages =
        Math.ceil(totalItems / itemsPerPage);


    if (totalPages <= 1) return;


    for (let i = 1; i <= totalPages; i++) {

        const button =
            document.createElement("button");

        button.innerText = i;


        if (i === currentPage) {

            button.classList.add("active");

        }


        button.onclick = function () {

            currentPage = i;

            displayEvents();

        };


        pagination.appendChild(button);
    }
}

function escapeHtml(str) {

    return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.getElementById("search")
    .addEventListener("input", () => {

        currentPage = 1;

        displayEvents();

    });

document.getElementById("filter")
    .addEventListener("change", () => {

        currentPage = 1;

        displayEvents();

    });
document.getElementById("sort")
    .addEventListener("change", () => {

        currentPage = 1;

        displayEvents();

    });