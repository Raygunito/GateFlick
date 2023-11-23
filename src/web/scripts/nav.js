//? Permet de changer la couleur des nav-link en fonction de celui sur lequel on clique
document.addEventListener('DOMContentLoaded', function () {
    var links = document.querySelectorAll('[class$="nav-link"]');

    links.forEach(function (link) {
        link.addEventListener('click', function () {
            links.forEach(function (l) {
                l.classList.remove('active');
            });
            link.classList.add('active');
        });
    });
});
