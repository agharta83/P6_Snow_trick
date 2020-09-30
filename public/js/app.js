var app = {
    // Propriété de mon objet app
    nbClick: 1, // Btn Load Tricks

    // Méthode de mon objet app
    init: () => {
        app.basePath = $('body').data('path');
        $('#olderTricks').on('click', app.loadOlderTricks);
    },

    // On définit les méthodes
    loadOlderTricks: () => {
        app.nbClick++;
        let newOffset = 6 * app.nbClick;
        const url = app.basePath + '/' + newOffset;

        $.ajax({
            url: url,
            method: 'post',
        }).done(function (data) {
            $('#tricks-list').append(data);
        })
    }
}

// Au chargement du DOM, on appelle le script
$(app.init);
