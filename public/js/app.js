//! à factoriser

//input du champ image
let inputImage = document.querySelector('.custom-file-input');

if (inputImage) {
    let labelInputImage = document.querySelector('.custom-file-label');
    //fonction fléchée: déclarée utilisée
    const handleChange = () => {
        labelInputImage.innerHTML = inputImage.files[0].name;
    }
    //appel de l'event
    inputImage.addEventListener('change', handleChange);
}

//regex qui valide la forme de l'email
function validateEmail(email) {
    const regex_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex_email.test(String(email).toLowerCase());
}

//input du champ email
let inputEmail = document.querySelector('#user_email');

const handleChangeEmail = (event) => {
    //cibler la valeur
    let inputCheck = event.target.value;
    //actions en fonction du résultat
    if (validateEmail(inputCheck)) {
        //au moment de l'évènement
        event.target.classList.remove('red_border');
        event.target.classList.add('green_border');
    } else {
        event.target.classList.remove('green_border');
        event.target.classList.add('red_border');
    }
}

//appel de l'event
if (inputEmail) {
    inputEmail.addEventListener('input', handleChangeEmail);
}

//sélection du bouton (sans href)
let buttonDel = document.querySelectorAll('.button_del');

const handleClickDel = (event) => {
    //sélection du second bouton (href)
    event.target.classList.add('button_hide');
    //sélection de la balise
    let buttonDelete = event.target.parentNode.childNodes[5].childNodes[1];
    buttonDelete.classList.remove('button_hide');
    //au bout de 4 secondes, les boutons reviennent à leur état initial
    setTimeout(function() {
        buttonDelete.classList.add('button_hide');
        event.target.classList.remove('button_hide');
    }, 4000);
}

//appel de l'event
buttonDel.forEach((button_NodeList) => {
    button_NodeList.addEventListener('click', handleClickDel);
})