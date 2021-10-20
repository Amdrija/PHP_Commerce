(function () {
    function getSelectedSKUs() {
        let selectedElements = document.querySelectorAll('input[type=checkbox].select-checkbox:checked');

        return Array.from(selectedElements).map((selectedElement) => selectedElement.name);
    }

    function batchAction(action) {
        let body = {
            SKUs: getSelectedSKUs()
        };

        if (action === 'enable') {
            body.enabled = true;
        } else if (action === 'disable') {
            body.enabled = false;
        }
        console.log(body);
        AJAX.sendJSONRequest(`/admin/products/actions/${action}`, 'POST', body).then(() => window.location.reload());
    }

    window.addEventListener('DOMContentLoaded', function () {
        let enableSelectedButton = document.getElementById('enable-selected');
        enableSelectedButton.addEventListener('click', function () {
                batchAction('enable');
            }
        );

        let disableSelectedButton = document.getElementById('disable-selected');
        disableSelectedButton.addEventListener('click', function () {
                batchAction('disable');
            }
        );

        let deleteSelectedButton = document.getElementById('delete-selected');
        deleteSelectedButton.addEventListener('click', function () {
                if (confirm("Do you want to delete selected products?")) {
                    batchAction('delete');
                }
            }
        );

        let deleteButtons = document.getElementsByClassName('delete-button');
        for (let deleteButton of deleteButtons) {
            deleteButton.addEventListener('click', function (event) {
                if (confirm("Do you want to delete this product?")) {
                    AJAX.sendJSONRequest(
                        `/admin/products/actions/delete`,
                        'POST',
                        {SKUs: [event.target.getAttribute('sku')]}
                    ).then(() => window.location.reload());
                }
            });

        }
    });
})();