function showEditForm(modpack) {
    document.getElementById('id').value = modpack.id;
    document.getElementById('name').value = modpack.name;
    document.getElementById('game_version').value = modpack.game_version;
    document.getElementById('mod_loader').value = modpack.loader;
    document.getElementById('loader_version').value = modpack.loader_version;
    showForm("Edit Modpack");
}

function showForm(modpack) {
    document.getElementById('form')
    showForm("Create Modpack")
}

function hideForm() {
    document.getElementById('form').classList.add('hidden');
}

function showForm(title = 'Create Modpack') {
    document.getElementById('form').classList.remove('hidden');
    document.getElementById('form-title').textContent = title;
}

function setAction(action) {
    document.getElementById('editAction').value = action;
}

function fetchModpack(id) {
    fetch('/?action=fetch&id=' + id)
        .then(response => response.json())
        .then(data => {
            showEditForm(data);
        })
        .catch(error => {
            console.error('Error fetching modpack:', error);
        });
}

function fetchModpacks() {
    fetch('/?action=list') // Correct route to fetch the modpacks list
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('modpacks-list');
            list.innerHTML = '';
            data.forEach(modpack => {
                const div = document.createElement('div');
                div.innerHTML = `
            <h3>${modpack.name}</h3>
            <p>Game Version: ${modpack.game_version}</p>
            <p>Mod Loader: ${modpack.loader}</p>
            <p>Loader Version: ${modpack.loader_version}</p>
            <p>Files: <a href="${modpack.files}" target="_blank">View</a></p>
            <button onclick="fetchModpack('${modpack.id}')">Edit</button>
            <button class="danger-button" onclick="deleteModpack('${modpack.id}')">Delete</button>`;
                list.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error fetching modpacks:', error);
        });
}

function deleteModpack(name) {
    if (confirm(`Are you sure you want to delete the modpack '${name}'?`)) {
        fetch(`/?action=delete&name=${name}`, {
            method: 'GET'
        })
            .then(response => response.text())
            .then(text => {
                alert(text);
                fetchModpacks();
            })
            .catch(error => {
                console.error('Error deleting modpack:', error);
            });
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const progressBarFill = document.querySelector(".progress-bar-fill");
    const statusText = document.querySelector(".status");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();

        xhr.open("POST", "", true);

        xhr.upload.addEventListener("progress", function (e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBarFill.style.width = percentComplete + "%";
                statusText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
            }
        });

        xhr.addEventListener("load", function () {
            if (xhr.status == 200) {
                statusText.textContent = "Upload and extraction complete!";
            } else {
                statusText.textContent = "An error occurred!";
            }
        });

        xhr.send(formData);
    });

    fetchModpacks();
});


