// quand le module JQuery est chargé alors execute cette fonction de callback (grâce au dollar)
$(function() {
    function loadAnimals() {
        console.log("loadAnimals")
        $.getJSON("/api/animals/", function(animals) {
            var message = "we don't kill animals!"
            if(animals.length > 0) {
                message = animals[0];
            }
           $(".intro-heading.text-uppercase").text(message);
        });
    };
    console.log("init load animals")
    setInterval(loadAnimals, 2000);
});