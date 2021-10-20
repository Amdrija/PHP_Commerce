const AJAX = (function () {
    /**
     * Sends a request to the web server to update the category table.
     * @param uri
     * @param method
     * @param body
     * @returns {Promise<Response>}
     */
    async function sendJSONRequest(uri, method, body) {
        return await fetch(uri, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body),
        });
    }

    return {
        sendJSONRequest: sendJSONRequest,
    }
})();