var juste 	= "Vous avez raison !";
var faux 	= "Vous avez tort.";

function answer_no(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 > naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
	document.getElementById('choix').style.display = "block";
	document.getElementById("answer_no").style.backgroundColor = "blue";
	document.getElementById("answer_no").style.color = "white";
	document.getElementById('answer_no').disabled = "disabled";
	document.getElementById('answer_yes').disabled = "disabled";
}

function answer_yes(param1,param2){
	var naiss1 = new Date(param1);
	var naiss2 = new Date(param2);
	var formatNaiss1 = naiss1.getDate() + "/" + (naiss1.getMonth() + 1) + "/" + naiss1.getFullYear();
	var formatNaiss2 = naiss2.getDate() + "/" + (naiss2.getMonth() + 1) + "/" + naiss2.getFullYear();
	(naiss1 < naiss2) 	
		? document.getElementsByClassName("reponse_quiz").item(0).innerHTML = juste + ' ' + formatNaiss1 + " contre " + formatNaiss2
		: document.getElementsByClassName("reponse_quiz").item(0).innerHTML = faux  + ' ' + formatNaiss1 + " contre " + formatNaiss2;
	document.getElementById('choix').style.display = "block";
	document.getElementById("answer_yes").style.backgroundColor = "blue";
	document.getElementById("answer_yes").style.color = "white";
	document.getElementById('answer_no').disabled = 'disabled';
	document.getElementById('answer_yes').disabled = 'disabled';
}

function verif_photo(id,choix_photo,reponse,compteur) {

	document.getElementsByClassName('reponse_quiz').item(0).innerHTML = (choix_photo == id) ? juste + " C'est " + reponse 
																							: faux + " C'est " + reponse;
	document.getElementById("choix").style.display = "block";
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == compteur) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
	
}

function verif_commun(id,choix_acteur,nom) {
	document.getElementsByClassName("reponse_quiz").item(0).innerHTML = (choix_acteur == 1) 
		? juste + " La r&eacute;ponse est " +nom
		: faux  + " La r&eacute;ponse est " +nom;
	document.getElementById("choix").style.display = "block";
	var elt = document.getElementById("question").children;
	var i = 0;
	for (i = 0; i < elt.length; ++i) {
		if (i == id) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
}

function verif_year_disney(id,oneannee,choixannee){
	var juste 	= "Vous avez raison ! La r&eacute;ponse &eacute;tait : ";
	var faux 	= "Vous avez tort. La r&eacute;ponse &eacute;tait : ";
	document.getElementsByClassName("reponse_quiz").item(0).innerHTML = (oneannee == choixannee) ? juste+oneannee : faux+oneannee;
	
	var elt = document.getElementsByClassName("button_annee");
	var i = 0;
	for(i = 0; i < elt.length; ++i) {
		if (i == id) {
			elt.item(i).style.backgroundColor = "blue";
			elt.item(i).style.color = "white";
		}
		elt.item(i).disabled = "disabled";
	}
	document.getElementById("choix").style.display = "block";
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = "./liste_quiz.php";
}
	
