(function(){
    window.addEventListener('DOMContentLoaded', function (){
        let alertButtons = document.getElementsByClassName('alert-button');
        Array.from(alertButtons).map(function(alertButton) {
            alertButton.addEventListener('click', function (event){
                event.target.parentElement.remove();
            })
        })
    })
})();