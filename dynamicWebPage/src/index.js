var Chance = require("chance")
var express = require("express")

var app = express()
var chance = new Chance()

app.get('/', function(req, res) {
    res.send(generateAnimals())
});

app.listen(3000, function() {
    console.log("wait HTTP requests on port 3000.")
});

function generateAnimals() {
    var numberOfAnimals = chance.integer({min:2, max:5});
    var animals = [];
    while (animals.length < numberOfAnimals) {
        var newAnimal = chance.animal({type: 'zoo'});
        var isNotYetIn = true;
        for (var i = 0; i < animals.length; i++) {
            if (animals[i] == newAnimal) {
                isNotYetIn = false
            }
        }
        if (isNotYetIn) {
            animals.push(newAnimal)
        } 
    }
    return animals;
};