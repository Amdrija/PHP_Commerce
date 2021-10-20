/**
 * Extract the category code from the uri. For example,
 * if the URI is '/category/LAP', it should return 'LAP'.
 * If it can't find a match, it returns null.
 * @param uri
 * @returns {string|null}
 */
function extractCategoryCodeFromURI(uri) {
    const categoryPathRegex = /\/category\/([\d\w]*)/;
    let matches = uri.toString().match(categoryPathRegex);

    return matches ? matches[1] : null;
}