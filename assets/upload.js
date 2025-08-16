// Assume fileFieldWrapper is already a reference to the wrapper div
const fileInput = fileFieldWrapper.querySelector('input[type="file"]');
const preview = fileFieldWrapper.querySelector('.preview');
const hiddenInput = fileFieldWrapper.querySelector('input[type="hidden"]');
const noscript = fileFieldWrapper.querySelector('noscript');

let uploadedFiles = [];

// --- Helper to update hidden input with FIDs ---
function updateHiddenField() {
    hiddenInput.value = JSON.stringify(uploadedFiles.map(f => f.fid));
}

// --- Helper to render a file entry with preview + remove button ---
function renderFile(fileObj) {
    const entry = document.createElement("div");
    entry.className = "file-entry";

    const p = document.createElement("p");

    if (fileObj.mime_type.startsWith("image/")) {
        const img = document.createElement("img");
        img.src = fileObj.uri;
        img.alt = fileObj.name;
        img.style.maxWidth = "100px";
        img.style.display = "block";
        img.style.margin = "5px 0";
        entry.appendChild(img);
    }

    p.textContent = `${fileObj.name} (${Math.round(fileObj.size / 1024)} KB) `;

    const btn = document.createElement("button");
    btn.type = "button";
    btn.textContent = "Remove";
    btn.dataset.fid = fileObj.fid;

    btn.addEventListener("click", async () => {
        btn.disabled = true;
        const removeSpinner = document.createElement("span");
        removeSpinner.className = "spinner";
        removeSpinner.style.marginLeft = "5px";
        btn.parentNode.appendChild(removeSpinner);

        try {
            const resp = await fetch("/core/remove.php?fid=" + encodeURIComponent(fileObj.fid));
            const result = await resp.json();
            if (result.success) {
                uploadedFiles = uploadedFiles.filter(f => f.fid != fileObj.fid);
                updateHiddenField();
                entry.remove();
            } else {
                alert("Error removing file: " + (result.error || "unknown"));
                btn.disabled = false;
                removeSpinner.remove();
            }
        } catch (err) {
            alert("Error removing file");
            btn.disabled = false;
            removeSpinner.remove();
        }
    });

    p.appendChild(btn);
    entry.appendChild(p);
    preview.appendChild(entry);
}

// --- Hydrate from <noscript> for edit forms ---
function hydrateFromNoscript() {
    if (noscript && noscript.textContent.trim()) {
        try {
            const files = JSON.parse(noscript.textContent.trim());
            console.log(files);
            if (files && files.hasOwnProperty(0) && files[0].hasOwnProperty("fid")) {
                files.forEach(fileObj => {
                    uploadedFiles.push(fileObj);
                    renderFile(fileObj);
                });
                updateHiddenField();
            }

        } catch (e) {
            console.error("Invalid JSON in noscript", e);
        }
    }
}

// --- Handle new uploads ---
fileInput.addEventListener("change", async (e) => {
    const files = e.target.files;
    if (!files.length) return;

    const entries = [];
    for (let file of files) {
        const entry = document.createElement("div");
        entry.className = "file-entry";
        entry.innerHTML = `<p>Uploading ${file.name} <span class="spinner"></span></p>`;
        preview.appendChild(entry);
        entries.push(entry);
    }

    const formData = new FormData();
    for (let file of files) {
        formData.append(fileInput.name, file);
    }

    try {
        const response = await fetch("/core/upload.php", { method: "POST", body: formData });
        const results = await response.json();

        results.forEach((fileObj, idx) => {
            const entry = entries[idx];
            console.log(fileObj, idx);
            if (fileObj.fid) {
                uploadedFiles.push(fileObj);
                updateHiddenField();
                entry.remove();
                renderFile(fileObj);
            } else {
                entry.textContent = fileObj.error || "Upload failed";
            }
        });

    } catch (err) {
        entries.forEach(entry => { entry.textContent = "Upload failed"; });
    }

    fileInput.value = "";
});

// --- Initialize existing files (for edit page) ---
hydrateFromNoscript();