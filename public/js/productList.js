(function () {
    function setQueryParameter(queryParameterName, value) {
        let address = new URL(window.location);
        let queryParameters = address.searchParams;
        console.log(address);
        console.log(queryParameters);
        queryParameters.set(queryParameterName, value);
        window.location = window.location.pathname.split("?")[0] + '?' + queryParameters.toString();
    }

    window.addEventListener('DOMContentLoaded', function () {
        document.getElementById('products-per-page-select').addEventListener('change', function (event) {
            setQueryParameter('productsPerPage', event.target.value);
        });

        document.getElementById('sort-by-select').addEventListener('change', function (event) {
            setQueryParameter('sortBy', event.target.value);
        })
    })
})();