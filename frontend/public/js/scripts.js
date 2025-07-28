document.addEventListener("DOMContentLoaded", function () {
    let themeConfig = {
        theme: "light",
        "theme-base": "gray",
        "theme-font": "serif",
        "theme-primary": "purple",
        "theme-radius": "1",
    };

    var form = document.getElementById("offcanvasSettings");
    var resetButton = document.getElementById("reset-changes");

    const applyThemeSettings = () => {
        for (var key in themeConfig) {
        const value = window.localStorage.getItem("tabler-" + key) || themeConfig[key];
        if (value) {
            document.documentElement.setAttribute(`data-bs-${key}`, value);

            // Centang radio sesuai value
            const radios = form.querySelectorAll(`[name="${key}"]`);
            radios.forEach((radio) => {
            radio.checked = radio.value === value;
            });
        }
        }
    };

    // Simpan perubahan dari form
    form.addEventListener("change", ({ target }) => {
        const { name, value } = target;
        if (themeConfig.hasOwnProperty(name)) {
        document.documentElement.setAttribute(`data-bs-${name}`, value);
        localStorage.setItem(`tabler-${name}`, value);
        }
    });

    // Tombol Reset
    resetButton.addEventListener("click", function () {
        for (var key in themeConfig) {
        document.documentElement.removeAttribute("data-bs-" + key);
        localStorage.removeItem("tabler-" + key);
        }
        applyThemeSettings();
    });

    // Tombol Dark/Light mode (tanpa reload, tanpa ubah URL)
    document.getElementById("enable-dark")?.addEventListener("click", (e) => {
        e.preventDefault();
        document.documentElement.setAttribute("data-bs-theme", "dark");
        localStorage.setItem("tabler-theme", "dark");
        form.querySelector('[name="theme"][value="dark"]').checked = true;
    });

    document.getElementById("enable-light")?.addEventListener("click", (e) => {
        e.preventDefault();
        document.documentElement.setAttribute("data-bs-theme", "light");
        localStorage.setItem("tabler-theme", "light");
        form.querySelector('[name="theme"][value="light"]').checked = true;
    });

    // Jalankan saat halaman dibuka
    applyThemeSettings();
});

function showAlert(type = 'info', message = '...') {
    const icons = {
        success: 'ti ti-check',
        error: 'ti ti-alert-circle',
        warning: 'ti ti-alert-triangle',
        info: 'ti ti-info-circle'
    };

    const alertId = `alert-${Date.now()}`; // ID unik
    // const alertHtml = `
    //     <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show mb-2" role="alert">
    //         <div class="alert-icon">
    //             <i class="icon ${icons[type] || icons.info}"></i>
    //         </div>
    //         ${message}
    //         <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    //     </div>
    // `;
    const alertHtml = `
        <div id="${alertId}" class="alert alert-important alert-${type} alert-dismissible" role="alert">
            <div class="alert-icon">
                <i class="icon ${icons[type] || icons.info}"></i>
            </div>
            <div>
                <h4 class="alert-heading">${message}</h4>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    `;

    // ⬅️ Ganti append jadi prepend supaya terbaru di atas
    $('#alert-container').prepend(alertHtml);

    // Scroll ke atas jika perlu
    $('html, body').animate({ scrollTop: 0 }, 'fast');

    // Auto-close
    setTimeout(() => {
        $(`#${alertId}`).alert('close');
    }, 5000);
}


function isLoopableObject(errors) {
    return typeof errors === 'object' && errors !== null && !Array.isArray(errors);
}

function parseXhrMessage(xhr) {
    let message = 'Terjadi kesalahan pada server.';

    if (xhr.responseJSON) {
        const res = xhr.responseJSON;
        if (res.message) {
            message = res.message;
        }

        if (res.errors) {
            const detail = Object.values(res.errors).join('<br>');
            message += `<br>${detail}`;
        }
    }

    return message;
}



