/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
// 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        let options = {
            searchable: true,
            perPage: 10,
            labels: {
                placeholder: "Buscar...",
                searchTitle: "Buscar dentro de la tabla",
                pageTitle: "Página {page}",
                perPage: "registros por página",
                noRows: "No se encontraron registros",
                info: "Mostrando {start} a {end} de {rows} registros",
                noResults: "Ningún resultado coincide con su consulta de búsqueda",
            }

        };
        new simpleDatatables.DataTable(datatablesSimple, options);
    }

    const loginFloatingControls = document.querySelectorAll('#layoutAuthentication .form-floating .form-control');
    if (loginFloatingControls.length) {
        loginFloatingControls.forEach((input) => {
            const defaultPlaceholder = input.getAttribute('placeholder') || '';

            const syncPlaceholder = () => {
                if (input.value.length > 0) {
                    input.placeholder = '';
                } else {
                    input.placeholder = defaultPlaceholder;
                }
            };

            input.addEventListener('input', syncPlaceholder);
            input.addEventListener('focus', syncPlaceholder);
            input.addEventListener('blur', syncPlaceholder);

            syncPlaceholder();
        });
    }
});
