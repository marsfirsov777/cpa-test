        </main>
        <footer class="bg-dark text-white text-center py-5">
            <span>All rights reserved | © 2025</span>
        </footer>
            
        <div class="modal fade" id="successModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Дякуємо!</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Ваші дані успішно відправлені, чекайте на двзінок.
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="errorModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Помилка!</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Нажаль сталася помилка при відправці данних, спробуйте ще.
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('.action-form');

                if(form) {

                    const successModal = document.getElementById('successModal')
                    const errorModal = document.getElementById('errorModal')

                    form.addEventListener('submit', function (e) {
                        e.preventDefault(); // зупиняємо стандартну відправку

                        // HTML5 + Bootstrap валідація
                        if (!form.checkValidity()) {
                            form.classList.add('was-validated');
                            return;
                        }

                        // Збираємо дані з форми
                        const formData = new FormData(form);

                        // Відправляємо через fetch
                        fetch('/api-add-lead.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Помилка HTTP: ' + response.status);
                            }
                            return response.json(); // або .json() — залежно від відповіді сервера
                        })
                        .then(data => {

                            let modal;

                            if(data.status && data.status === true) {
                                // Створюємо екземпляр модального вікна
                                modal = new bootstrap.Modal(successModal);

                                // Очистити форму та прибрати "was-validated"
                                form.reset();
                                form.classList.remove('was-validated');
                            }
                            else
                            {
                                // Створюємо екземпляр модального вікна
                                modal = new bootstrap.Modal(errorModal);
                            }

                            // Відкриваємо модальне вікно
                            modal.show();
                        })
                        .catch(error => {
                            
                            // Створюємо екземпляр модального вікна
                            const modal = new bootstrap.Modal(errorModal);

                            // Відкриваємо модальне вікно
                            modal.show();

                            console.error('Помилка:', error);
                        });
                    });
                }
            });
        </script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

        let currentPage = 0;
        let totalPages = 10;
        const limit = 100;

        const form = document.getElementById('filter-form');

        if(form) {

            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            const loader = document.getElementById('loader');

            async function loadPage(page = 0) {
                currentPage = page;

                const params = new URLSearchParams();
                params.append('page', page);

                const dateFrom = dateFromInput.value;
                const dateTo = dateToInput.value;
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                loader.classList.remove('d-none');

                try {
                    const res = await fetch('/api-get-statuses.php?' + params.toString());
                    const json = await res.json();

                    loader.classList.add('d-none');

                    if (!json.status) throw new Error('API повернул помилку');

                    renderTable(json.data, page);
                    /* totalPages = json.total ? Math.ceil(json.total / limit) : 10;
                    renderPagination(currentPage, totalPages); */
                } catch (err) {
                    alert('Помилка завантаження ' + err.message);
                    loader.classList.add('d-none');
                }
            }

            function renderTable(items, page) {
                const tbody = document.querySelector('#statuses-table tbody');
                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Немає данних</td></tr>';
                    return;
                }

                items.forEach((item, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.email}</td>
                    <td>${item.status != '' ? item.status : 'Невідомо' }</td>
                    <td>${item.ftd}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function renderPagination(currentPage, totalPages) {
                const ul = document.getElementById('pagination');
                ul.innerHTML = '';

                const createItem = (page, label = null, disabled = false, active = false) => {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.textContent = label ?? (page + 1);
                    if (!disabled && !active) {
                    a.addEventListener('click', e => {
                        e.preventDefault();
                        loadPage(page);
                    });
                    }
                    li.appendChild(a);
                    return li;
                };

                ul.appendChild(createItem(Math.max(0, currentPage - 1), '←', currentPage <= 0));

                const maxVisible = 10;
                let start = Math.max(0, currentPage - Math.floor(maxVisible / 2));
                let end = Math.min(totalPages - 1, start + maxVisible - 1);
                if (end - start < maxVisible) start = Math.max(0, end - maxVisible + 1);

                if (start > 0) {
                    ul.appendChild(createItem(0));
                    if (start > 1) {
                    ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                for (let i = start; i <= end; i++) {
                    ul.appendChild(createItem(i, null, false, i === currentPage));
                }

                if (end < totalPages - 1) {
                    if (end < totalPages - 2) {
                    ul.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                    ul.appendChild(createItem(totalPages - 1));
                }

                ul.appendChild(createItem(Math.min(totalPages - 1, currentPage + 1), '→', currentPage >= totalPages - 1));
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                loadPage(0);
            });
        }
        });
        </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const header = document.querySelector('header');

            window.addEventListener('scroll', function () {

                if (window.scrollY > 200) {
                    header.classList.add('fixed');
                } else {
                    header.classList.remove('fixed');
                }
            });
        });
    </script>
    </body>
</html>