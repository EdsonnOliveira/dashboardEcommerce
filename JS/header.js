var closeDashboard = document.querySelector('.menuUser');

closeDashboard.onclick = function() {
    var showMenu = document.querySelector('#showMenu');

    if (showMenu.classList.contains('show')) {
        showMenu.classList.remove('show');
    } else {
        showMenu.classList.add('show');
    }
}