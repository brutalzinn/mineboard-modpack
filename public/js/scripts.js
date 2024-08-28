function showEditForm(modpack) {
    hideModpackList();
    document.getElementById('id').value = modpack.id;
    document.getElementById('name').value = modpack.name;
    document.getElementById('game_version').value = modpack.gameVersion;
    document.getElementById('mod_loader').value = modpack.loader;
    document.getElementById('loader_version').value = modpack.loaderVersion;
    document.getElementById('zip_file').value = "";

    document.getElementById('form').classList.remove('hidden');
    document.getElementById('form-title').textContent = "Edit Modpack";
}

function hideModpackList() {
    document.getElementById('modpacks-list').classList.add('hidden');
}
function showModpackList() {
    document.getElementById('modpacks-list').classList.remove('hidden');
}
function hideForm() {
    document.getElementById('form').classList.add('hidden');
    showModpackList();
}

function showForm() {
    hideModpackList();
    document.getElementById('id').value = "";
    document.getElementById('zip_file').value = "";
    document.getElementById('form').classList.remove('hidden');
    document.getElementById('form-title').textContent = "Create Modpack";
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
            <p>Game Version: ${modpack.gameVersion}</p>
            <p>Mod Loader: ${modpack.loader}</p>
            <p>Loader Version: ${modpack.loaderVersion}</p>
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

function deleteModpack(id) {
    if (confirm(`Are you sure you want to delete the modpack '${id}'?`)) {
        fetch(`/?action=modpack&id=${id}`, {
            method: 'DELETE'
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


