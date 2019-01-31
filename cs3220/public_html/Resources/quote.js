/*eslint-env browser*/
var quotes = [
    'We can only see a short distance ahead, but we can see plenty there that needs to be done.',
    'I propose to consider the question, Can machines think?',
    'A computer would deserve to be called intelligent if it could deceive a human into believing that it was human.',
    'Machines take me by surprise with great frequency.',
    'Mathematical reasoning may be regarded...',
    'We are not interested in the fact that the brain has the consistency of cold porridge.'
]

var author = [
    '-Alan Turing',
    '-Alan Turing',
    '-Alan Turing',
    '-Alan Turing',
    '-Alan Turing',
    '-Alan Turing'
]

function getQuote(){
    var randNum = Math.floor(Math.random()* quotes.length) - 1;
    document.getElementById('quote').innerHTML = quotes[randNum];
    document.getElementById('author').innerHTML = author[randNum];
}