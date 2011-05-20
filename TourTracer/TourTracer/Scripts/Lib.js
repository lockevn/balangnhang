function log(s) {
    $('#message').append(s + '<br />');
}

function AddCSSFileToDocument(pHref) {
    var link = $("<link>");
    link.attr({
        type: 'text/css',
        rel: 'stylesheet',
        href: pHref
    });
    $("head").append(link);
}